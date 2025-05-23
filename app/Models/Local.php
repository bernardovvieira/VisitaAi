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
        'loc_endereco',
        'loc_bairro',
        'loc_coordenadas',
        'loc_codigo_unico',
    ];

    // Accessors
    public function getLatitudeAttribute()
    {
        return explode(',', $this->loc_coordenadas)[0] ?? null;
    }

    public function getLongitudeAttribute()
    {
        return explode(',', $this->loc_coordenadas)[1] ?? null;
    }

    // Mutators
    public function setLatitudeAttribute($value)
    {
        $longitude = $this->longitude ?? '';
        $this->attributes['loc_coordenadas'] = "{$value},{$longitude}";
    }

    public function setLongitudeAttribute($value)
    {
        $latitude = $this->latitude ?? '';
        $this->attributes['loc_coordenadas'] = "{$latitude},{$value}";
    }

    public function visitas()
    {
        return $this->hasMany(Visita::class, 'fk_local_id');
    }
}