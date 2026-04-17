<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\Morador;
use App\Models\MoradorDocumento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MoradorTest extends TestCase
{
    use RefreshDatabase;

    protected function gestorAprovado(): User
    {
        return User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => 1,
        ]);
    }

    #[Test]
    public function gestor_pode_listar_moradores_do_local(): void
    {
        $user = $this->gestorAprovado();
        $local = Local::factory()->create();
        Morador::factory()->count(2)->create(['fk_local_id' => $local->loc_id]);

        $response = $this->actingAs($user)
            ->get(route('gestor.locais.moradores.index', $local));

        $response->assertStatus(200)
            ->assertSeeText(config('visitaai_municipio.ocupantes.titulo_listagem'));
    }

    #[Test]
    public function gestor_pode_cadastrar_morador(): void
    {
        $agente = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ]);

        $local = Local::factory()->create();

        $response = $this->actingAs($agente)
            ->post(route('agente.locais.moradores.store', $local), [
                'mor_nome' => 'Fulano Teste',
                'mor_data_nascimento' => '1990-05-20',
                'mor_escolaridade' => 'medio_completo',
                'mor_renda_faixa' => 'ate_1_sm',
            ]);

        $response->assertRedirect(route('agente.locais.moradores.index', $local));
        $this->assertDatabaseHas('moradores', [
            'fk_local_id' => $local->loc_id,
            'mor_nome' => 'Fulano Teste',
        ]);
    }

    #[Test]
    public function acs_nao_acessa_ocupantes_na_rota_do_gestor_mas_acessa_em_saude(): void
    {
        $acs = User::factory()->create([
            'use_perfil' => 'agente_saude',
            'use_aprovado' => 1,
        ]);
        $local = Local::factory()->create();

        $this->actingAs($acs)
            ->get(route('gestor.locais.moradores.index', $local))
            ->assertForbidden();

        $this->actingAs($acs)
            ->get(route('saude.locais.moradores.index', $local))
            ->assertOk();
    }

    #[Test]
    public function download_de_documento_bloqueia_path_traversal(): void
    {
        $gestor = $this->gestorAprovado();
        $local = Local::factory()->create();
        $morador = Morador::factory()->create([
            'fk_local_id' => $local->loc_id,
        ]);
        $doc = MoradorDocumento::query()->create([
            'fk_morador_id' => $morador->mor_id,
            'path' => '../../../.env',
            'original_name' => 'env.txt',
            'mime' => 'text/plain',
            'size_bytes' => 1,
        ]);

        $this->actingAs($gestor)
            ->get(route('gestor.locais.moradores.documento-pessoal', [$local, $morador, $doc]))
            ->assertNotFound();
    }
}
