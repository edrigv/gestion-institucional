<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoReserva extends Model
{
    protected $table = 'movimiento_reserva';
    protected $primaryKey = 'SERIAL_MRES';

    protected $fillable = [
        'SERIAL_RES', 'SERIAL_USR', 'ACCION_MRES', 'ESTADO_ANTERIOR_MRES',
        'ESTADO_NUEVO_MRES', 'OBSERVACION_MRES', 'FECHA_HORA_MRES',
    ];

    protected $casts = ['FECHA_HORA_MRES' => 'datetime'];

    public function reserva()
    {
        return $this->belongsTo(ReservaEspacio::class, 'SERIAL_RES', 'SERIAL_RES');
    }

    public function usuario()
    {
        return $this->belongsTo(UsuarioInstitucional::class, 'SERIAL_USR', 'SERIAL_USR');
    }
}
