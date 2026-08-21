<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivoRequerimiento extends Model
{
    protected $table = 'archivo_requerimiento';

    protected $primaryKey = 'SERIAL_AREQ';

    protected $fillable = [
        'SERIAL_REQ',
        'SERIAL_USR',
        'NOMBRE_AREQ',
        'RUTA_AREQ',
        'TIPO_AREQ',
        'FECHA_HORA_AREQ',
        'ESTADO_AREQ',
    ];

    protected $casts = [
        'FECHA_HORA_AREQ' => 'datetime',
    ];

    public function requerimiento()
    {
        return $this->belongsTo(
            Requerimiento::class,
            'SERIAL_REQ',
            'SERIAL_REQ'
        );
    }
}