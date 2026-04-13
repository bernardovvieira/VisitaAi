<?php

namespace Tests\Feature;

use App\Models\Doenca;
use App\Models\Local;
use App\Models\Log;
use App\Models\Morador;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SmartSearchCoverageTest extends TestCase
{
    use RefreshDatabase;

    private function gestor(): User
    {
        Local::factory()->create();

        return User::factory()->create([
            'use_nome' => 'Gestor de Teste',
            'use_perfil' => 'gestor',
            'use_aprovado' => 1,
        ]);
    }

    #[Test]
    public function locais_encontram_numero_e_responsavel_ocultos(): void
    {
        $gestor = $this->gestor();

        $localEncontrado = Local::factory()->create([
            'loc_endereco' => 'Rua Central',
            'loc_numero' => '742',
            'loc_bairro' => 'Centro',
            'loc_cidade' => 'Soledade',
            'loc_estado' => 'RS',
            'loc_responsavel_nome' => 'Bernardo Vivian Vieira',
        ]);

        Local::factory()->create([
            'loc_endereco' => 'Rua Longe',
            'loc_numero' => '111',
            'loc_bairro' => 'Bairro Novo',
            'loc_cidade' => 'Soledade',
            'loc_estado' => 'RS',
            'loc_responsavel_nome' => 'Outra Pessoa',
        ]);

        $this->actingAs($gestor)
            ->get(route('gestor.locais.index', ['search' => 'Bernardo Vivian Vieira']))
            ->assertOk()
            ->assertSeeText('Rua Central')
            ->assertDontSeeText('Rua Longe');

        foreach (['ber', 'bernar', 'bernard', 'nardo', 'vivi', 'vivian', 'vieira', 'vie'] as $chunk) {
            $this->actingAs($gestor)
                ->get(route('gestor.locais.index', ['search' => $chunk]))
                ->assertOk()
                ->assertSeeText('Rua Central')
                ->assertDontSeeText('Rua Longe');
        }

        $this->actingAs($gestor)
            ->get(route('gestor.locais.index', ['search' => '742']))
            ->assertOk()
            ->assertSeeText('Rua Central')
            ->assertDontSeeText('Rua Longe');

        $this->assertDatabaseHas('locais', ['loc_id' => $localEncontrado->loc_id]);
    }

    #[Test]
    public function visitas_encontram_dados_relacionados_do_local_e_observacoes(): void
    {
        $gestor = $this->gestor();
        $usuario = User::factory()->create([
            'use_nome' => 'Agente Relacionado',
            'use_email' => 'agente.relacionado@example.com',
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ]);

        $local = Local::factory()->create([
            'loc_endereco' => 'Rua das Flores',
            'loc_numero' => '123',
            'loc_bairro' => 'Centro',
            'loc_cidade' => 'Soledade',
            'loc_estado' => 'RS',
            'loc_responsavel_nome' => 'Pessoa Oculta',
        ]);

        Morador::factory()->create([
            'fk_local_id' => $local->loc_id,
            'mor_nome' => 'Bernardo Vivian Vieira',
            'mor_telefone' => '51999887766',
            'mor_cpf' => '12345678901',
        ]);

        Visita::create([
            'fk_usuario_id' => $usuario->use_id,
            'fk_local_id' => $local->loc_id,
            'vis_data' => '2025-04-13',
            'vis_ciclo' => '04/25',
            'vis_atividade' => '7',
            'vis_observacoes' => 'Atenção ao número 742 e contato oculto.',
            'vis_ocupantes_observacoes' => json_encode([['obs' => 'Bernardo Vivian Vieira com telefone 51999887766']]),
            'vis_pendencias' => false,
            'vis_concluida' => true,
        ]);

        $this->actingAs($gestor)
            ->get(route('gestor.visitas.index', ['busca' => 'Bernardo Vivian Vieira']))
            ->assertOk()
            ->assertSeeText('Rua das Flores')
            ->assertDontSeeText('Rua do Outro Lado');

        foreach (['ber', 'bernar', 'nardo', 'vivi', 'vivian', 'vieira', 'vie'] as $chunk) {
            $this->actingAs($gestor)
                ->get(route('gestor.visitas.index', ['busca' => $chunk]))
                ->assertOk()
                ->assertSeeText('Rua das Flores');
        }

        $this->actingAs($gestor)
            ->get(route('gestor.visitas.index', ['busca' => '742']))
            ->assertOk()
            ->assertSeeText('Rua das Flores');
    }

    #[Test]
    public function ocupantes_encontram_cpf_telefone_e_rotulo_humano(): void
    {
        $gestor = $this->gestor();
        $local = Local::factory()->create([
            'loc_endereco' => 'Rua dos Pinheiros',
            'loc_numero' => '88',
            'loc_bairro' => 'Centro',
            'loc_cidade' => 'Soledade',
            'loc_estado' => 'RS',
        ]);

        $encontrado = Morador::factory()->create([
            'fk_local_id' => $local->loc_id,
            'mor_nome' => 'Bernardo Vivian Vieira',
            'mor_telefone' => '51912345678',
            'mor_cpf' => '98765432100',
            'mor_sexo' => 'prefiro_nao_informar',
            'mor_escolaridade' => 'nao_informado',
            'mor_renda_faixa' => 'nao_informado',
            'mor_cor_raca' => 'nao_informado',
            'mor_situacao_trabalho' => 'nao_informado',
            'mor_estado_civil' => 'nao_informado',
        ]);

        Morador::factory()->create([
            'fk_local_id' => $local->loc_id,
            'mor_nome' => 'Outro Ocupante',
            'mor_telefone' => '51900001111',
            'mor_cpf' => '11122233344',
        ]);

        $this->actingAs($gestor)
            ->get(route('gestor.locais.moradores.index', ['local' => $local, 'q' => '987654321']))
            ->assertOk()
            ->assertSeeText('Bernardo Vivian Vieira')
            ->assertDontSeeText('Outro Ocupante');

        $this->actingAs($gestor)
            ->get(route('gestor.locais.moradores.index', ['local' => $local, 'q' => '519123']))
            ->assertOk()
            ->assertSeeText('Bernardo Vivian Vieira');

        $this->actingAs($gestor)
            ->get(route('gestor.locais.moradores.index', ['local' => $local, 'q' => 'prefiro nao informar']))
            ->assertOk()
            ->assertSeeText('Bernardo Vivian Vieira');

        foreach (['ber', 'bernar', 'nardo', 'vivi', 'vivian', 'vieira', 'vie'] as $chunk) {
            $this->actingAs($gestor)
                ->get(route('gestor.locais.moradores.index', ['local' => $local, 'q' => $chunk]))
                ->assertOk()
                ->assertSeeText('Bernardo Vivian Vieira');
        }

        $this->assertDatabaseHas('moradores', ['mor_id' => $encontrado->mor_id]);
    }

    #[Test]
    public function usuarios_encontram_cpf_fragmentado(): void
    {
        $gestor = $this->gestor();

        User::factory()->create([
            'use_nome' => 'Usuario Secundario',
            'use_cpf' => '11122233344',
            'use_email' => 'secundario@example.com',
            'use_perfil' => 'agente_saude',
            'use_aprovado' => 1,
        ]);

        User::factory()->create([
            'use_nome' => 'José Bernardo Vivian Vieira',
            'use_cpf' => '12345678901',
            'use_email' => 'alvo@example.com',
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ]);

        $this->actingAs($gestor)
            ->get(route('gestor.users.index', ['search' => '456789']))
            ->assertOk()
            ->assertSeeText('José Bernardo Vivian Vieira')
            ->assertDontSeeText('Usuario Secundario');

        foreach (['jose', 'ber', 'bernard', 'nardo', 'vivi', 'vieira'] as $chunk) {
            $this->actingAs($gestor)
                ->get(route('gestor.users.index', ['search' => $chunk]))
                ->assertOk()
                ->assertSeeText('José Bernardo Vivian Vieira');
        }
    }

    #[Test]
    public function logs_encontram_ip_e_agente_relacionado(): void
    {
        $gestor = $this->gestor();
        $usuario = User::factory()->create([
            'use_nome' => 'Usuario Log',
            'use_email' => 'log@example.com',
            'use_perfil' => 'agente_saude',
            'use_aprovado' => 1,
        ]);

        Log::create([
            'log_user_id' => $usuario->use_id,
            'log_acao' => 'create',
            'log_entidade' => 'Visita',
            'log_tipo' => 'info',
            'log_descricao' => 'Cadastro executado para validação de busca.',
            'log_data' => '2025-04-13 10:20:30',
            'log_ip' => '10.8.9.12',
            'log_user_agent' => 'Mozilla/5.0 VisitaAi Test Agent',
        ]);

        $this->actingAs($gestor)
            ->get(route('gestor.logs.index', ['search' => '10.8.9']))
            ->assertOk()
            ->assertSeeText('Cadastro executado para validação de busca.');

        $this->actingAs($gestor)
            ->get(route('gestor.logs.index', ['search' => 'VisitaAi Test Agent']))
            ->assertOk()
            ->assertSeeText('Cadastro executado para validação de busca.');
    }

    #[Test]
    public function doencas_encontram_id_e_conteudo_textual(): void
    {
        $gestor = $this->gestor();

        $doenca = Doenca::create([
            'doe_nome' => 'Febre da Busca',
            'doe_sintomas' => ['Febre alta', 'Dor de cabeça'],
            'doe_transmissao' => ['Contato direto'],
            'doe_medidas_controle' => ['Isolamento'],
        ]);

        $this->actingAs($gestor)
            ->get(route('gestor.doencas.index', ['search' => (string) $doenca->doe_id]))
            ->assertOk()
            ->assertSeeText('Febre da Busca');

        $this->actingAs($gestor)
            ->get(route('gestor.doencas.index', ['search' => 'dor de cabeça']))
            ->assertOk()
            ->assertSeeText('Febre da Busca');
    }
}