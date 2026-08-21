<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Espacio extends Model
{
    protected $table = 'espacio';
    protected $primaryKey = 'SERIAL_ESP';

    protected $fillable = [
        'NOMBRE_ESP', 'DESCRIPCION_ESP', 'UBICACION_ESP', 'CAPACIDAD_ESP',
        'SERIAL_USR_ENCARGADO', 'ESTADO_ESP',
    ];

    public function encargado()
    {
        return $this->belongsTo(UsuarioInstitucional::class, 'SERIAL_USR_ENCARGADO', 'SERIAL_USR');
    }

    public function reservas()
    {
        return $this->hasMany(ReservaEspacio::class, 'SERIAL_ESP', 'SERIAL_ESP');
    }
}
