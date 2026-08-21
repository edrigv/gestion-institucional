<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
</head>
<body class="login-page">
<button type="button" class="theme-toggle-btn" style="position:fixed;top:18px;right:18px;z-index:99;background:rgba(255,255,255,0.15)!important;color:#fff!important;border:1px solid rgba(255,255,255,0.25)!important" onclick="toggleTheme()" title="Alternar modo claro / oscuro" aria-label="Alternar tema">
    <span class="theme-toggle-icon">🌙</span>
</button>
<div class="login-shell">
    <section class="login-card">
        <div class="login-brand">
            <div class="login-logo-container">
                <img src="{{ asset('images/logo-cristo-rey.png') }}" class="login-logo-img" alt="Unidad Educativa Particular Cristo Rey">
            </div>
            <div>
                <h1>Gestión Institucional</h1>
                <p>Plataforma de Requerimientos, Mensajería y Espacios</p>
            </div>
        </div>

        <div class="login-copy">
            <h2>Acceso al Sistema</h2>
            <p>Ingresa con tus credenciales institucionales.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="form-group">
                <label for="codigo">Código de Usuario</label>
                <input id="codigo" type="text" name="codigo" value="{{ old('codigo') }}" autocomplete="username" placeholder="Ej. ADMIN, DOCENTE..." autofocus required>
            </div>
            <div class="form-group">
                <label for="clave">Contraseña</label>
                <input id="clave" type="password" name="clave" autocomplete="current-password" placeholder="••••••••" required>
            </div>
            <button class="btn-primary login-submit" type="submit">Ingresar a la Plataforma</button>
        </form>

        <div class="login-help">
            <em>"Formando hombres y mujeres para los demás"</em>
            Unidad Educativa Particular Cristo Rey · Portoviejo, Manabí, Ecuador
        </div>
    </section>
</div>
</body>
</html>
