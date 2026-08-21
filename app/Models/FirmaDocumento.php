<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirmaDocumento extends Model
{
    protected $table = 'firma_documento';

    protected $primaryKey = 'SERIAL_FIR';

    protected $fillable = [
        'SERIAL_DOC_GES',
        'SERIAL_USR',
        'FECHA_HORA_FIR',
        'TIPO_FIRMA',
        'HASH_DOCUMENTO',
        'ESTADO_FIR',
    ];

    protected $casts = [
        'FECHA_HORA_FIR' => 'datetime',
    ];

    public function documento()
    {
        return $this->belongsTo(
            DocumentoGestion::class,
            'SERIAL_DOC_GES',
            'SERIAL_DOC_GES'
        );
    }
}