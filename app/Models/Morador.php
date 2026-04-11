<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ocupante associado ao imóvel no Visita Aí (vigilância / PNCD).
 *
 * Não representa cadastro e-SUS APS, Cidadão no PEC nem ficha de Território federal.
 * Tabela `moradores` mantém nome legado; ver documentação de complementaridade.
 */
class Morador extends Model
{
    use HasFactory;

    protected $table = 'moradores';

    protected $primaryKey = 'mor_id';

    public $timestamps = true;

    protected $fillable = [
        'fk_local_id',
        'mor_nome',
        'mor_data_nascimento',
        'mor_escolaridade',
        'mor_renda_faixa',
        'mor_cor_raca',
        'mor_situacao_trabalho',
        'mor_sexo',
        'mor_estado_civil',
        'mor_naturalidade',
        'mor_profissao',
        'mor_parentesco',
        'mor_referencia_familiar',
        'mor_telefone',
        'mor_rg_numero',
        'mor_rg_orgao',
        'mor_cpf',
        'mor_documento_pessoal_path',
        'mor_documento_pessoal_nome',
        'mor_documento_pessoal_mime',
        'mor_documento_pessoal_tamanho',
        'mor_tempo_uniao_conjuge',
        'mor_ajuda_compra_imovel',
        'mor_renda_formal_informal',
        'mor_observacao',
    ];

    protected function casts(): array
    {
        return [
            'mor_data_nascimento' => 'date',
            'mor_referencia_familiar' => 'boolean',
        ];
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'fk_local_id', 'loc_id');
    }

    /**
     * Idade em anos completos na data atual; null se data de nascimento ausente.
     */
    public function idadeAnos(): ?int
    {
        if (! $this->mor_data_nascimento instanceof Carbon) {
            return null;
        }

        return $this->mor_data_nascimento->diffInYears(Carbon::now());
    }
}
