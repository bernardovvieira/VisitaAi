<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    ];

    public function visitas()
    {
        return $this->hasMany(Visita::class, 'fk_local_id');
    }
}