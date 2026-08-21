<?php

namespace App\Http\Controllers;

use App\Models\Espacio;
use App\Models\TipoRequerimiento;
use App\Models\DepartamentoInstitucional;
use App\Models\UsuarioInstitucional;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function usuarioActual(Request $request): UsuarioInstitucional
    {
        return $request->attributes->get('usuarioSesion');
    }

    private function esAdministrador(UsuarioInstitucional $usuario): bool
    {
        $codigo = strtoupper(trim((string) ($usuario->perfil?->CODIGO_PFL ?? '')));
        $nombre = strtoupper(trim((string) ($usuario->perfil?->NOMBRE_PFL ?? '')));
        return $codigo === 'ADM' || str_contains($nombre, 'ADMIN');
    }

    private function soloAdmin(Request $request): void
    {
        abort_unless($this->esAdministrador($this->usuarioActual($request)), 403, 'Acceso restringido: Solo el administrador puede acceder al centro de configuración.');
    }

    public function index(Request $request)
    {
        $this->soloAdmin($request);

        $espacios = Espacio::with('encargado.empleado')->orderBy('NOMBRE_ESP')->get();
        $tipos = TipoRequerimiento::with('departamento')->orderBy('NOMBRE_TREQ')->get();
        $departamentos = DepartamentoInstitucional::orderBy('DESCRIPCION_DEP')->get();
        
        $usuarios = UsuarioInstitucional::with(['empleado', 'perfil'])
            ->orderBy('CODIGO_USR')
            ->get();

        $stats = [
            'totalEspacios' => $espacios->count(),
            'espaciosActivos' => $espacios->where('ESTADO_ESP', 'ACTIVO')->count(),
            'totalTipos' => $tipos->count(),
            'totalDepartamentos' => $departamentos->count(),
            'totalUsuarios' => $usuarios->count(),
        ];

        return view('admin.index', compact('espacios', 'tipos', 'departamentos', 'usuarios', 'stats'));
    }
}
