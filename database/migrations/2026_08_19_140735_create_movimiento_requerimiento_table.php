<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimiento_requerimiento', function (Blueprint $table) {
            $table->id('SERIAL_MOV');

            $table->unsignedBigInteger('SERIAL_REQ');

            // Usuario que realiza la acción
            $table->integer('SERIAL_USR');

            $table->string('ACCION_MOV', 40);

            // Destino cuando aplique
            $table->integer('SERIAL_USR_DESTINO')->nullable();
            $table->integer('SERIAL_DEP_DESTINO')->nullable();

            $table->string('ESTADO_ANTERIOR_MOV', 30)->nullable();
            $table->string('ESTADO_NUEVO_MOV', 30)->nullable();

            $table->text('OBSERVACION_MOV')->nullable();

            $table->dateTime('FECHA_HORA_MOV');

            $table->timestamps();

            $table->foreign('SERIAL_REQ')
                ->references('SERIAL_REQ')
                ->on('requerimiento')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_requerimiento');
    }
};