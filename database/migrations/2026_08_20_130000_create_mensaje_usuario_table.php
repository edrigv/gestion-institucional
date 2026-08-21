<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensaje_usuario', function (Blueprint $table) {
            $table->id('SERIAL_MEN');
            $table->integer('SERIAL_USR_ENVIA');
            $table->integer('SERIAL_USR_RECIBE');
            $table->string('ASUNTO_MEN', 180);
            $table->text('CONTENIDO_MEN');
            $table->string('ESTADO_MEN', 20)->default('ENVIADO');
            $table->dateTime('FECHA_HORA_MEN');
            $table->dateTime('FECHA_LECTURA_MEN')->nullable();
            $table->unsignedBigInteger('SERIAL_REQ')->nullable();
            $table->timestamps();

            $table->index('SERIAL_USR_ENVIA');
            $table->index('SERIAL_USR_RECIBE');
            $table->index('ESTADO_MEN');

            $table->foreign('SERIAL_REQ')
                ->references('SERIAL_REQ')
                ->on('requerimiento')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensaje_usuario');
    }
};
