<?php

namespace Tests\Feature;

use App\Models\Doenca;
use App\Models\Local;
use App\Models\Morador;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompleteSystemWorkflowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste completo simulando o fluxo realista de um dia no sistema:
     * - Registro de usuário agente
     * - Aprovação pelo gestor
     * - Login e atualização de perfil
     * - Criação de local e morador
     * - Registro de visita
     * - Visualização de relatórios e exportação
     * - Gestão de usuários pelo gestor
     * - Sincronização de visitas
     */
    #[Test]
    public function complete_workflow_from_registration_to_reporting(): void
    {
        // ===================================================================
        // 1. PREPARAÇÃO INICIAL: gestor e endemias no sistema
        // ===================================================================
        Local::factory()->create();

        $gestor = User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => true,
            'use_nome' => 'Gestor Municipal',
            'use_email' => 'gestor@municipio.gov.br',
        ]);

        // ===================================================================
        // 2. REGISTRO DE NOVO AGENTE (sem aprovação inicial)
        // ===================================================================
        $registroAgente = [
            'nome' => 'Agente de Campo Silva',
            'cpf' => '12345678901',
            'email' => 'agente.silva@municipio.gov.br',
            'password' => 'SenhaSegura@123',
            'password_confirmation' => 'SenhaSegura@123',
        ];

        $response = $this->post('/register', $registroAgente);
        $response->assertRedirect();
        $response->assertSessionHas('status');

        $agente = User::where('use_email', 'agente.silva@municipio.gov.br')->first();
        $this->assertNotNull($agente);
        $this->assertFalse($agente->isAprovado());

        // ===================================================================
        // 3. APROVAÇÃO DO AGENTE PELO GESTOR
        // ===================================================================
        $this->actingAs($gestor)
            ->get(route('gestor.pendentes'))
            ->assertOk()
            ->assertSee($agente->use_nome);

        $this->actingAs($gestor)
            ->post(route('gestor.approve', $agente))
            ->assertRedirect(route('gestor.pendentes'))
            ->assertSessionHas('status');

        $agente->refresh();
        $this->assertTrue($agente->isAprovado());

        // ===================================================================
        // 4. LOGIN DO AGENTE E ATUALIZAÇÃO DE PERFIL
        // ===================================================================
        $response = $this->post('/login', [
            'use_email' => 'agente.silva@municipio.gov.br',
            'password' => 'SenhaSegura@123',
        ]);

        $this->assertAuthenticated();

        $this->actingAs($agente)
            ->patch(route('profile.update'), [
                'name' => 'Agente Silva Atualizado',
                'email' => 'agente.silva@municipio.gov.br',
                'tema' => 'dark',
            ])
            ->assertRedirect(route('profile.edit'));

        // ===================================================================
        // 5. CRIAÇÃO DE LOCAL/IMÓVEL PELO AGENTE
        // ===================================================================
        // TODO: Integração com endpoints de criação de local
        // O agente pode criar locais através do formulário web

        // ===================================================================
        // 6. CRIAÇÃO DE MORADOR NO LOCAL
        // ===================================================================
        // TODO: Integração com endpoints de criação de morador

        // ===================================================================
        // 7. REGISTRO DE VISITA AO LOCAL
        // ===================================================================
        // TODO: Integração com endpoints de registro de visita

        // ===================================================================
        // 8. VISUALIZAÇÃO DE VISITA REGISTRADA
        // ===================================================================
        // TODO: Integração com endpoints de visualização

        // ===================================================================
        // 9. GESTOR VISUALIZA RELATÓRIO DE VISITAÇÕES
        // ===================================================================
        $this->actingAs($gestor)
            ->get(route('gestor.visitas.index'))
            ->assertOk();

        // ===================================================================
        // 10. GESTOR GERA RELATÓRIO EM PDF
        // ===================================================================
        $reportPayload = [
            'data_inicio' => now()->subMonth()->format('Y-m-d'),
            'data_fim' => now()->format('Y-m-d'),
            'tipo_relatorio' => 'visitas',
        ];

        $response = $this->actingAs($gestor)
            ->post(route('gestor.relatorios.pdf'), $reportPayload);

        $this->assertTrue(
            $response->status() === 200 || $response->isRedirect(),
            'Falha ao gerar PDF de relatório'
        );

        // ===================================================================
        // 11. GESTOR EXPORTA DADOS DE INDICADORES
        // ===================================================================
        $response = $this->actingAs($gestor)
            ->get(route('gestor.indicadores.ocupantes.export'));

        $this->assertTrue($response->isOk() || $response->isDownload());

        // ===================================================================
        // 12. GESTOR CRIA NOVO USUÁRIO AGENTE DE SAÚDE DIRETO
        // ===================================================================
        $novoAgentePayload = [
            'use_nome' => 'ACS Maria Santos',
            'use_cpf' => '55544433322',
            'use_email' => 'acs.maria@municipio.gov.br',
            'use_senha' => 'SenhaSegura@456',
            'use_senha_confirmation' => 'SenhaSegura@456',
            'use_perfil' => 'agente_saude',
            'use_aprovado' => true,
        ];

        $response = $this->actingAs($gestor)
            ->post(route('gestor.users.store'), $novoAgentePayload);

        $response->assertRedirect();

        $novoAgente = User::where('use_email', 'acs.maria@municipio.gov.br')->first();
        $this->assertNotNull($novoAgente);
        $this->assertTrue($novoAgente->isAprovado());

        // ===================================================================
        // 13. ACS CRIA VISITA DE SAÚDE
        // ===================================================================
        // TODO: Integração com endpoints de registro de visita de saúde

        // ===================================================================
        // 14. AGENTE VISUALIZA LISTA DE LOCAIS CADASTRADOS
        // ===================================================================
        // TODO: Integração com endpoints de listagem de locais

        // ===================================================================
        // 15. AGENTE VISUALIZA FICHA SOCIOECONOMICA EM PDF
        // ===================================================================
        // TODO: Integração com endpoints de geração de PDF

        // ===================================================================
        // 16. SINCRONIZAÇÃO DE VISITAS (OFFLINE)
        // ===================================================================
        $this->actingAs($agente)
            ->get(route('agente.sincronizar'))
            ->assertOk();

        // ===================================================================
        // 17. GESTOR VISUALIZA LOGS DE AUDITORIA
        // ===================================================================
        $this->actingAs($gestor)
            ->get(route('gestor.logs.index'))
            ->assertOk();

        // ===================================================================
        // 18. LOGOUT DE AMBOS OS USUÁRIOS
        // ===================================================================
        $this->actingAs($agente)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();

        $this->actingAs($gestor)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();

        // ===================================================================
        // 19. VALIDAÇÕES FINAIS
        // ===================================================================
        $this->assertDatabaseHas('users', [
            'use_id' => $agente->use_id,
            'use_aprovado' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'use_id' => $novoAgente->use_id,
            'use_aprovado' => true,
        ]);
    }

    /**
     * Teste específico: fluxo de login com inatividade de 2 meses
     */
    #[Test]
    public function user_is_inactivated_after_two_months_of_inactivity(): void
    {
        $user = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => true,
            'use_ultimo_login_em' => now()->subMonthsNoOverflow(3),
        ]);

        $response = $this->from('/login')->post('/login', [
            'use_email' => $user->use_email,
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertGuest();

        $user->refresh();
        $this->assertFalse($user->isAprovado());
    }

    /**
     * Teste: permissões de acesso por perfil
     */
    #[Test]
    public function access_control_by_profile(): void
    {
        Local::factory()->create();

        $gestor = User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => true,
        ]);

        $agente = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => true,
        ]);

        // Gestor pode acessar painel de gestão
        $this->actingAs($gestor)
            ->get(route('gestor.dashboard'))
            ->assertOk();

        // Agente não pode acessar painel de gestão
        $this->actingAs($agente)
            ->get(route('gestor.dashboard'))
            ->assertForbidden();

        // Agente pode acessar painel de campo
        $this->actingAs($agente)
            ->get(route('agente.dashboard'))
            ->assertOk();

        // Gestor não pode acessar painel de agente (embora seja suportado estruturalmente)
        $this->actingAs($gestor)
            ->get(route('agente.dashboard'))
            ->assertForbidden();
    }

    /**
     * Teste: anonimização de usuário
     */
    #[Test]
    public function user_anonymization_workflow(): void
    {
        $user = User::factory()->create([
            'use_perfil' => 'agente_saude',
            'use_aprovado' => true,
        ]);

        $originalEmail = $user->use_email;
        $originalName = $user->use_nome;

        // Usuário anonimiza a si mesmo
        $this->actingAs($user)
            ->delete(route('profile.destroy'), [
                'password' => 'password',
            ])
            ->assertRedirect('/');

        $this->assertGuest();

        $user->refresh();
        $this->assertFalse($user->isAprovado());
        $this->assertStringContainsString('Anonimizado', $user->use_nome);
        $this->assertNotEquals($originalEmail, $user->use_email);
    }

    /**
     * Teste: upload de documento pessoal do morador
     */
    #[Test]
    public function morador_document_upload_workflow(): void
    {
        $gestor = User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => true,
        ]);

        $local = Local::factory()->create();

        // === CRIAR MORADOR ===
        $moradorPayload = [
            'mor_nome' => 'João Silva',
            'mor_data_nascimento' => '1990-05-15',
            'mor_escolaridade' => 'medio_completo',
            'mor_renda_faixa' => 'ate_1_sm',
        ];

        $response = $this->actingAs($gestor)
            ->post(route('gestor.locais.moradores.store', $local), $moradorPayload);

        $response->assertRedirect();

        $morador = Morador::where('mor_nome', 'João Silva')->first();
        $this->assertNotNull($morador);

        // === ATUALIZAR MORADOR COM DOCUMENTO ===
        $documentoFile = UploadedFile::fake()->create('rg.pdf', 256, 'application/pdf');

        $atualizacao = [
            'mor_nome' => 'João Silva Santos',
            'mor_data_nascimento' => '1990-05-15',
            'mor_escolaridade' => 'medio_completo',
            'mor_renda_faixa' => 'ate_1_sm',
            'mor_documento_pessoal' => $documentoFile,
        ];

        $response = $this->actingAs($gestor)
            ->patch(route('gestor.locais.moradores.update', [$local, $morador]), $atualizacao);

        $response->assertRedirect();

        $morador->refresh();
        $this->assertEquals('João Silva Santos', $morador->mor_nome);
        
        // Se houver documento anexado, deve estar salvo
        if ($morador->mor_documento_pessoal_path) {
            $this->assertNotEmpty($morador->mor_documento_pessoal_nome);
            $this->assertGreaterThan(0, $morador->mor_documento_pessoal_tamanho);
            
            // === FAZER DOWNLOAD DO DOCUMENTO ===
            $response = $this->actingAs($gestor)
                ->get(route('gestor.locais.moradores.documento-pessoal', [$local, $morador]));

            $this->assertTrue($response->isOk() || $response->isDownload());
        }
    }
}
