<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoradorDocumento extends Model
{
    protected $table = 'morador_documentos';

    protected $fillable = [
        'fk_morador_id',
        'path',
        'original_name',
        'mime',
        'size_bytes',
    ];

    public function morador(): BelongsTo
    {
        return $this->belongsTo(Morador::class, 'fk_morador_id', 'mor_id');
    }
}
