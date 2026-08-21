<?php

namespace App\Http\Controllers;

use App\Models\TipoRequerimiento;
use App\Models\DepartamentoInstitucional;
use Illuminate\Http\Request;

class TipoRequerimientoController extends Controller
{
    public function index()
    {
        $tipos = TipoRequerimiento::with('departamento')->orderBy('NOMBRE_TREQ')->get();

        return view('tipos-requerimiento.index', compact('tipos'));
    }

    public function create()
    {
        $departamentos = DepartamentoInstitucional::orderBy('DESCRIPCION_DEP')->get();

        return view('tipos-requerimiento.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'NOMBRE_TREQ' => 'required|string|max:100',
            'DESCRIPCION_TREQ' => 'nullable|string|max:255',
            'SERIAL_DEP' => 'nullable|integer',
            'REQUIERE_FIRMA_TREQ' => 'nullable|boolean',
            'REQUIERE_APROBACION_TREQ' => 'nullable|boolean',
        ]);

        $datos['REQUIERE_FIRMA_TREQ'] =
            $request->boolean('REQUIERE_FIRMA_TREQ');

        $datos['REQUIERE_APROBACION_TREQ'] =
            $request->boolean('REQUIERE_APROBACION_TREQ');

        $datos['ESTADO_TREQ'] = 'ACTIVO';

        TipoRequerimiento::create($datos);

        return redirect()
            ->route('tipos-requerimiento.index')
            ->with('success', 'Tipo de requerimiento creado correctamente.');
    }

    public function show(TipoRequerimiento $tipos_requerimiento)
    {
        return view(
            'tipos-requerimiento.show',
            ['tipo' => $tipos_requerimiento]
        );
    }

    public function edit(TipoRequerimiento $tipos_requerimiento)
    {
        return view(
            'tipos-requerimiento.edit',
            ['tipo' => $tipos_requerimiento]
        );
    }

    public function update(
        Request $request,
        TipoRequerimiento $tipos_requerimiento
    ) {
        $datos = $request->validate([
            'NOMBRE_TREQ' => 'required|string|max:100',
            'DESCRIPCION_TREQ' => 'nullable|string|max:255',
            'SERIAL_DEP' => 'nullable|integer',
            'REQUIERE_FIRMA_TREQ' => 'nullable|boolean',
            'REQUIERE_APROBACION_TREQ' => 'nullable|boolean',
            'ESTADO_TREQ' => 'required|string|max:20',
        ]);

        $datos['REQUIERE_FIRMA_TREQ'] =
            $request->boolean('REQUIERE_FIRMA_TREQ');

        $datos['REQUIERE_APROBACION_TREQ'] =
            $request->boolean('REQUIERE_APROBACION_TREQ');

        $tipos_requerimiento->update($datos);

        return redirect()
            ->route('tipos-requerimiento.index')
            ->with('success', 'Tipo de requerimiento actualizado.');
    }

    public function destroy(TipoRequerimiento $tipos_requerimiento)
    {
        // Para este sistema conviene desactivar, no borrar físicamente.
        $tipos_requerimiento->update([
            'ESTADO_TREQ' => 'INACTIVO'
        ]);

        return redirect()
            ->route('tipos-requerimiento.index')
            ->with('success', 'Tipo de requerimiento desactivado.');
    }
}