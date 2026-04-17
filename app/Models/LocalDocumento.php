<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalDocumento extends Model
{
    protected $table = 'local_documentos';

    protected $fillable = [
        'fk_local_id',
        'path',
        'original_name',
        'mime',
        'size_bytes',
    ];

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'fk_local_id', 'loc_id');
    }
}
