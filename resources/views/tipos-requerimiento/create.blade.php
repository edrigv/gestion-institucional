<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Tipo de Requerimiento · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Nuevo Tipo de Requerimiento</div>
        </header>
        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Registrar Tipo de Requerimiento</h1>
                    <p class="page-subtitle">Define un nuevo trámite en el catálogo y sus requisitos de firma o aprobación.</p>
                </div>
                <div class="actions">
                    <a href="{{ route('tipos-requerimiento.index') }}" class="btn">Volver al catálogo</a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Revisa los siguientes datos:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="panel form-card">
                <form method="POST" action="{{ route('tipos-requerimiento.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="span-2 form-group">
                            <label for="NOMBRE_TREQ">Nombre del trámite / requerimiento</label>
                            <input type="text" id="NOMBRE_TREQ" name="NOMBRE_TREQ" value="{{ old('NOMBRE_TREQ') }}" placeholder="Ej. Solicitud de Certificados, Permiso de Salida..." required>
                        </div>

                        <div class="span-2 form-group">
                            <label for="DESCRIPCION_TREQ">Descripción</label>
                            <textarea id="DESCRIPCION_TREQ" name="DESCRIPCION_TREQ" rows="4" placeholder="Breve explicación del propósito de este tipo de requerimiento...">{{ old('DESCRIPCION_TREQ') }}</textarea>
                        </div>

                        <div class="span-2 form-group">
                            <label for="SERIAL_DEP">Departamento Responsable</label>
                            <select id="SERIAL_DEP" name="SERIAL_DEP">
                                <option value="">Sin departamento específico (General)</option>
                                @foreach($departamentos as $dep)
                                    <option value="{{ $dep->SERIAL_DEP }}" @selected(old('SERIAL_DEP') == $dep->SERIAL_DEP)>
                                        {{ $dep->DESCRIPCION_DEP }} ({{ $dep->CODIGO_DEP }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="span-2" style="display:flex;gap:24px;margin-bottom:18px">
                            <label class="checkbox-row" style="cursor:pointer">
                                <input type="checkbox" name="REQUIERE_FIRMA_TREQ" value="1" @checked(old('REQUIERE_FIRMA_TREQ'))>
                                <span>Requiere firma digital o física</span>
                            </label>

                            <label class="checkbox-row" style="cursor:pointer">
                                <input type="checkbox" name="REQUIERE_APROBACION_TREQ" value="1" @checked(old('REQUIERE_APROBACION_TREQ'))>
                                <span>Requiere aprobación previa</span>
                            </label>
                        </div>
                    </div>

                    <div class="actions" style="margin-top:10px">
                        <button type="submit" class="btn btn-gold">Guardar tipo de requerimiento</button>
                        <a href="{{ route('tipos-requerimiento.index') }}" class="btn">Cancelar</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>