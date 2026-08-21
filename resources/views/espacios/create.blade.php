<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Espacio · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Nuevo Espacio</div>
        </header>
        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Crear Espacio Reservable</h1>
                    <p class="page-subtitle">Registra un nuevo espacio institucional y asigna al responsable de su gestión.</p>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('espacios.index') }}">Volver al listado</a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="panel form-card">
                <form method="POST" action="{{ route('espacios.store') }}">
                    @csrf
                    @include('espacios.partials.form', ['espacio' => null])
                    <div class="actions">
                        <button class="btn btn-gold" type="submit">Crear espacio</button>
                        <a class="btn" href="{{ route('espacios.index') }}">Cancelar</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>
