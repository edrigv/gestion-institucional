<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requerimiento extends Model
{
    protected $table = 'requerimiento';

    protected $primaryKey = 'SERIAL_REQ';

    protected $fillable = [
        'NUMERO_REQ',
        'SERIAL_USR_SOLICITA',
        'SERIAL_DEP_ORIGEN',
        'SERIAL_DEP_DESTINO',
        'SERIAL_USR_RESPONSABLE',
        'SERIAL_TREQ',
        'ASUNTO_REQ',
        'DESCRIPCION_REQ',
        'PRIORIDAD_REQ',
        'ESTADO_REQ',
        'FECHA_CREACION_REQ',
        'FECHA_LIMITE_REQ',
        'FECHA_CIERRE_REQ',
        'OBSERVACION_CIERRE_REQ',
    ];

    protected $casts = [
        'FECHA_CREACION_REQ' => 'datetime',
        'FECHA_LIMITE_REQ' => 'datetime',
        'FECHA_CIERRE_REQ' => 'datetime',
    ];

    public function tipo()
    {
        return $this->belongsTo(
            TipoRequerimiento::class,
            'SERIAL_TREQ',
            'SERIAL_TREQ'
        );
    }

    public function departamentoOrigen()
    {
        return $this->belongsTo(
            DepartamentoInstitucional::class,
            'SERIAL_DEP_ORIGEN',
            'SERIAL_DEP'
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

    public function movimientos()
    {
        return $this->hasMany(
            MovimientoRequerimiento::class,
            'SERIAL_REQ',
            'SERIAL_REQ'
        );
    }

    public function archivos()
    {
        return $this->hasMany(
            ArchivoRequerimiento::class,
            'SERIAL_REQ',
            'SERIAL_REQ'
        );
    }

    public function documentos()
    {
        return $this->hasMany(
            DocumentoGestion::class,
            'SERIAL_REQ',
            'SERIAL_REQ'
        );
    }
}