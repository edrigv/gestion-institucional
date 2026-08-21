<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProduccionInstitucionalSeeder extends Seeder
{
    /**
     * Cargar la estructura institucional oficial y datos maestros de producción
     * para la Unidad Educativa Particular Cristo Rey (Portoviejo, Manabí, Ecuador).
     */
    public function run(): void
    {
        // 1. Perfiles de Acceso Institucional Oficiales
        $perfiles = [
            [
                'CODIGO_PFL' => 'ADM',
                'NOMBRE_PFL' => 'Administrador',
                'DESCRIPCION_PFL' => 'Administrador general con control total del sistema, espacios, usuarios y catálogos.',
            ],
            [
                'CODIGO_PFL' => 'AUT',
                'NOMBRE_PFL' => 'Autoridad',
                'DESCRIPCION_PFL' => 'Rectorado, Vicerrectorado y Direcciones con potestad de aprobación superior y firma.',
            ],
            [
                'CODIGO_PFL' => 'DOC',
                'NOMBRE_PFL' => 'Docente',
                'DESCRIPCION_PFL' => 'Cuerpo docente y profesores de los diferentes niveles educativos.',
            ],
            [
                'CODIGO_PFL' => 'JEF',
                'NOMBRE_PFL' => 'Responsable de Departamento',
                'DESCRIPCION_PFL' => 'Jefaturas y encargados departamentales de gestión y atención de trámites.',
            ],
            [
                'CODIGO_PFL' => 'EMP',
                'NOMBRE_PFL' => 'Personal Administrativo',
                'DESCRIPCION_PFL' => 'Personal de secretaría, servicios generales, talento humano y apoyo institucional.',
            ],
        ];

        foreach ($perfiles as $perfil) {
            DB::table('perfil')->updateOrInsert(
                ['CODIGO_PFL' => $perfil['CODIGO_PFL']],
                $perfil
            );
        }

        // 2. Sede Institucional Principal
        DB::table('sucursal')->updateOrInsert(
            ['CODIGO_SUC' => 'MATRIZ'],
            [
                'NOMBRE_SUC' => 'Unidad Educativa Particular Cristo Rey',
                'DIRECCION_SUC' => 'Av. Guayaquil y San Francisco, Portoviejo, Manabí, Ecuador',
                'MATRIZ_SUC' => 'SI',
                'EMAIL_SUC' => 'info@cristorey.edu.ec',
            ]
        );

        $serialSuc = DB::table('sucursal')->where('CODIGO_SUC', 'MATRIZ')->value('SERIAL_SUC');

        // 3. Departamentos Institucionales Oficiales
        $departamentos = [
            ['CODIGO_DEP' => 'REC', 'DESCRIPCION_DEP' => 'Rectorado'],
            ['CODIGO_DEP' => 'VIC', 'DESCRIPCION_DEP' => 'Vicerrectorado Académico'],
            ['CODIGO_DEP' => 'PAS', 'DESCRIPCION_DEP' => 'Pastoral y Formación Cristiana'],
            ['CODIGO_DEP' => 'TIC', 'DESCRIPCION_DEP' => 'Tecnologías de la Información y Comunicación (TIC)'],
            ['CODIGO_DEP' => 'SEC', 'DESCRIPCION_DEP' => 'Secretaría General'],
            ['CODIGO_DEP' => 'ADM', 'DESCRIPCION_DEP' => 'Administración y Finanzas'],
            ['CODIGO_DEP' => 'TH',  'DESCRIPCION_DEP' => 'Talento Humano'],
            ['CODIGO_DEP' => 'DECE','DESCRIPCION_DEP' => 'Consejería Estudiantil (DECE)'],
            ['CODIGO_DEP' => 'MAN', 'DESCRIPCION_DEP' => 'Mantenimiento e Infraestructura'],
            ['CODIGO_DEP' => 'BIB', 'DESCRIPCION_DEP' => 'Biblioteca y Recursos de Aprendizaje'],
            ['CODIGO_DEP' => 'DEP', 'DESCRIPCION_DEP' => 'Coordinación de Deportes y Cultura'],
        ];

        foreach ($departamentos as $dep) {
            DB::table('departamentos')->updateOrInsert(
                ['CODIGO_DEP' => $dep['CODIGO_DEP']],
                ['DESCRIPCION_DEP' => $dep['DESCRIPCION_DEP'], 'CONSOLIDADO_DEP' => 'NO']
            );

            $serialDep = DB::table('departamentos')->where('CODIGO_DEP', $dep['CODIGO_DEP'])->value('SERIAL_DEP');
            DB::table('sucursaldepartamentos')->updateOrInsert(
                ['SERIAL_SUC' => $serialSuc, 'SERIAL_DEP' => $serialDep],
                []
            );
        }

        // 4. Catálogo Oficial de Tipos de Requerimientos Institucionales
        $tiposRequerimiento = [
            [
                'NOMBRE_TREQ' => 'Soporte Técnico y Sistemas TIC',
                'DESCRIPCION_TREQ' => 'Incidencias con equipos informáticos, conectividad, software y plataformas educativas.',
                'dep' => 'TIC',
                'REQUIERE_FIRMA_TREQ' => false,
                'REQUIERE_APROBACION_TREQ' => false,
            ],
            [
                'NOMBRE_TREQ' => 'Mantenimiento de Infraestructura y Aulas',
                'DESCRIPCION_TREQ' => 'Solicitud de reparaciones eléctricas, mobiliario, pintura, climatización y cerrajería.',
                'dep' => 'MAN',
                'REQUIERE_FIRMA_TREQ' => false,
                'REQUIERE_APROBACION_TREQ' => false,
            ],
            [
                'NOMBRE_TREQ' => 'Solicitud de Permiso / Licencia Laboral',
                'DESCRIPCION_TREQ' => 'Trámite formal de permisos por calamidad, salud, capacitación o comisiones de servicio.',
                'dep' => 'TH',
                'REQUIERE_FIRMA_TREQ' => true,
                'REQUIERE_APROBACION_TREQ' => true,
            ],
            [
                'NOMBRE_TREQ' => 'Certificación y Trámites de Secretaría',
                'DESCRIPCION_TREQ' => 'Emisión de certificados laborales, actas académicas y constancias institucionales.',
                'dep' => 'SEC',
                'REQUIERE_FIRMA_TREQ' => true,
                'REQUIERE_APROBACION_TREQ' => false,
            ],
            [
                'NOMBRE_TREQ' => 'Adquisición de Insumos y Materiales',
                'DESCRIPCION_TREQ' => 'Requerimiento de compra o reposición de insumos de oficina, didácticos y suministros.',
                'dep' => 'ADM',
                'REQUIERE_FIRMA_TREQ' => true,
                'REQUIERE_APROBACION_TREQ' => true,
            ],
            [
                'NOMBRE_TREQ' => 'Uso de Recursos y Apoyo Pastoral',
                'DESCRIPCION_TREQ' => 'Coordinación para convivencias, liturgias, retiros y actividades de formación jesuita.',
                'dep' => 'PAS',
                'REQUIERE_FIRMA_TREQ' => false,
                'REQUIERE_APROBACION_TREQ' => false,
            ],
            [
                'NOMBRE_TREQ' => 'Atención Psicopedagógica y Acompañamiento DECE',
                'DESCRIPCION_TREQ' => 'Derivación de casos estudiantiles, seguimiento psicopedagógico y atención familiar.',
                'dep' => 'DECE',
                'REQUIERE_FIRMA_TREQ' => false,
                'REQUIERE_APROBACION_TREQ' => false,
            ],
            [
                'NOMBRE_TREQ' => 'Préstamo y Recursos de Biblioteca',
                'DESCRIPCION_TREQ' => 'Solicitud de textos guías, material bibliográfico y uso de salas de lectura.',
                'dep' => 'BIB',
                'REQUIERE_FIRMA_TREQ' => false,
                'REQUIERE_APROBACION_TREQ' => false,
            ],
        ];

        foreach ($tiposRequerimiento as $tipo) {
            $serialDep = DB::table('departamentos')->where('CODIGO_DEP', $tipo['dep'])->value('SERIAL_DEP');
            DB::table('tipo_requerimiento')->updateOrInsert(
                ['NOMBRE_TREQ' => $tipo['NOMBRE_TREQ']],
                [
                    'DESCRIPCION_TREQ' => $tipo['DESCRIPCION_TREQ'],
                    'SERIAL_DEP' => $serialDep,
                    'REQUIERE_FIRMA_TREQ' => $tipo['REQUIERE_FIRMA_TREQ'],
                    'REQUIERE_APROBACION_TREQ' => $tipo['REQUIERE_APROBACION_TREQ'],
                    'ESTADO_TREQ' => 'ACTIVO',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 5. Usuarios Institucionales Iniciales de Producción
        $usuariosIniciales = [
            [
                'doc' => '1300000001',
                'nombre' => 'Administrador',
                'apellido' => 'General',
                'email' => 'admin@cristorey.edu.ec',
                'dep' => 'TIC',
                'perfil' => 'ADM',
                'codigo' => 'ADMIN',
                'clave' => 'CristoRey2026*',
            ],
            [
                'doc' => '1300000002',
                'nombre' => 'Rectorado',
                'apellido' => 'Institucional',
                'email' => 'rectorado@cristorey.edu.ec',
                'dep' => 'REC',
                'perfil' => 'AUT',
                'codigo' => 'RECTOR',
                'clave' => 'CristoRey2026*',
            ],
            [
                'doc' => '1300000003',
                'nombre' => 'Vicerrectorado',
                'apellido' => 'Académico',
                'email' => 'vicerrectorado@cristorey.edu.ec',
                'dep' => 'VIC',
                'perfil' => 'AUT',
                'codigo' => 'VICEREC',
                'clave' => 'CristoRey2026*',
            ],
            [
                'doc' => '1300000004',
                'nombre' => 'Coordinación',
                'apellido' => 'Pastoral',
                'email' => 'pastoral@cristorey.edu.ec',
                'dep' => 'PAS',
                'perfil' => 'JEF',
                'codigo' => 'PASTORAL',
                'clave' => 'CristoRey2026*',
            ],
            [
                'doc' => '1300000005',
                'nombre' => 'Secretaría',
                'apellido' => 'General',
                'email' => 'secretaria@cristorey.edu.ec',
                'dep' => 'SEC',
                'perfil' => 'EMP',
                'codigo' => 'SECRETAR',
                'clave' => 'CristoRey2026*',
            ],
            [
                'doc' => '1300000006',
                'nombre' => 'Docente',
                'apellido' => 'Ejemplo',
                'email' => 'docente@cristorey.edu.ec',
                'dep' => 'VIC',
                'perfil' => 'DOC',
                'codigo' => 'DOCENTE',
                'clave' => 'CristoRey2026*',
            ],
        ];

        foreach ($usuariosIniciales as $u) {
            $serialDep = DB::table('departamentos')->where('CODIGO_DEP', $u['dep'])->value('SERIAL_DEP');
            $serialDesc = DB::table('sucursaldepartamentos')
                ->where('SERIAL_SUC', $serialSuc)
                ->where('SERIAL_DEP', $serialDep)
                ->value('SERIAL_DESC');

            DB::table('empleado')->updateOrInsert(
                ['DOCUMENTOIDENTIDAD_EPL' => $u['doc']],
                [
                    'SERIAL_DESC' => $serialDesc,
                    'NOMBRE_EPL' => $u['nombre'],
                    'APELLIDO_EPL' => $u['apellido'],
                    'TIPOEMPLEADO_EPL' => $u['perfil'] === 'DOC' ? 'DOCENTE' : 'ADMINISTRATIVO',
                    'EMAIL_EPL' => $u['email'],
                    'ESTADOEMPLEADO_EPL' => 'ACTIVO',
                    'ESTADO_EPL' => 'ACTIVO',
                ]
            );

            $serialEpl = DB::table('empleado')->where('DOCUMENTOIDENTIDAD_EPL', $u['doc'])->value('SERIAL_EPL');
            $serialPfl = DB::table('perfil')->where('CODIGO_PFL', $u['perfil'])->value('SERIAL_PFL');

            DB::table('usuario')->updateOrInsert(
                ['CODIGO_USR' => $u['codigo']],
                [
                    'SERIAL_PFL' => $serialPfl,
                    'SERIAL_EPL' => $serialEpl,
                    'CLAVE_USR' => Hash::make($u['clave']),
                    'NOMBRE_USR' => $u['nombre'],
                    'APELLIDO_USR' => $u['apellido'],
                    'NOMBRE2_USR' => '',
                    'APELLIDO2_USR' => '',
                    'EMAIL_USR' => $u['email'],
                    'ESTADO_USR' => 'ACTIVO',
                    'ESEMPLEADO' => 'SI',
                ]
            );
        }
    }
}
