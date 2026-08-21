<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requerimiento', function (Blueprint $table) {
            $table->id('SERIAL_REQ');

            $table->string('NUMERO_REQ', 25)->unique();

            // Referencias temporales a tablas institucionales
            $table->integer('SERIAL_USR_SOLICITA');
            $table->integer('SERIAL_DEP_ORIGEN')->nullable();
            $table->integer('SERIAL_DEP_DESTINO');
            $table->integer('SERIAL_USR_RESPONSABLE')->nullable();

            $table->unsignedBigInteger('SERIAL_TREQ');

            $table->string('ASUNTO_REQ', 150);
            $table->text('DESCRIPCION_REQ');

            $table->string('PRIORIDAD_REQ', 15)->default('MEDIA');
            $table->string('ESTADO_REQ', 30)->default('BORRADOR');

            $table->dateTime('FECHA_CREACION_REQ');
            $table->dateTime('FECHA_LIMITE_REQ')->nullable();
            $table->dateTime('FECHA_CIERRE_REQ')->nullable();

            $table->text('OBSERVACION_CIERRE_REQ')->nullable();

            $table->timestamps();

            // Esta relación sí es interna al nuevo módulo
            $table->foreign('SERIAL_TREQ')
                ->references('SERIAL_TREQ')
                ->on('tipo_requerimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requerimiento');
    }
};