<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Reserva · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Nueva Reserva de Espacio</div>
        </header>
        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Solicitar un Espacio</h1>
                    <p class="page-subtitle">Aunque el horario esté ocupado, podrás enviar la solicitud. El encargado institucional resolverá cualquier conflicto.</p>
                </div>
                <div class="actions">
                    <a class="btn btn-primary" href="{{ route('reservas.horario') }}">▦ Consultar horario</a>
                    <a class="btn" href="{{ route('reservas.index') }}">Volver al listado</a>
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
                <form method="POST" action="{{ route('reservas.store') }}" id="reserva-form">
                    @csrf
                    <div class="form-grid">
                        <div class="span-2 form-group">
                            <label>Espacio Institucional</label>
                            <select name="SERIAL_ESP" id="SERIAL_ESP" required>
                                <option value="">Seleccione un espacio...</option>
                                @foreach($espacios as $espacio)
                                    <option value="{{ $espacio->SERIAL_ESP }}" @selected(old('SERIAL_ESP') == $espacio->SERIAL_ESP)>
                                        {{ $espacio->NOMBRE_ESP }}@if($espacio->UBICACION_ESP) — {{ $espacio->UBICACION_ESP }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fecha y hora de inicio</label>
                            <input type="datetime-local" name="FECHA_INICIO_RES" id="FECHA_INICIO_RES" value="{{ old('FECHA_INICIO_RES') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Fecha y hora de finalización</label>
                            <input type="datetime-local" name="FECHA_FIN_RES" id="FECHA_FIN_RES" value="{{ old('FECHA_FIN_RES') }}" required>
                        </div>
                        <div class="span-2" id="conflicto-box" style="display:none"></div>
                        <div class="span-2 form-group">
                            <label>Actividad / Motivo</label>
                            <input type="text" name="TITULO_RES" maxlength="180" placeholder="Ej. Reunión de área pedagógica, Evento pastoral..." value="{{ old('TITULO_RES') }}" required>
                        </div>
                        <div class="span-2 form-group">
                            <label>Descripción / Requerimientos adicionales</label>
                            <textarea name="DESCRIPCION_RES" rows="5" placeholder="Detalle de la actividad, recursos necesarios (proyector, sillas, audio), asistentes estimados...">{{ old('DESCRIPCION_RES') }}</textarea>
                        </div>
                    </div>
                    <div class="actions">
                        <button class="btn btn-gold" type="submit">Enviar solicitud de reserva</button>
                        <a class="btn" href="{{ route('reservas.index') }}">Cancelar</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>

<script>
const token = document.querySelector('input[name="_token"]').value;
const box = document.getElementById('conflicto-box');

async function verificar() {
    const esp = document.getElementById('SERIAL_ESP').value;
    const ini = document.getElementById('FECHA_INICIO_RES').value;
    const fin = document.getElementById('FECHA_FIN_RES').value;
    
    if(!esp || !ini || !fin) {
        box.style.display = 'none';
        return;
    }
    
    try {
        const r = await fetch(@json(route('reservas.verificar')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ SERIAL_ESP: esp, FECHA_INICIO_RES: ini, FECHA_FIN_RES: fin })
        });
        if(!r.ok) return;
        const d = await r.json();
        box.style.display = 'block';
        if(d.hay_conflicto) {
            box.className = 'conflict-alert';
            box.innerHTML = '<strong>⚠ Horario ocupado.</strong><br>Existe' + (d.cantidad > 1 ? 'n ' : ' ') + d.cantidad + ' reserva(s) aprobada(s) en este intervalo. <b>Puedes enviar la solicitud de todos modos.</b> El encargado evaluará y resolverá la prioridad institucional.';
        } else {
            box.className = 'availability-ok';
            box.innerHTML = '<strong>✓ Espacio disponible en el horario seleccionado.</strong>';
        }
    } catch(e) {
        box.style.display = 'none';
    }
}

['SERIAL_ESP','FECHA_INICIO_RES','FECHA_FIN_RES'].forEach(id => {
    document.getElementById(id).addEventListener('change', verificar);
});
</script>
</body>
</html>
