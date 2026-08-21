<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivo_requerimiento', function (Blueprint $table) {
            $table->id('SERIAL_AREQ');

            $table->unsignedBigInteger('SERIAL_REQ');
            $table->integer('SERIAL_USR');

            $table->string('NOMBRE_AREQ', 255);
            $table->string('RUTA_AREQ', 500);
            $table->string('TIPO_AREQ', 100)->nullable();

            $table->dateTime('FECHA_HORA_AREQ');

            $table->string('ESTADO_AREQ', 20)->default('ACTIVO');

            $table->timestamps();

            $table->foreign('SERIAL_REQ')
                ->references('SERIAL_REQ')
                ->on('requerimiento')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivo_requerimiento');
    }
};