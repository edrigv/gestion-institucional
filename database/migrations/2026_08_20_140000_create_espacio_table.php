<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('espacio', function (Blueprint $table) {
            $table->id('SERIAL_ESP');
            $table->string('NOMBRE_ESP', 120);
            $table->string('DESCRIPCION_ESP', 255)->nullable();
            $table->string('UBICACION_ESP', 180)->nullable();
            $table->unsignedInteger('CAPACIDAD_ESP')->nullable();
            $table->integer('SERIAL_USR_ENCARGADO')->nullable(); // referencia lógica a usuario.SERIAL_USR
            $table->string('ESTADO_ESP', 20)->default('ACTIVO');
            $table->timestamps();
            $table->index('SERIAL_USR_ENCARGADO');
            $table->index('ESTADO_ESP');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('espacio');
    }
};
