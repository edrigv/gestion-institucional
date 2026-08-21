<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartamentoInstitucional extends Model
{
    protected $table = 'departamentos';
    protected $primaryKey = 'SERIAL_DEP';
    public $timestamps = false;
    protected $guarded = [];

    public function getNombreAttribute(): string
    {
        return $this->DESCRIPCION_DEP ?? $this->CODIGO_DEP ?? ('Departamento #' . $this->SERIAL_DEP);
    }
}
