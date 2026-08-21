<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes Enviados · Mensajería Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar"><div class="topbar-title">Mensajería interna</div><div class="topbar-right"><span class="user-pill">{{ $usuarioActual->nombre_completo }}</span></div></header>
        <div class="page-content">
            <div class="page-header">
                <div><h1 class="page-title">Mensajes enviados</h1><p class="page-subtitle">Comunicaciones enviadas desde tu cuenta.</p></div>
                <div class="actions">
                    <a href="{{ route('mensajes.create') }}" class="btn btn-gold">＋ Nuevo mensaje</a>
                    <a href="{{ route('mensajes.index') }}" class="btn">Bandeja de entrada</a>
                </div>
            </div>
            <section class="panel" style="padding:0!important;overflow:hidden">
                @if($mensajes->count())
                    <div class="table-wrap" style="border:0;border-radius:0;box-shadow:none">
                        <table>
                            <thead><tr><th>Para</th><th>Asunto</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
                            <tbody>
                            @foreach($mensajes as $mensaje)
                                <tr>
                                    <td>{{ $mensaje->destinatario?->nombre_completo ?? 'Usuario #'.$mensaje->SERIAL_USR_RECIBE }}</td>
                                    <td><strong>{{ $mensaje->ASUNTO_MEN }}</strong></td>
                                    <td><span class="badge {{ $mensaje->FECHA_LECTURA_MEN ? 'estado-ATENDIDO' : 'estado-ENVIADO' }}">{{ $mensaje->FECHA_LECTURA_MEN ? 'LEÍDO' : 'ENVIADO' }}</span></td>
                                    <td>{{ optional($mensaje->FECHA_HORA_MEN)->format('d/m/Y H:i') }}</td>
                                    <td><a class="btn" href="{{ route('mensajes.show', $mensaje) }}">Ver</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty" style="margin:20px">Todavía no has enviado mensajes.</div>
                @endif
            </section>
        </div>
    </main>
</div>
</body>
</html>
