<?php

namespace App\Http\Controllers;

use App\Models\Espacio;
use App\Models\UsuarioInstitucional;
use Illuminate\Http\Request;

class EspacioController extends Controller
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
        abort_unless($this->esAdministrador($this->usuarioActual($request)), 403, 'Solo el administrador puede configurar espacios.');
    }

    public function index(Request $request)
    {
        $this->soloAdmin($request);
        $espacios = Espacio::with('encargado.empleado')->orderBy('NOMBRE_ESP')->get();
        $usuarios = UsuarioInstitucional::with('empleado')->whereNotNull('SERIAL_EPL')->get()->sortBy('nombre_completo')->values();
        return view('espacios.index', compact('espacios', 'usuarios'));
    }

    public function create(Request $request)
    {
        $this->soloAdmin($request);
        $usuarios = UsuarioInstitucional::with('empleado')->whereNotNull('SERIAL_EPL')->get()->sortBy('nombre_completo')->values();
        return view('espacios.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $this->soloAdmin($request);
        $datos = $request->validate([
            'NOMBRE_ESP' => 'required|string|max:120',
            'DESCRIPCION_ESP' => 'nullable|string|max:255',
            'UBICACION_ESP' => 'nullable|string|max:180',
            'CAPACIDAD_ESP' => 'nullable|integer|min:1|max:100000',
            'SERIAL_USR_ENCARGADO' => 'nullable|integer|exists:usuario,SERIAL_USR',
            'ESTADO_ESP' => 'required|in:ACTIVO,INACTIVO',
        ]);
        Espacio::create($datos);
        return redirect()->route('espacios.index')->with('success', 'Espacio creado correctamente.');
    }

    public function edit(Request $request, Espacio $espacio)
    {
        $this->soloAdmin($request);
        $usuarios = UsuarioInstitucional::with('empleado')->whereNotNull('SERIAL_EPL')->get()->sortBy('nombre_completo')->values();
        return view('espacios.edit', compact('espacio', 'usuarios'));
    }

    public function update(Request $request, Espacio $espacio)
    {
        $this->soloAdmin($request);
        $datos = $request->validate([
            'NOMBRE_ESP' => 'required|string|max:120',
            'DESCRIPCION_ESP' => 'nullable|string|max:255',
            'UBICACION_ESP' => 'nullable|string|max:180',
            'CAPACIDAD_ESP' => 'nullable|integer|min:1|max:100000',
            'SERIAL_USR_ENCARGADO' => 'nullable|integer|exists:usuario,SERIAL_USR',
            'ESTADO_ESP' => 'required|in:ACTIVO,INACTIVO',
        ]);
        $espacio->update($datos);
        return redirect()->route('espacios.index')->with('success', 'Espacio actualizado.');
    }
}
