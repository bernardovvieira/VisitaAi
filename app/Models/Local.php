<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $table = 'locais';

    protected $primaryKey = 'loc_id';

    public $timestamps = true;

    protected $fillable = [
        'loc_cep',
        'loc_tipo',
        'loc_zona',
        'loc_quarteirao',
        'loc_sequencia',
        'loc_complemento',
        'loc_lado',
        'loc_endereco',
        'loc_numero',
        'loc_bairro',
        'loc_cidade',
        'loc_estado',
        'loc_pais',
        'loc_categoria',
        'loc_latitude',
        'loc_longitude',
        'loc_codigo',
        'loc_codigo_unico',
        'loc_responsavel_nome',
        'loc_documento_posse_path',
        'loc_documento_posse_nome',
        'loc_documento_posse_mime',
        'loc_documento_posse_tamanho',
    ];

    public function visitas()
    {
        return $this->hasMany(Visita::class, 'fk_local_id');
    }

    public function moradores()
    {
        return $this->hasMany(Morador::class, 'fk_local_id');
    }

    public function socioeconomico()
    {
        return $this->hasOne(LocalSocioeconomico::class, 'fk_local_id', 'loc_id');
    }

    public function documentosPosse()
    {
        return $this->hasMany(LocalDocumento::class, 'fk_local_id', 'loc_id')->orderBy('id');
    }

    /**
     * Indica se este é o local primário (primeiro cadastrado).
     * Não pode ser editado nem excluído pela UI, apenas por suporte técnico.
     */
    public function isPrimary(): bool
    {
        $minId = static::min('loc_id');

        return $minId !== null && (int) $this->loc_id === (int) $minId;
    }
}
