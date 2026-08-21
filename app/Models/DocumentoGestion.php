<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoGestion extends Model
{
    protected $table = 'documento_gestion';

    protected $primaryKey = 'SERIAL_DOC_GES';

    protected $fillable = [
        'NUMERO_DOC',
        'SERIAL_REQ',
        'SERIAL_USR_AUTOR',
        'SERIAL_USR_DESTINO',
        'SERIAL_DEP_ORIGEN',
        'SERIAL_DEP_DESTINO',
        'TIPO_DOC',
        'ASUNTO_DOC',
        'RUTA_DOC',
        'ESTADO_DOC',
        'FECHA_HORA_DOC',
    ];

    protected $casts = [
        'FECHA_HORA_DOC' => 'datetime',
    ];

    public function requerimiento()
    {
        return $this->belongsTo(
            Requerimiento::class,
            'SERIAL_REQ',
            'SERIAL_REQ'
        );
    }

    public function firmas()
    {
        return $this->hasMany(
            FirmaDocumento::class,
            'SERIAL_DOC_GES',
            'SERIAL_DOC_GES'
        );
    }
}