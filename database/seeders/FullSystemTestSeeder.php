<?php

namespace Database\Seeders;

use App\Models\Doenca;
use App\Models\Local;
use App\Models\LocalSocioeconomico;
use App\Models\Morador;
use App\Models\Tratamento;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seeder completo para ambiente de testes/demonstração.
 *
 * Objetivo: popular o sistema inteiro sem alterar credenciais de login já existentes.
 * Estratégia: idempotente por contagem/firstOrCreate para permitir múltiplas execuções.
 */
class FullSystemTestSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('pt_BR');

        $users = $this->ensureUsers();
        $doencas = $this->ensureDoencas();
        $locais = $this->ensureLocais($faker, 72);

        $this->ensureSocioeconomico($faker, $locais);
        $this->ensureMoradores($faker, $locais);
        $this->ensureVisitas($faker, $locais, $users['agentes'], $doencas);
    }

    /**
     * @return array{gestor: User, agentes: \Illuminate\Support\Collection<int, User>}
     */
    private function ensureUsers(): array
    {
        $gestor = User::query()->where('use_perfil', 'gestor')->first();
        if (! $gestor) {
            $gestor = User::query()->firstOrCreate(
                ['use_email' => 'gestor@exemplo.com'],
                [
                    'use_nome' => 'Gestor Demo',
                    'use_cpf' => '444.444.444-44',
                    'use_senha' => bcrypt('Senha123!'),
                    'email_verified_at' => now(),
                    'use_perfil' => 'gestor',
                    'use_aprovado' => true,
                    'use_data_criacao' => now(),
                ]
            );
        }

        $agentesSeed = [
            [
                'use_nome' => 'Agente Endemias Demo',
                'use_cpf' => '111.111.111-11',
                'use_email' => 'agente1@exemplo.com',
                'use_perfil' => 'agente_endemias',
                'use_aprovado' => true,
            ],
            [
                'use_nome' => 'Agente Saúde Demo',
                'use_cpf' => '222.222.222-22',
                'use_email' => 'agente2@exemplo.com',
                'use_perfil' => 'agente_saude',
                'use_aprovado' => true,
            ],
            [
                'use_nome' => 'Agente Pendente Demo',
                'use_cpf' => '333.333.333-33',
                'use_email' => 'agente3@exemplo.com',
                'use_perfil' => 'agente_endemias',
                'use_aprovado' => false,
            ],
        ];

        foreach ($agentesSeed as $seed) {
            User::query()->firstOrCreate(
                ['use_email' => $seed['use_email']],
                [
                    'use_nome' => $seed['use_nome'],
                    'use_cpf' => $seed['use_cpf'],
                    'use_senha' => bcrypt('Senha123!'),
                    'email_verified_at' => now(),
                    'use_perfil' => $seed['use_perfil'],
                    'use_aprovado' => $seed['use_aprovado'],
                    'use_data_criacao' => now(),
                    'fk_gestor_id' => $gestor->use_id,
                ]
            );
        }

        $agentes = User::query()
            ->whereIn('use_perfil', ['agente_endemias', 'agente_saude'])
            ->where('use_aprovado', true)
            ->get();

        return [
            'gestor' => $gestor,
            'agentes' => $agentes,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, Doenca>
     */
    private function ensureDoencas()
    {
        $doencasSeed = [
            ['nome' => 'Dengue', 'sintomas' => ['Febre', 'Dor no corpo', 'Dor de cabeça']],
            ['nome' => 'Zika', 'sintomas' => ['Febre baixa', 'Exantema', 'Conjuntivite']],
            ['nome' => 'Chikungunya', 'sintomas' => ['Febre alta', 'Dor articular intensa']],
            ['nome' => 'Leptospirose', 'sintomas' => ['Febre', 'Dor muscular', 'Icterícia']],
            ['nome' => 'Febre Amarela', 'sintomas' => ['Febre', 'Calafrios', 'Mialgia']],
        ];

        foreach ($doencasSeed as $seed) {
            Doenca::query()->firstOrCreate(
                ['doe_nome' => $seed['nome']],
                [
                    'doe_sintomas' => $seed['sintomas'],
                    'doe_transmissao' => ['Vetor biológico'],
                    'doe_medidas_controle' => ['Eliminação de criadouros', 'Educação em saúde'],
                ]
            );
        }

        return Doenca::query()->get();
    }

    /**
     * @return \Illuminate\Support\Collection<int, Local>
     */
    private function ensureLocais($faker, int $target)
    {
        $existing = Local::query()->count();
        $base = Local::query()->first();

        $cidade = $base?->loc_cidade ?: 'Soledade';
        $estado = $base?->loc_estado ?: 'RS';
        $pais = $base?->loc_pais ?: 'Brasil';
        $baseLat = (float) ($base?->loc_latitude ?: -28.8353);
        $baseLng = (float) ($base?->loc_longitude ?: -52.5081);

        $bairros = [
            'Centro', 'Farroupilha', 'Botucarai', 'Missões', 'Expedicionário', 'Fontes', 'Ipiranga', 'Nova Soledade',
        ];

        for ($i = $existing; $i < $target; $i++) {
            $codigoUnico = $this->nextCodigoUnico();
            $bairro = $bairros[array_rand($bairros)];

            Local::query()->create([
                'loc_cep' => '99300-000',
                'loc_tipo' => ['R', 'C', 'T'][array_rand(['R', 'C', 'T'])],
                'loc_zona' => ['U', 'R'][array_rand(['U', 'R'])],
                'loc_quarteirao' => (string) random_int(1, 35),
                'loc_complemento' => random_int(0, 100) <= 35 ? $faker->secondaryAddress() : null,
                'loc_categoria' => 'BIR',
                'loc_sequencia' => random_int(1, 20),
                'loc_lado' => random_int(1, 4),
                'loc_codigo' => str_pad((string) random_int(1, 999), 5, '0', STR_PAD_LEFT),
                'loc_endereco' => $faker->streetName(),
                'loc_numero' => random_int(1, 2500),
                'loc_bairro' => $bairro,
                'loc_cidade' => $cidade,
                'loc_estado' => $estado,
                'loc_pais' => $pais,
                'loc_latitude' => number_format($baseLat + mt_rand(-700, 700) / 10000, 7, '.', ''),
                'loc_longitude' => number_format($baseLng + mt_rand(-700, 700) / 10000, 7, '.', ''),
                'loc_codigo_unico' => $codigoUnico,
                'loc_responsavel_nome' => random_int(0, 100) <= 75 ? $faker->name() : null,
            ]);
        }

        return Local::query()->get();
    }

    private function ensureSocioeconomico($faker, $locais): void
    {
        $condCasa = array_keys(config('visitaai_socioeconomico.condicao_casa_opcoes', []));
        $posicao = array_keys(config('visitaai_socioeconomico.posicao_entrevistado_opcoes', []));
        $rendaFam = array_keys(config('visitaai_municipio.renda_faixa_opcoes', []));
        $rendaTipo = array_keys(config('visitaai_socioeconomico.renda_formal_informal_opcoes', []));
        $situacaoPosse = array_keys(config('visitaai_socioeconomico.situacao_posse_opcoes', []));

        foreach ($locais as $local) {
            if (LocalSocioeconomico::query()->where('fk_local_id', $local->loc_id)->exists()) {
                continue;
            }
            if (random_int(0, 100) > 82) {
                continue;
            }

            LocalSocioeconomico::query()->create([
                'fk_local_id' => $local->loc_id,
                'lse_data_entrevista' => Carbon::now()->subDays(random_int(1, 240))->toDateString(),
                'lse_condicao_casa' => $condCasa[array_rand($condCasa)] ?? 'nao_informado',
                'lse_posicao_entrevistado' => $posicao[array_rand($posicao)] ?? 'nao_informado',
                'lse_telefone_contato' => $faker->numerify('(54) 9####-####'),
                'lse_n_moradores_declarado' => random_int(1, 7),
                'lse_renda_formal_informal' => $rendaTipo[array_rand($rendaTipo)] ?? 'nao_informado',
                'lse_principal_fonte_renda' => $faker->randomElement(['Assalariado', 'Autônomo', 'Benefício social', 'Aposentadoria']),
                'lse_renda_familiar_faixa' => $rendaFam[array_rand($rendaFam)] ?? 'nao_informado',
                'lse_qtd_contribuintes' => random_int(1, 3),
                'lse_beneficios_sociais' => $faker->randomElement(['Bolsa Família', 'BPC', 'Nenhum', 'Auxílio eventual']),
                'lse_situacao_posse' => $situacaoPosse[array_rand($situacaoPosse)] ?? 'nao_informado',
                'lse_num_comodos' => random_int(3, 9),
                'lse_num_quartos' => random_int(1, 4),
                'lse_num_banheiros' => random_int(1, 3),
                'lse_observacoes_imovel' => random_int(0, 100) < 20 ? $faker->sentence() : null,
            ]);
        }
    }

    private function ensureMoradores($faker, $locais): void
    {
        $esc = array_keys(config('visitaai_municipio.escolaridade_opcoes', []));
        $renda = array_keys(config('visitaai_municipio.renda_faixa_opcoes', []));
        $cor = array_keys(config('visitaai_municipio.cor_raca_opcoes', []));
        $trab = array_keys(config('visitaai_municipio.situacao_trabalho_opcoes', []));
        $sexo = array_keys(config('visitaai_socioeconomico.sexo_opcoes', []));
        $ec = array_keys(config('visitaai_socioeconomico.estado_civil_opcoes', []));
        $par = array_keys(config('visitaai_socioeconomico.parentesco_opcoes', []));
        $rfi = array_keys(config('visitaai_socioeconomico.renda_formal_informal_opcoes', []));

        foreach ($locais as $local) {
            $current = Morador::query()->where('fk_local_id', $local->loc_id)->count();
            if ($current >= 4) {
                continue;
            }

            $toCreate = random_int(max(1, 4 - $current), 7 - $current);
            for ($i = 0; $i < $toCreate; $i++) {
                Morador::query()->create([
                    'fk_local_id' => $local->loc_id,
                    'mor_nome' => $faker->name(),
                    'mor_data_nascimento' => Carbon::now()->subYears(random_int(1, 84))->subDays(random_int(0, 364))->toDateString(),
                    'mor_escolaridade' => $esc[array_rand($esc)] ?? 'nao_informado',
                    'mor_renda_faixa' => $renda[array_rand($renda)] ?? 'nao_informado',
                    'mor_cor_raca' => $cor[array_rand($cor)] ?? 'nao_informado',
                    'mor_situacao_trabalho' => $trab[array_rand($trab)] ?? 'nao_informado',
                    'mor_sexo' => $sexo[array_rand($sexo)] ?? 'nao_informado',
                    'mor_estado_civil' => $ec[array_rand($ec)] ?? 'nao_informado',
                    'mor_parentesco' => $par[array_rand($par)] ?? 'nao_informado',
                    'mor_referencia_familiar' => false,
                    'mor_telefone' => random_int(0, 100) < 60 ? $faker->numerify('(54) 9####-####') : null,
                    'mor_profissao' => random_int(0, 100) < 65 ? $faker->jobTitle() : null,
                    'mor_naturalidade' => random_int(0, 100) < 65 ? $faker->city() : null,
                    'mor_renda_formal_informal' => $rfi[array_rand($rfi)] ?? 'nao_informado',
                    'mor_observacao' => random_int(0, 100) < 18 ? $faker->sentence() : null,
                ]);
            }

            $moradorRef = Morador::query()->where('fk_local_id', $local->loc_id)->inRandomOrder()->first();
            if ($moradorRef) {
                Morador::query()->where('fk_local_id', $local->loc_id)->update(['mor_referencia_familiar' => false]);
                $moradorRef->update(['mor_referencia_familiar' => true]);
            }
        }
    }

    private function ensureVisitas($faker, $locais, $agentes, $doencas): void
    {
        if ($agentes->isEmpty() || $doencas->isEmpty()) {
            return;
        }

        foreach ($locais as $local) {
            $existing = Visita::query()->where('fk_local_id', $local->loc_id)->count();
            if ($existing >= 3) {
                continue;
            }

            $moradoresLocal = Morador::query()->where('fk_local_id', $local->loc_id)->get();
            $toCreate = random_int(max(1, 3 - $existing), 5 - $existing);

            for ($i = 0; $i < $toCreate; $i++) {
                $agente = $agentes->random();
                $pendencia = random_int(0, 100) < 28;
                $coleta = random_int(0, 100) < 35;
                $data = Carbon::now()->subDays(random_int(0, 220));

                $ocupObs = [];
                foreach ($moradoresLocal->random(min($moradoresLocal->count(), random_int(0, 2))) as $m) {
                    $ocupObs[(string) $m->mor_id] = $faker->sentence();
                }

                $visita = Visita::query()->create([
                    'fk_usuario_id' => $agente->use_id,
                    'fk_local_id' => $local->loc_id,
                    'vis_data' => $data->toDateString(),
                    'vis_ciclo' => $data->format('m/y'),
                    'vis_atividade' => (string) random_int(1, 8),
                    'vis_visita_tipo' => random_int(0, 100) < 20 ? 'R' : 'N',
                    'vis_pendencias' => $pendencia,
                    'vis_coleta_amostra' => $coleta,
                    'vis_qtd_tubitos' => $coleta ? random_int(1, 6) : null,
                    'vis_amos_inicial' => $coleta ? random_int(80, 180) : null,
                    'vis_amos_final' => $coleta ? random_int(181, 260) : null,
                    'vis_depositos_eliminados' => random_int(0, 8),
                    'vis_imoveis_tratados' => random_int(0, 3),
                    'vis_observacoes' => $faker->sentence(10),
                    'vis_ocupantes_observacoes' => $ocupObs,
                    'vis_concluida' => ! $pendencia,
                    'insp_a1' => random_int(0, 4),
                    'insp_a2' => random_int(0, 4),
                    'insp_b' => random_int(0, 4),
                    'insp_c' => random_int(0, 4),
                    'insp_d1' => random_int(0, 4),
                    'insp_d2' => random_int(0, 4),
                    'insp_e' => random_int(0, 4),
                ]);

                $doencaIds = $doencas->random(random_int(1, min(3, $doencas->count())))->pluck('doe_id')->all();
                $visita->doencas()->syncWithoutDetaching($doencaIds);

                if (random_int(0, 100) < 45) {
                    Tratamento::query()->create([
                        'fk_visita_id' => $visita->vis_id,
                        'trat_tipo' => 'Larvicida',
                        'trat_forma' => random_int(0, 1) ? 'Focal' : 'Perifocal',
                        'linha' => random_int(1, 4),
                        'qtd_gramas' => random_int(1, 10),
                        'qtd_depositos_tratados' => random_int(1, 6),
                    ]);
                }
            }
        }
    }

    private function nextCodigoUnico(): string
    {
        do {
            $codigo = (string) random_int(10000000, 99999999);
        } while (Local::query()->where('loc_codigo_unico', $codigo)->exists());

        return $codigo;
    }
}
