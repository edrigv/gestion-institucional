<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoInstitucionalSeeder extends Seeder
{
    public function run(): void
    {
        $perfiles = [
            ['CODIGO_PFL' => 'ADM', 'NOMBRE_PFL' => 'Administrador', 'DESCRIPCION_PFL' => 'Perfil administrador de prueba'],
            ['CODIGO_PFL' => 'EMP', 'NOMBRE_PFL' => 'Empleado', 'DESCRIPCION_PFL' => 'Perfil empleado de prueba'],
            ['CODIGO_PFL' => 'AUT', 'NOMBRE_PFL' => 'Autoridad', 'DESCRIPCION_PFL' => 'Perfil autoridad de prueba'],
        ];

        foreach ($perfiles as $perfil) {
            DB::table('perfil')->updateOrInsert(['CODIGO_PFL' => $perfil['CODIGO_PFL']], $perfil);
        }

        DB::table('sucursal')->updateOrInsert(
            ['CODIGO_SUC' => 'LOCAL01'],
            [
                'NOMBRE_SUC' => 'Sede Principal - Pruebas',
                'DIRECCION_SUC' => 'Dirección de prueba',
                'MATRIZ_SUC' => 'SI',
                'EMAIL_SUC' => 'pruebas@local.test',
            ]
        );

        $departamentos = [
            ['CODIGO_DEP' => 'SIS', 'DESCRIPCION_DEP' => 'Sistemas'],
            ['CODIGO_DEP' => 'ADM', 'DESCRIPCION_DEP' => 'Administración'],
            ['CODIGO_DEP' => 'ACA', 'DESCRIPCION_DEP' => 'Académico'],
            ['CODIGO_DEP' => 'TH', 'DESCRIPCION_DEP' => 'Talento Humano'],
        ];

        foreach ($departamentos as $dep) {
            DB::table('departamentos')->updateOrInsert(
                ['CODIGO_DEP' => $dep['CODIGO_DEP']],
                ['DESCRIPCION_DEP' => $dep['DESCRIPCION_DEP'], 'CONSOLIDADO_DEP' => 'NO']
            );
        }

        $serialSuc = DB::table('sucursal')->where('CODIGO_SUC', 'LOCAL01')->value('SERIAL_SUC');

        foreach ($departamentos as $dep) {
            $serialDep = DB::table('departamentos')->where('CODIGO_DEP', $dep['CODIGO_DEP'])->value('SERIAL_DEP');
            DB::table('sucursaldepartamentos')->updateOrInsert(
                ['SERIAL_SUC' => $serialSuc, 'SERIAL_DEP' => $serialDep],
                []
            );
        }

        $empleados = [
            ['doc' => '9990000001', 'nombre' => 'Carlos', 'apellido' => 'Mendoza', 'email' => 'carlos.mendoza@local.test', 'dep' => 'SIS', 'perfil' => 'ADM', 'codigo' => 'carlos.p'],
            ['doc' => '9990000002', 'nombre' => 'Maria', 'apellido' => 'Torres', 'email' => 'maria.torres@local.test', 'dep' => 'ADM', 'perfil' => 'EMP', 'codigo' => 'maria.p'],
            ['doc' => '9990000003', 'nombre' => 'Ana', 'apellido' => 'Vera', 'email' => 'ana.vera@local.test', 'dep' => 'ACA', 'perfil' => 'AUT', 'codigo' => 'ana.p'],
            ['doc' => '9990000004', 'nombre' => 'Sofia', 'apellido' => 'Lopez', 'email' => 'sofia.lopez@local.test', 'dep' => 'TH', 'perfil' => 'EMP', 'codigo' => 'sofia.p'],
        ];

        foreach ($empleados as $e) {
            $serialDep = DB::table('departamentos')->where('CODIGO_DEP', $e['dep'])->value('SERIAL_DEP');
            $serialDesc = DB::table('sucursaldepartamentos')
                ->where('SERIAL_SUC', $serialSuc)
                ->where('SERIAL_DEP', $serialDep)
                ->value('SERIAL_DESC');

            DB::table('empleado')->updateOrInsert(
                ['DOCUMENTOIDENTIDAD_EPL' => $e['doc']],
                [
                    'SERIAL_DESC' => $serialDesc,
                    'NOMBRE_EPL' => $e['nombre'],
                    'APELLIDO_EPL' => $e['apellido'],
                    'TIPOEMPLEADO_EPL' => 'ADMINISTRATIVO',
                    'EMAIL_EPL' => $e['email'],
                    'ESTADOEMPLEADO_EPL' => 'ACTIVO',
                    'ESTADO_EPL' => 'ACTIVO',
                ]
            );

            $serialEpl = DB::table('empleado')->where('DOCUMENTOIDENTIDAD_EPL', $e['doc'])->value('SERIAL_EPL');
            $serialPfl = DB::table('perfil')->where('CODIGO_PFL', $e['perfil'])->value('SERIAL_PFL');

            DB::table('usuario')->updateOrInsert(
                ['CODIGO_USR' => $e['codigo']],
                [
                    'SERIAL_PFL' => $serialPfl,
                    'SERIAL_EPL' => $serialEpl,
                    'CLAVE_USR' => 'PRUEBA123',
                    'NOMBRE_USR' => $e['nombre'],
                    'APELLIDO_USR' => $e['apellido'],
                    'NOMBRE2_USR' => '',
                    'APELLIDO2_USR' => '',
                    'EMAIL_USR' => $e['email'],
                    'ESTADO_USR' => 'ACTIVO',
                    'ESEMPLEADO' => 'SI',
                ]
            );
        }

        // Espacios de prueba para el módulo de reservas.
        $usuariosPorCodigo = DB::table('usuario')
            ->whereIn('CODIGO_USR', ['carlos.p', 'maria.p', 'ana.p'])
            ->pluck('SERIAL_USR', 'CODIGO_USR');

        $espacios = [
            ['NOMBRE_ESP' => 'Auditorio', 'DESCRIPCION_ESP' => 'Auditorio institucional para reuniones y eventos.', 'UBICACION_ESP' => 'Bloque principal', 'CAPACIDAD_ESP' => 180, 'encargado' => 'carlos.p'],
            ['NOMBRE_ESP' => 'Coliseo', 'DESCRIPCION_ESP' => 'Espacio deportivo y de eventos masivos.', 'UBICACION_ESP' => 'Área deportiva', 'CAPACIDAD_ESP' => 600, 'encargado' => 'ana.p'],
            ['NOMBRE_ESP' => 'Sala de reuniones', 'DESCRIPCION_ESP' => 'Sala para reuniones administrativas y académicas.', 'UBICACION_ESP' => 'Administración', 'CAPACIDAD_ESP' => 24, 'encargado' => 'maria.p'],
        ];

        foreach ($espacios as $espacio) {
            DB::table('espacio')->updateOrInsert(
                ['NOMBRE_ESP' => $espacio['NOMBRE_ESP']],
                [
                    'DESCRIPCION_ESP' => $espacio['DESCRIPCION_ESP'],
                    'UBICACION_ESP' => $espacio['UBICACION_ESP'],
                    'CAPACIDAD_ESP' => $espacio['CAPACIDAD_ESP'],
                    'SERIAL_USR_ENCARGADO' => $usuariosPorCodigo[$espacio['encargado']] ?? null,
                    'ESTADO_ESP' => 'ACTIVO',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        DB::table('tipo_requerimiento')->updateOrInsert(
            ['NOMBRE_TREQ' => 'Soporte técnico'],
            [
                'DESCRIPCION_TREQ' => 'Incidencias de equipos, sistemas y soporte informático',
                'SERIAL_DEP' => DB::table('departamentos')->where('CODIGO_DEP', 'SIS')->value('SERIAL_DEP'),
                'REQUIERE_FIRMA_TREQ' => false,
                'REQUIERE_APROBACION_TREQ' => false,
                'ESTADO_TREQ' => 'ACTIVO',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
