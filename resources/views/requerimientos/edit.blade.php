<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar {{ $requerimiento->NUMERO_REQ }} · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Editar Requerimiento</div>
        </header>
        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Editar {{ $requerimiento->NUMERO_REQ }}</h1>
                    <p class="page-subtitle">Modificación de los datos principales del requerimiento.</p>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('requerimientos.show', $requerimiento) }}">Volver al detalle</a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="panel form-card">
                <form action="{{ route('requerimientos.update', $requerimiento) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="span-2 form-group">
                            <label>Tipo de requerimiento</label>
                            <select name="SERIAL_TREQ" required>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->SERIAL_TREQ }}" @selected($requerimiento->SERIAL_TREQ == $tipo->SERIAL_TREQ)>
                                        {{ $tipo->NOMBRE_TREQ }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="span-2 form-group">
                            <label for="SERIAL_DEP_DESTINO">Departamento de destino *</label>
                            <select name="SERIAL_DEP_DESTINO" id="SERIAL_DEP_DESTINO" required>
                                @foreach($departamentos as $dep)
                                    <option value="{{ $dep->SERIAL_DEP }}" @selected(old('SERIAL_DEP_DESTINO', $requerimiento->SERIAL_DEP_DESTINO) == $dep->SERIAL_DEP)>
                                        {{ $dep->DESCRIPCION_DEP }} ({{ $dep->CODIGO_DEP }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="span-2 form-group">
                            <label>Asunto</label>
                            <input type="text" name="ASUNTO_REQ" value="{{ old('ASUNTO_REQ', $requerimiento->ASUNTO_REQ) }}" required>
                        </div>

                        <div class="span-2 form-group">
                            <label>Descripción</label>
                            <textarea name="DESCRIPCION_REQ" rows="6" required>{{ old('DESCRIPCION_REQ', $requerimiento->DESCRIPCION_REQ) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Prioridad</label>
                            <select name="PRIORIDAD_REQ" required>
                                @foreach(['BAJA', 'MEDIA', 'ALTA', 'URGENTE'] as $p)
                                    <option value="{{ $p }}" @selected(old('PRIORIDAD_REQ', $requerimiento->PRIORIDAD_REQ) === $p)>
                                        {{ $p }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Fecha límite</label>
                            <input type="datetime-local" name="FECHA_LIMITE_REQ" value="{{ old('FECHA_LIMITE_REQ', optional($requerimiento->FECHA_LIMITE_REQ)->format('Y-m-d\TH:i')) }}">
                        </div>
                    </div>

                    <div class="actions" style="margin-top:14px">
                        <button type="submit" class="btn btn-gold">Guardar cambios</button>
                        <a href="{{ route('requerimientos.show', $requerimiento) }}" class="btn">Cancelar</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>