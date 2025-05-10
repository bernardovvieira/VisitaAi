<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doenca extends Model
{
    use HasFactory;
    
    protected $table = 'doencas';
    protected $primaryKey = 'doe_id';
    public $timestamps = true;

    protected $fillable = [
        'doe_nome',
        'doe_sintomas',
        'doe_transmissao',
        'doe_medidas_controle',
    ];

    protected $casts = [
        'doe_sintomas'          => 'array',
        'doe_transmissao'       => 'array',
        'doe_medidas_controle'  => 'array',
    ];
}
