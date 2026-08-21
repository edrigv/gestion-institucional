<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * En producción no se inyectan datos simulados ni usuarios de prueba.
     * Toda la información institucional y operativa se gestiona directamente
     * a través de las transacciones del sistema y la base de datos oficial.
     */
    public function run(): void
    {
        // Sin inyección de datos ficticios.
    }
}
