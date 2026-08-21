<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Reservas de Espacios</div>
            <div class="topbar-right">
                <span class="user-pill">{{ $usuario->nombre_completo }}</span>
            </div>
        </header>
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <div class="page-header">
                <div>
                    <h1 class="page-title">Mis Reservas</h1>
                    <p class="page-subtitle">Historial y estado de las solicitudes de espacios institucionales realizadas con tu cuenta.</p>
                </div>
                <div class="actions">
                    <a class="btn btn-primary" href="{{ route('reservas.horario') }}">▦ Reservar en el horario</a>
                    <a class="btn" href="{{ route('reservas.gestion') }}">Reservas por gestionar</a>
                </div>
            </div>

            <section class="panel" style="padding:0!important;overflow:hidden">
                @if($reservas->count())
                    <div class="table-wrap" style="border:0;border-radius:0;box-shadow:none">
                        <table>
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Espacio</th>
                                    <th>Actividad</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservas as $reserva)
                                    <tr>
                                        <td class="req-number">{{ $reserva->NUMERO_RES }}</td>
                                        <td><strong>{{ $reserva->espacio?->NOMBRE_ESP ?? 'Espacio' }}</strong></td>
                                        <td>{{ $reserva->TITULO_RES }}</td>
                                        <td>{{ optional($reserva->FECHA_INICIO_RES)->format('d/m/Y H:i') }}</td>
                                        <td>{{ optional($reserva->FECHA_FIN_RES)->format('d/m/Y H:i') }}</td>
                                        <td><span class="badge reserva-{{ $reserva->ESTADO_RES }}">{{ $reserva->ESTADO_RES }}</span></td>
                                        <td><a class="btn" href="{{ route('reservas.show', $reserva) }}">Ver detalle</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty" style="margin:24px">Todavía no has solicitado reservas de espacios institucionales.</div>
                @endif
            </section>
        </div>
    </main>
</div>
</body>
</html>
