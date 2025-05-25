<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'log_user_id',
        'log_acao',
        'log_entidade',
        'log_tipo',
        'log_descricao',
        'log_data',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'log_user_id', 'use_id');
    }
}
