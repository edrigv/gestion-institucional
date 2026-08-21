<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Requerimiento · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Nuevo Requerimiento</div>
        </header>
        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Crear Nuevo Requerimiento</h1>
                    <p class="page-subtitle">Ingresa la solicitud para derivación y trámite institucional.</p>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('requerimientos.index') }}">Volver al listado</a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Revisa los siguientes campos:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="panel form-card">
                <form action="{{ route('requerimientos.store') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Usuario solicitante</label>
                            <input type="text" value="{{ $usuarioSesion->nombre_completo }}" readonly>
                            <small>Se obtiene automáticamente de la sesión iniciada.</small>
                        </div>

                        <div class="form-group">
                            <label>Departamento de origen</label>
                            <input type="text" value="Automático según el usuario institucional" readonly>
                        </div>

                        <div class="span-2 form-group">
                            <label for="SERIAL_DEP_DESTINO">Departamento de destino *</label>
                            <select name="SERIAL_DEP_DESTINO" id="SERIAL_DEP_DESTINO" required>
                                <option value="">Seleccione el departamento destinatario...</option>
                                @foreach($departamentos as $dep)
                                    <option value="{{ $dep->SERIAL_DEP }}" @selected(old('SERIAL_DEP_DESTINO') == $dep->SERIAL_DEP)>
                                        {{ $dep->DESCRIPCION_DEP }} ({{ $dep->CODIGO_DEP }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="span-2 form-group">
                            <label>Tipo de requerimiento</label>
                            <select name="SERIAL_TREQ" required>
                                <option value="">Seleccione un tipo...</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->SERIAL_TREQ }}" @selected(old('SERIAL_TREQ') == $tipo->SERIAL_TREQ)>
                                        {{ $tipo->NOMBRE_TREQ }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="span-2 form-group">
                            <label>Asunto</label>
                            <input type="text" name="ASUNTO_REQ" value="{{ old('ASUNTO_REQ') }}" placeholder="Resumen conciso del requerimiento..." required>
                        </div>

                        <div class="span-2 form-group">
                            <label>Descripción detallada</label>
                            <textarea name="DESCRIPCION_REQ" rows="6" placeholder="Describe los antecedentes, justificación y detalles de la solicitud..." required>{{ old('DESCRIPCION_REQ') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Prioridad</label>
                            <select name="PRIORIDAD_REQ" required>
                                <option value="BAJA" @selected(old('PRIORIDAD_REQ') === 'BAJA')>Baja</option>
                                <option value="MEDIA" @selected(old('PRIORIDAD_REQ', 'MEDIA') === 'MEDIA')>Media</option>
                                <option value="ALTA" @selected(old('PRIORIDAD_REQ') === 'ALTA')>Alta</option>
                                <option value="URGENTE" @selected(old('PRIORIDAD_REQ') === 'URGENTE')>Urgente</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Fecha límite (opcional)</label>
                            <input type="datetime-local" name="FECHA_LIMITE_REQ" value="{{ old('FECHA_LIMITE_REQ') }}">
                        </div>
                    </div>

                    <div class="actions" style="margin-top:14px">
                        <button type="submit" class="btn btn-gold">Crear requerimiento</button>
                        <a href="{{ route('requerimientos.index') }}" class="btn">Cancelar</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>