<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Mensaje · Mensajería Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar"><div class="topbar-title">Mensajería interna</div><div class="topbar-right"><span class="user-pill">De: {{ $usuarioActual->nombre_completo }}</span></div></header>
        <div class="page-content">
            <div class="page-header">
                <div><h1 class="page-title">Nuevo mensaje</h1><p class="page-subtitle">El remitente se obtiene automáticamente de tu sesión.</p></div>
                <div class="actions"><a href="{{ route('mensajes.index') }}" class="btn">Volver</a></div>
            </div>
            @if($errors->any())
                <div class="alert alert-error"><strong>No se pudo enviar:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <section class="panel form-card">
                <form method="POST" action="{{ route('mensajes.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Remitente</label>
                            <input type="text" value="{{ $usuarioActual->nombre_completo }}{{ $usuarioActual->perfil ? ' — '.$usuarioActual->perfil->NOMBRE_PFL : '' }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Usuario destino</label>
                            <select name="SERIAL_USR_RECIBE" required>
                                <option value="">Seleccione...</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->SERIAL_USR }}" @selected((string)old('SERIAL_USR_RECIBE', $destinatario) === (string)$usuario->SERIAL_USR)>
                                        {{ $usuario->nombre_completo }}{{ $usuario->perfil ? ' — '.$usuario->perfil->NOMBRE_PFL : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group span-2">
                            <label>Asunto</label>
                            <input type="text" name="ASUNTO_MEN" maxlength="180" value="{{ old('ASUNTO_MEN') }}" required>
                        </div>
                        <div class="form-group span-2">
                            <label>Mensaje</label>
                            <textarea name="CONTENIDO_MEN" rows="8" required>{{ old('CONTENIDO_MEN') }}</textarea>
                        </div>
                        <div class="form-group span-2">
                            <label>Requerimiento relacionado (opcional)</label>
                            <input type="number" name="SERIAL_REQ" min="1" value="{{ old('SERIAL_REQ', $serialReq) }}" placeholder="Ej. 2">
                        </div>
                    </div>
                    <div class="actions">
                        <button class="btn-primary" type="submit">Enviar mensaje</button>
                        <a class="btn" href="{{ route('mensajes.index') }}">Cancelar</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>
