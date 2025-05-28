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
        'vis_tipo',
        'vis_ciclo',
        'vis_atividade',
        'vis_visita_tipo',
        'vis_pendencias',
        'vis_insp_inicial',
        'vis_insp_final',
        'vis_coleta_amostra',
        'vis_qtd_tubitos',
        'vis_imoveis_tratados',
        'vis_depositos_eliminados',
        'vis_observacoes',
        'vis_concluida',
        'fk_local_id',
        'fk_usuario_id',

        // depósitos inspecionados
        'insp_a1', 'insp_a2', 'insp_b', 'insp_c', 'insp_d1', 'insp_d2', 'insp_e',
    ];

    protected $casts = [
        'vis_pendencias' => 'array',
        'vis_coleta_amostra' => 'boolean',
        'vis_concluida' => 'boolean',
    ];

    public function local()
    {
        return $this->belongsTo(Local::class, 'fk_local_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_usuario_id');
    }

    public function agente()
    {
        return $this->usuario(); 
    }

    public function doencas()
    {
        return $this->belongsToMany(Doenca::class, 'monitoradas', 'fk_visita_id', 'fk_doenca_id');
    }

    public function monitoradas()
    {
        return $this->hasMany(Monitorada::class, 'fk_visita_id');
    }

    public function tratamentos()
    {
        return $this->hasMany(Tratamento::class, 'fk_visita_id');
    }
}