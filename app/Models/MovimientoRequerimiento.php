<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoRequerimiento extends Model
{
    protected $table = 'movimiento_requerimiento';

    protected $primaryKey = 'SERIAL_MOV';

    protected $fillable = [
        'SERIAL_REQ',
        'SERIAL_USR',
        'ACCION_MOV',
        'SERIAL_USR_DESTINO',
        'SERIAL_DEP_DESTINO',
        'ESTADO_ANTERIOR_MOV',
        'ESTADO_NUEVO_MOV',
        'OBSERVACION_MOV',
        'FECHA_HORA_MOV',
    ];

    protected $casts = [
        'FECHA_HORA_MOV' => 'datetime',
    ];

    public function requerimiento()
    {
        return $this->belongsTo(
            Requerimiento::class,
            'SERIAL_REQ',
            'SERIAL_REQ'
        );
    }

    public function departamentoDestino()
    {
        return $this->belongsTo(
            DepartamentoInstitucional::class,
            'SERIAL_DEP_DESTINO',
            'SERIAL_DEP'
        );
    }

    public function usuario()
    {
        return $this->belongsTo(
            UsuarioInstitucional::class,
            'SERIAL_USR',
            'SERIAL_USR'
        );
    }
}