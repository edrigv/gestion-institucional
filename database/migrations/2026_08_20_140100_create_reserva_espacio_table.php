<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reserva_espacio', function (Blueprint $table) {
            $table->id('SERIAL_RES');
            $table->string('NUMERO_RES', 25)->unique();
            $table->unsignedBigInteger('SERIAL_ESP');
            $table->integer('SERIAL_USR_SOLICITA'); // referencia lógica a usuario.SERIAL_USR
            $table->string('TITULO_RES', 180);
            $table->text('DESCRIPCION_RES')->nullable();
            $table->dateTime('FECHA_INICIO_RES');
            $table->dateTime('FECHA_FIN_RES');
            $table->string('ESTADO_RES', 20)->default('PENDIENTE');
            $table->text('OBSERVACION_RES')->nullable();
            $table->dateTime('FECHA_CREACION_RES');
            $table->dateTime('FECHA_RESOLUCION_RES')->nullable();
            $table->integer('SERIAL_USR_RESUELVE')->nullable(); // referencia lógica a usuario.SERIAL_USR
            $table->timestamps();

            $table->foreign('SERIAL_ESP')->references('SERIAL_ESP')->on('espacio')->cascadeOnDelete();
            $table->index(['SERIAL_ESP', 'FECHA_INICIO_RES', 'FECHA_FIN_RES'], 'idx_reserva_horario');
            $table->index('SERIAL_USR_SOLICITA');
            $table->index('ESTADO_RES');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reserva_espacio');
    }
};
