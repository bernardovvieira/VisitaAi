<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Visita extends Model
{
    use HasFactory;

    protected $table = 'visitas';
    protected $primaryKey = 'vis_id';
    public $timestamps = true;

    protected $fillable = [
        'vis_data',
        'vis_observacoes',
        'fk_local_id',
        'fk_usuario_id',
        'fk_doenca_id',
    ];

    public function local()
    {
        return $this->belongsTo(Local::class, 'fk_local_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_usuario_id');
    }

    public function doenca()
    {
        return $this->belongsTo(Doenca::class, 'fk_doenca_id');
    }

    public function monitoradas()
    {
        return $this->hasMany(Monitorada::class, 'fk_visita_id');
    }
}
