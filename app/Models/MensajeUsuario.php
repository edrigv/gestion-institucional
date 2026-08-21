<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MensajeUsuario extends Model
{
    protected $table = 'mensaje_usuario';
    protected $primaryKey = 'SERIAL_MEN';

    protected $fillable = [
        'SERIAL_USR_ENVIA',
        'SERIAL_USR_RECIBE',
        'ASUNTO_MEN',
        'CONTENIDO_MEN',
        'ESTADO_MEN',
        'FECHA_HORA_MEN',
        'FECHA_LECTURA_MEN',
        'SERIAL_REQ',
    ];

    protected $casts = [
        'FECHA_HORA_MEN' => 'datetime',
        'FECHA_LECTURA_MEN' => 'datetime',
    ];

    public function remitente()
    {
        return $this->belongsTo(UsuarioInstitucional::class, 'SERIAL_USR_ENVIA', 'SERIAL_USR');
    }

    public function destinatario()
    {
        return $this->belongsTo(UsuarioInstitucional::class, 'SERIAL_USR_RECIBE', 'SERIAL_USR');
    }

    public function requerimiento()
    {
        return $this->belongsTo(Requerimiento::class, 'SERIAL_REQ', 'SERIAL_REQ');
    }
}
