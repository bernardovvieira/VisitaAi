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
        'mor_observacao',
    ];

    protected function casts(): array
    {
        return [
            'mor_data_nascimento' => 'date',
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
