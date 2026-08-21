<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mensaje->ASUNTO_MEN }} · Mensajería Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar"><div class="topbar-title">Mensaje</div><div class="topbar-right"><span class="user-pill">{{ $usuarioActual->nombre_completo }}</span></div></header>
        <div class="page-content">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            <div class="page-header">
                <div><h1 class="page-title">{{ $mensaje->ASUNTO_MEN }}</h1><p class="page-subtitle">{{ optional($mensaje->FECHA_HORA_MEN)->format('d/m/Y H:i') }}</p></div>
                <div class="actions">
                    <a class="btn" href="{{ route('mensajes.index') }}">Bandeja</a>
                    @if((int)$usuarioActual->SERIAL_USR === (int)$mensaje->SERIAL_USR_RECIBE)
                        <a class="btn btn-gold" href="{{ route('mensajes.create', ['destinatario' => $mensaje->SERIAL_USR_ENVIA]) }}">Responder</a>
                    @endif
                </div>
            </div>
            <section class="panel">
                <div class="detail-grid">
                    <div><div class="detail-label">De</div><div class="detail-value">{{ $mensaje->remitente?->nombre_completo ?? 'Usuario #'.$mensaje->SERIAL_USR_ENVIA }}</div></div>
                    <div><div class="detail-label">Para</div><div class="detail-value">{{ $mensaje->destinatario?->nombre_completo ?? 'Usuario #'.$mensaje->SERIAL_USR_RECIBE }}</div></div>
                    <div><div class="detail-label">Estado</div><div class="detail-value"><span class="badge {{ $mensaje->FECHA_LECTURA_MEN ? 'estado-ATENDIDO' : 'estado-ENVIADO' }}">{{ $mensaje->FECHA_LECTURA_MEN ? 'LEÍDO' : 'ENVIADO' }}</span></div></div>
                    @if($mensaje->SERIAL_REQ)
                        <div><div class="detail-label">Requerimiento relacionado</div><div class="detail-value"><a href="{{ route('requerimientos.show', $mensaje->SERIAL_REQ) }}">#{{ $mensaje->SERIAL_REQ }}</a></div></div>
                    @endif
                </div>
                <hr style="border:0;border-top:1px solid var(--line);margin:22px 0">
                <div style="white-space:pre-wrap;line-height:1.75;color:#27324a">{{ $mensaje->CONTENIDO_MEN }}</div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
