<?php

namespace App\Http\Controllers;

use App\Models\Requerimiento;

class DashboardController extends Controller
{
    public function index()
    {
        $total = Requerimiento::count();

        $enProceso = Requerimiento::where(
            'ESTADO_REQ',
            'EN_PROCESO'
        )->count();

        $pendientesAprobacion = Requerimiento::where(
            'ESTADO_REQ',
            'PENDIENTE_APROBACION'
        )->count();

        $pendientesFirma = Requerimiento::where(
            'ESTADO_REQ',
            'PENDIENTE_FIRMA'
        )->count();

        $cerrados = Requerimiento::where(
            'ESTADO_REQ',
            'CERRADO'
        )->count();

        $ultimos = Requerimiento::with('tipo')
            ->orderByDesc('FECHA_CREACION_REQ')
            ->limit(8)
            ->get();

        return view(
            'dashboard',
            compact(
                'total',
                'enProceso',
                'pendientesAprobacion',
                'pendientesFirma',
                'cerrados',
                'ultimos'
            )
        );
    }
}