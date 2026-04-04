<?php

namespace App\Models;

use App\Casts\FlexibleStringOrJsonArray;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'doe_sintomas' => FlexibleStringOrJsonArray::class,
        'doe_transmissao' => FlexibleStringOrJsonArray::class,
        'doe_medidas_controle' => FlexibleStringOrJsonArray::class,
    ];
}
