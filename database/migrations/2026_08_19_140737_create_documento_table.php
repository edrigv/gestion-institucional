<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_gestion', function (Blueprint $table) {
            $table->id('SERIAL_DOC_GES');

            $table->string('NUMERO_DOC', 30)->nullable()->unique();

            $table->unsignedBigInteger('SERIAL_REQ')->nullable();

            // Referencias institucionales temporales
            $table->integer('SERIAL_USR_AUTOR');
            $table->integer('SERIAL_USR_DESTINO')->nullable();

            $table->integer('SERIAL_DEP_ORIGEN')->nullable();
            $table->integer('SERIAL_DEP_DESTINO')->nullable();

            $table->string('TIPO_DOC', 30);
            $table->string('ASUNTO_DOC', 180);

            $table->string('RUTA_DOC', 500)->nullable();

            $table->string('ESTADO_DOC', 25)->default('BORRADOR');

            $table->dateTime('FECHA_HORA_DOC');

            $table->timestamps();

            $table->foreign('SERIAL_REQ')
                ->references('SERIAL_REQ')
                ->on('requerimiento')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_gestion');
    }
};