<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRequerimiento extends Model
{
    protected $table = 'tipo_requerimiento';

    protected $primaryKey = 'SERIAL_TREQ';

    protected $fillable = [
        'NOMBRE_TREQ',
        'DESCRIPCION_TREQ',
        'SERIAL_DEP',
        'REQUIERE_FIRMA_TREQ',
        'REQUIERE_APROBACION_TREQ',
        'ESTADO_TREQ',
    ];

    protected $casts = [
        'REQUIERE_FIRMA_TREQ' => 'boolean',
        'REQUIERE_APROBACION_TREQ' => 'boolean',
    ];

    public function requerimientos()
    {
        return $this->hasMany(
            Requerimiento::class,
            'SERIAL_TREQ',
            'SERIAL_TREQ'
        );
    }

    public function departamento()
    {
        return $this->belongsTo(
            DepartamentoInstitucional::class,
            'SERIAL_DEP',
            'SERIAL_DEP'
        );
    }
}