<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioInstitucional extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'SERIAL_USR';
    public $timestamps = false;
    protected $guarded = [];

    public function empleado()
    {
        return $this->belongsTo(EmpleadoInstitucional::class, 'SERIAL_EPL', 'SERIAL_EPL');
    }

    public function perfil()
    {
        return $this->belongsTo(PerfilInstitucional::class, 'SERIAL_PFL', 'SERIAL_PFL');
    }

    public function getNombreCompletoAttribute(): string
    {
        if ($this->empleado) {
            return trim(($this->empleado->NOMBRE_EPL ?? '') . ' ' . ($this->empleado->APELLIDO_EPL ?? ''));
        }

        $nombre = trim(($this->NOMBRE_USR ?? '') . ' ' . ($this->APELLIDO_USR ?? ''));
        return $nombre !== '' ? $nombre : 'Usuario #' . $this->SERIAL_USR;
    }
}
