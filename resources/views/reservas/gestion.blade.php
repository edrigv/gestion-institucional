<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Gestión de Reservas</div>
            <div class="topbar-right">
                <span class="user-pill">{{ $usuario->nombre_completo }}</span>
            </div>
        </header>
        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Reservas por Gestionar</h1>
                    <p class="page-subtitle">Solicitudes de espacios institucionales bajo tu responsabilidad.</p>
                </div>
                <div class="actions">
                    <a class="btn btn-primary" href="{{ route('reservas.horario') }}">▦ Ver horario</a>
                    <a class="btn" href="{{ route('reservas.index') }}">Mis reservas</a>
                </div>
            </div>

            <form class="filter-bar" method="GET">
                <select name="estado">
                    <option value="">Todos los estados</option>
                    @foreach(['PENDIENTE','APROBADA','RECHAZADA','CANCELADA','FINALIZADA'] as $e)
                        <option value="{{ $e }}" @selected(request('estado') === $e)>{{ $e }}</option>
                    @endforeach
                </select>
                <button type="submit">Filtrar</button>
                <a class="btn" href="{{ route('reservas.gestion') }}">Limpiar</a>
            </form>

            <section class="panel" style="padding:0!important;overflow:hidden">
                @if($reservas->count())
                    <div class="table-wrap" style="border:0;border-radius:0;box-shadow:none">
                        <table>
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Espacio</th>
                                    <th>Solicitante</th>
                                    <th>Actividad</th>
                                    <th>Horario</th>
                                    <th>Conflictos</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservas as $r)
                                    <tr>
                                        <td class="req-number">{{ $r->NUMERO_RES }}</td>
                                        <td><strong>{{ $r->espacio?->NOMBRE_ESP }}</strong></td>
                                        <td>{{ $r->solicitante?->nombre_completo ?? 'Usuario #'.$r->SERIAL_USR_SOLICITA }}</td>
                                        <td>{{ $r->TITULO_RES }}</td>
                                        <td>{{ optional($r->FECHA_INICIO_RES)->format('d/m H:i') }} — {{ optional($r->FECHA_FIN_RES)->format('d/m H:i') }}</td>
                                        <td>
                                            @if($r->cantidad_conflictos)
                                                <span class="badge conflict-badge">⚠ {{ $r->cantidad_conflictos }}</span>
                                            @else
                                                <span class="muted">—</span>
                                            @endif
                                        </td>
                                        <td><span class="badge reserva-{{ $r->ESTADO_RES }}">{{ $r->ESTADO_RES }}</span></td>
                                        <td><a class="btn" href="{{ route('reservas.show', $r) }}">Revisar</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty" style="margin:24px">No hay reservas para mostrar con los filtros seleccionados.</div>
                @endif
            </section>
        </div>
    </main>
</div>
</body>
</html>
