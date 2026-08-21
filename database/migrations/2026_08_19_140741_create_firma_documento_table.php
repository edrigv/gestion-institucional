<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('firma_documento', function (Blueprint $table) {
            $table->id('SERIAL_FIR');

            $table->unsignedBigInteger('SERIAL_DOC_GES');

            // Usuario institucional que firma
            $table->integer('SERIAL_USR');

            $table->dateTime('FECHA_HORA_FIR');

            $table->string('TIPO_FIRMA', 30);
            $table->string('HASH_DOCUMENTO', 128);

            $table->string('ESTADO_FIR', 20)->default('VALIDA');

            $table->timestamps();

            $table->foreign('SERIAL_DOC_GES')
                ->references('SERIAL_DOC_GES')
                ->on('documento_gestion')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firma_documento');
    }
};