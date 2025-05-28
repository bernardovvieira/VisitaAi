<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tratamento extends Model
{
    use HasFactory;

    protected $table = 'tratamentos';

    protected $fillable = [
        'fk_visita_id',
        'trat_tipo',
        'trat_forma',
        'linha',
        'produto',
        'qtd_gramas',
        'qtd_depositos_tratados',
        'qtd_cargas',
    ];

    public function visita()
    {
        return $this->belongsTo(Visita::class, 'fk_visita_id');
    }
}