<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoInstitucional extends Model
{
    protected $table = 'empleado';
    protected $primaryKey = 'SERIAL_EPL';
    public $timestamps = false;
    protected $guarded = [];
}
