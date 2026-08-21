<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilInstitucional extends Model
{
    protected $table = 'perfil';
    protected $primaryKey = 'SERIAL_PFL';
    public $timestamps = false;
    protected $guarded = [];
}
