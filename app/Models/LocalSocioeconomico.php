<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalSocioeconomico extends Model
{
    protected $table = 'locais_socioeconomico';

    protected $primaryKey = 'lse_id';

    public $timestamps = true;

    /** @var list<string> */
    public const FILLABLE_COLUMNS = [
        'fk_local_id',
        'lse_data_entrevista',
        'lse_condicao_casa',
        'lse_posicao_entrevistado',
        'lse_telefone_contato',
        'lse_n_moradores_declarado',
        'lse_renda_formal_informal',
        'lse_principal_fonte_renda',
        'lse_renda_familiar_faixa',
        'lse_qtd_contribuintes',
        'lse_beneficios_sociais',
        'lse_gastos_mensais_faixa',
        'lse_proprietario_nome',
        'lse_proprietario_endereco',
        'lse_proprietario_telefone',
        'lse_uso_imovel',
        'lse_situacao_posse',
        'lse_material_predominante',
        'lse_condicao_edificacao',
        'lse_num_comodos',
        'lse_num_quartos',
        'lse_num_banheiros',
        'lse_area_externa',
        'lse_viz_frente',
        'lse_viz_fundos',
        'lse_viz_direita',
        'lse_viz_esquerda',
        'lse_tipologia',
        'lse_tipo_implantacao',
        'lse_posicao_lote',
        'lse_num_pavimentos',
        'lse_banheiro_dentro',
        'lse_banheiro_fora',
        'lse_banheiro_compartilha',
        'lse_acesso_imovel',
        'lse_entrada_para',
        'lse_area_livre',
        'lse_observacoes_imovel',
        'lse_abastecimento_agua',
        'lse_energia_eletrica',
        'lse_esgoto',
        'lse_coleta_lixo',
        'lse_pavimentacao',
        'lse_situacao_terreno',
        'lse_posse_area',
        'lse_tempo_residencia_texto',
        'lse_data_ocupacao',
        'lse_forma_aquisicao',
        'lse_houve_compra_venda',
        'lse_escritura',
        'lse_situacao_legal_obs',
        'lse_proprietario_anterior_nome',
        'lse_proprietario_anterior_doc',
        'lse_como_ocupou',
        'lse_contrato_promessa',
        'lse_documento_quitado',
        'lse_sabe_local_vendedor',
        'lse_paga_iptu',
        'lse_iptu_desde',
        'lse_local_data_assinatura',
    ];

    protected $fillable = self::FILLABLE_COLUMNS;

    protected function casts(): array
    {
        return [
            'lse_data_entrevista' => 'date',
            'lse_data_ocupacao' => 'date',
            'lse_banheiro_compartilha' => 'boolean',
            'lse_n_moradores_declarado' => 'integer',
            'lse_qtd_contribuintes' => 'integer',
            'lse_num_comodos' => 'integer',
            'lse_num_quartos' => 'integer',
            'lse_num_banheiros' => 'integer',
            'lse_num_pavimentos' => 'integer',
            'lse_banheiro_dentro' => 'integer',
            'lse_banheiro_fora' => 'integer',
        ];
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'fk_local_id', 'loc_id');
    }

    /**
     * @return array<string, string>
     */
    public static function formKeyToColumn(): array
    {
        $map = [];
        foreach (self::FILLABLE_COLUMNS as $col) {
            if ($col === 'fk_local_id') {
                continue;
            }
            $map[str_replace('lse_', '', $col)] = $col;
        }

        return $map;
    }

    /**
     * @param  array<string, mixed>|null  $input
     * @return array<string, mixed>
     */
    public static function attributesFromForm(?array $input): array
    {
        if (! is_array($input)) {
            return [];
        }
        $keyToCol = self::formKeyToColumn();
        $out = [];
        foreach ($keyToCol as $formKey => $col) {
            if (! array_key_exists($formKey, $input)) {
                continue;
            }
            $v = $input[$formKey];
            if ($v === '' || $v === null) {
                $out[$col] = null;

                continue;
            }
            if ($col === 'lse_banheiro_compartilha') {
                if ($v === '' || $v === null) {
                    $out[$col] = null;
                } else {
                    $out[$col] = in_array((string) $v, ['1', 'true', 'sim', 'yes', 'on'], true)
                        || $v === true
                        || $v === 1;
                }

                continue;
            }
            if (in_array($col, [
                'lse_n_moradores_declarado', 'lse_qtd_contribuintes', 'lse_num_comodos',
                'lse_num_quartos', 'lse_num_banheiros', 'lse_num_pavimentos',
                'lse_banheiro_dentro', 'lse_banheiro_fora',
            ], true)) {
                $out[$col] = is_numeric($v) ? (int) $v : null;

                continue;
            }
            $out[$col] = is_string($v) ? trim($v) : $v;
        }

        if (
            array_key_exists('lse_banheiro_dentro', $out)
            || array_key_exists('lse_banheiro_fora', $out)
            || array_key_exists('lse_num_banheiros', $out)
        ) {
            $hasDentro = array_key_exists('lse_banheiro_dentro', $out) && $out['lse_banheiro_dentro'] !== null;
            $hasFora = array_key_exists('lse_banheiro_fora', $out) && $out['lse_banheiro_fora'] !== null;
            if ($hasDentro || $hasFora) {
                $dentro = is_numeric($out['lse_banheiro_dentro'] ?? null) ? (int) $out['lse_banheiro_dentro'] : 0;
                $fora = is_numeric($out['lse_banheiro_fora'] ?? null) ? (int) $out['lse_banheiro_fora'] : 0;
                $out['lse_num_banheiros'] = $dentro + $fora;
            } else {
                $out['lse_num_banheiros'] = null;
            }
        }

        return $out;
    }

    /**
     * @return array<string, mixed>
     */
    public function toFormArray(): array
    {
        $keyToCol = self::formKeyToColumn();
        $out = [];
        foreach ($keyToCol as $formKey => $col) {
            $v = $this->getAttribute($col);
            if ($v instanceof \DateTimeInterface) {
                $out[$formKey] = $v->format('Y-m-d');
            } elseif (is_bool($v)) {
                $out[$formKey] = $v ? '1' : '0';
            } else {
                $out[$formKey] = $v;
            }
        }

        return $out;
    }
}
