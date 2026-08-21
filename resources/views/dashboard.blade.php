<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-brand-indicator">
                <div class="topbar-title">Panel Principal</div>
            </div>
            <div class="topbar-right">
                <button type="button" class="theme-toggle-btn" onclick="toggleTheme()" title="Alternar modo claro / oscuro" aria-label="Alternar tema">
                    <span class="theme-toggle-icon">🌙</span>
                </button>
                <span class="user-pill">U.E. Particular Cristo Rey</span>
            </div>
        </header>
        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Gestión Institucional</h1>
                    <p class="page-subtitle">Resumen general de requerimientos, trámites internos y reservas de espacios.</p>
                </div>
                <div class="actions">
                    <a href="{{ route('requerimientos.create') }}" class="btn btn-gold">＋ Nuevo requerimiento</a>
                    <a href="{{ route('requerimientos.index') }}" class="btn">Ver requerimientos</a>
                    <a href="{{ route('reservas.horario') }}" class="btn btn-primary">▦ Horario y reservas</a>
                </div>
            </div>

            <div class="card-grid">
                <div class="stat-card"><div class="stat-value">{{ $total }}</div><div class="stat-label">Total requerimientos</div></div>
                <div class="stat-card green"><div class="stat-value">{{ $enProceso }}</div><div class="stat-label">En proceso</div></div>
                <div class="stat-card orange"><div class="stat-value">{{ $pendientesAprobacion }}</div><div class="stat-label">Pendientes aprobación</div></div>
                <div class="stat-card gold"><div class="stat-value">{{ $pendientesFirma }}</div><div class="stat-label">Pendientes de firma</div></div>
                <div class="stat-card gray"><div class="stat-value">{{ $cerrados }}</div><div class="stat-label">Cerrados / Atendidos</div></div>
            </div>

            <section class="panel" style="padding:0!important;overflow:hidden">
                <div style="padding:20px 24px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:12px;background:var(--surface)">
                    <div>
                        <h2 style="margin:0">Últimos Requerimientos</h2>
                        <div class="muted" style="font-size:12.5px;margin-top:4px">Actividad reciente registrada en el sistema institucional</div>
                    </div>
                    <a href="{{ route('requerimientos.index') }}" class="btn">Consultar listado completo</a>
                </div>
                @if($ultimos->count())
                    <div class="table-wrap" style="border:0;border-radius:0;box-shadow:none">
                        <table>
                            <thead><tr><th>Número</th><th>Asunto</th><th>Tipo</th><th>Prioridad</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
                            <tbody>
                            @foreach($ultimos as $requerimiento)
                                <tr>
                                    <td class="req-number">{{ $requerimiento->NUMERO_REQ }}</td>
                                    <td class="truncate">{{ $requerimiento->ASUNTO_REQ }}</td>
                                    <td>{{ $requerimiento->tipo->NOMBRE_TREQ ?? 'Sin tipo' }}</td>
                                    <td><span class="priority priority-{{ $requerimiento->PRIORIDAD_REQ }}">{{ $requerimiento->PRIORIDAD_REQ }}</span></td>
                                    <td><span class="badge estado-{{ $requerimiento->ESTADO_REQ }}">{{ str_replace('_', ' ', $requerimiento->ESTADO_REQ) }}</span></td>
                                    <td>{{ optional($requerimiento->FECHA_CREACION_REQ)->format('d/m/Y H:i') }}</td>
                                    <td><a href="{{ route('requerimientos.show', $requerimiento) }}" class="btn">Ver</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty" style="margin:24px">Todavía no existen requerimientos registrados.</div>
                @endif
            </section>
        </div>
    </main>
</div>
</body>
</html>