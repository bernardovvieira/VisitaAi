<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Monitorada extends Model
{
    use HasFactory;

    protected $table = 'monitoradas';
    protected $primaryKey = 'mon_id';
    public $timestamps = true;

    protected $fillable = [
        'fk_visita_id',
        'fk_doenca_id',
    ];

    public function visita()
    {
        return $this->belongsTo(Visita::class, 'fk_visita_id');
    }

    public function doenca()
    {
        return $this->belongsTo(Doenca::class, 'fk_doenca_id');
    }
}