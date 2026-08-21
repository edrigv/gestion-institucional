<?php

namespace App\Http\Controllers;

use App\Models\DepartamentoInstitucional;
use App\Models\UsuarioInstitucional;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
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
        abort_unless($this->esAdministrador($this->usuarioActual($request)), 403, 'Solo el administrador puede configurar departamentos.');
    }

    public function store(Request $request)
    {
        $this->soloAdmin($request);

        $datos = $request->validate([
            'CODIGO_DEP' => 'required|string|max:20|unique:departamentos,CODIGO_DEP',
            'DESCRIPCION_DEP' => 'required|string|max:120',
        ]);

        $datos['CONSOLIDADO_DEP'] = 'NO';

        DepartamentoInstitucional::create($datos);

        return back()->with('success', 'Departamento institucional creado correctamente.');
    }

    public function update(Request $request, DepartamentoInstitucional $departamento)
    {
        $this->soloAdmin($request);

        $datos = $request->validate([
            'CODIGO_DEP' => 'required|string|max:20|unique:departamentos,CODIGO_DEP,' . $departamento->SERIAL_DEP . ',SERIAL_DEP',
            'DESCRIPCION_DEP' => 'required|string|max:120',
        ]);

        $departamento->update($datos);

        return back()->with('success', 'Departamento institucional actualizado.');
    }
}
