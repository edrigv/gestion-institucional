<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservaEspacio extends Model
{
    protected $table = 'reserva_espacio';
    protected $primaryKey = 'SERIAL_RES';

    protected $fillable = [
        'NUMERO_RES', 'SERIAL_ESP', 'SERIAL_USR_SOLICITA', 'TITULO_RES',
        'DESCRIPCION_RES', 'FECHA_INICIO_RES', 'FECHA_FIN_RES', 'ESTADO_RES',
        'OBSERVACION_RES', 'FECHA_CREACION_RES', 'FECHA_RESOLUCION_RES',
        'SERIAL_USR_RESUELVE',
    ];

    protected $casts = [
        'FECHA_INICIO_RES' => 'datetime',
        'FECHA_FIN_RES' => 'datetime',
        'FECHA_CREACION_RES' => 'datetime',
        'FECHA_RESOLUCION_RES' => 'datetime',
    ];

    public function espacio()
    {
        return $this->belongsTo(Espacio::class, 'SERIAL_ESP', 'SERIAL_ESP');
    }

    public function solicitante()
    {
        return $this->belongsTo(UsuarioInstitucional::class, 'SERIAL_USR_SOLICITA', 'SERIAL_USR');
    }

    public function resuelve()
    {
        return $this->belongsTo(UsuarioInstitucional::class, 'SERIAL_USR_RESUELVE', 'SERIAL_USR');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoReserva::class, 'SERIAL_RES', 'SERIAL_RES');
    }

    public function conflictosAprobados()
    {
        return self::with(['solicitante.empleado'])
            ->where('SERIAL_ESP', $this->SERIAL_ESP)
            ->where('SERIAL_RES', '<>', $this->SERIAL_RES)
            ->where('ESTADO_RES', 'APROBADA')
            ->where('FECHA_INICIO_RES', '<', $this->FECHA_FIN_RES)
            ->where('FECHA_FIN_RES', '>', $this->FECHA_INICIO_RES)
            ->orderBy('FECHA_INICIO_RES')
            ->get();
    }
}
