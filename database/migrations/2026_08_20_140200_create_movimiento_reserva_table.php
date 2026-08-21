<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimiento_reserva', function (Blueprint $table) {
            $table->id('SERIAL_MRES');
            $table->unsignedBigInteger('SERIAL_RES');
            $table->integer('SERIAL_USR'); // referencia lógica a usuario.SERIAL_USR
            $table->string('ACCION_MRES', 50);
            $table->string('ESTADO_ANTERIOR_MRES', 20)->nullable();
            $table->string('ESTADO_NUEVO_MRES', 20)->nullable();
            $table->text('OBSERVACION_MRES')->nullable();
            $table->dateTime('FECHA_HORA_MRES');
            $table->timestamps();

            $table->foreign('SERIAL_RES')->references('SERIAL_RES')->on('reserva_espacio')->cascadeOnDelete();
            $table->index('SERIAL_USR');
            $table->index('ACCION_MRES');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_reserva');
    }
};
