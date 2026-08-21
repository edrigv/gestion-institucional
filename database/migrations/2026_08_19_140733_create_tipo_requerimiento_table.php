<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_requerimiento', function (Blueprint $table) {
            $table->id('SERIAL_TREQ');

            $table->string('NOMBRE_TREQ', 100);
            $table->string('DESCRIPCION_TREQ', 255)->nullable();

            // Referencia temporal al departamento institucional
            $table->integer('SERIAL_DEP')->nullable();

            $table->boolean('REQUIERE_FIRMA_TREQ')->default(false);
            $table->boolean('REQUIERE_APROBACION_TREQ')->default(false);

            $table->string('ESTADO_TREQ', 20)->default('ACTIVO');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_requerimiento');
    }
};