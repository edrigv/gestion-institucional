<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de Requerimiento · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Tipos de Requerimiento</div>
        </header>
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="page-header">
                <div>
                    <h1 class="page-title">Tipos de Requerimiento</h1>
                    <p class="page-subtitle">Catálogo institucional de trámites, flujos de aprobación y firmas requeridas.</p>
                </div>
                <div class="actions">
                    <a href="{{ route('tipos-requerimiento.create') }}" class="btn btn-gold">
                        ＋ Nuevo tipo de requerimiento
                    </a>
                </div>
            </div>

            <section class="panel" style="padding:0!important;overflow:hidden">
                <div class="table-wrap" style="border:0;border-radius:0;box-shadow:none">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Departamento</th>
                                <th>Descripción</th>
                                <th>Requiere Firma</th>
                                <th>Requiere Aprobación</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tipos as $tipo)
                                <tr>
                                    <td class="req-number">#{{ $tipo->SERIAL_TREQ }}</td>
                                    <td><strong>{{ $tipo->NOMBRE_TREQ }}</strong></td>
                                    <td>{{ $tipo->departamento?->DESCRIPCION_DEP ?? 'General' }}</td>
                                    <td>{{ $tipo->DESCRIPCION_TREQ ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $tipo->REQUIERE_FIRMA_TREQ ? 'estado-APROBADO' : 'estado-ANULADO' }}">
                                            {{ $tipo->REQUIERE_FIRMA_TREQ ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $tipo->REQUIERE_APROBACION_TREQ ? 'estado-APROBADO' : 'estado-ANULADO' }}">
                                            {{ $tipo->REQUIERE_APROBACION_TREQ ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $tipo->ESTADO_TREQ === 'ACTIVO' ? 'estado-APROBADO' : 'estado-ANULADO' }}">
                                            {{ $tipo->ESTADO_TREQ }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty">
                                        No existen tipos de requerimiento registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>