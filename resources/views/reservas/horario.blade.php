<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario de Espacios · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Horario de reservas</div>
            <div class="topbar-right"><span class="user-pill">{{ $usuarioSesion->nombre_completo }}</span></div>
        </header>

        <div class="page-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Horario de espacios</h1>
                    <p class="page-subtitle">Haz clic en cualquier hora de la cuadrícula para reservar rápidamente. Una franja ocupada no impide solicitar el espacio.</p>
                </div>
                <div class="actions">
                    <button type="button" class="btn btn-gold" onclick="abrirModalReserva('{{ $fecha }}', 8)">＋ Nueva reserva</button>
                    <a class="btn" href="{{ route('reservas.index') }}">Mis reservas</a>
                </div>
            </div>

            <form class="filter-bar" method="GET" action="{{ route('reservas.horario') }}">
                <div>
                    <label>Espacio</label>
                    <select name="espacio" onchange="this.form.submit()">
                        @foreach($espacios as $espacio)
                            <option value="{{ $espacio->SERIAL_ESP }}" @selected((int)$espacioId === (int)$espacio->SERIAL_ESP)>
                                {{ $espacio->NOMBRE_ESP }}@if($espacio->UBICACION_ESP) — {{ $espacio->UBICACION_ESP }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Fecha</label>
                    <input type="date" name="fecha" value="{{ $fecha }}" onchange="this.form.submit()">
                </div>
                <div class="agenda-nav-actions">
                    <a class="btn" href="{{ route('reservas.horario', ['espacio'=>$espacioId, 'fecha'=>$dia->copy()->subDay()->format('Y-m-d')]) }}">← Día anterior</a>
                    <a class="btn" href="{{ route('reservas.horario', ['espacio'=>$espacioId, 'fecha'=>now()->format('Y-m-d')]) }}">Hoy</a>
                    <a class="btn" href="{{ route('reservas.horario', ['espacio'=>$espacioId, 'fecha'=>$dia->copy()->addDay()->format('Y-m-d')]) }}">Día siguiente →</a>
                </div>
            </form>

            @if(!$espacioSeleccionado)
                <div class="empty">No hay espacios activos configurados.</div>
            @else
                <section class="panel agenda-panel">
                    <div class="agenda-heading">
                        <div>
                            <h2>{{ $espacioSeleccionado->NOMBRE_ESP }}</h2>
                            <p>{{ ucfirst($dia->locale('es')->translatedFormat('l d \d\e F \d\e Y')) }} · <span style="color:var(--brand);font-weight:700">Haz clic en una hora para reservar</span></p>
                        </div>
                        <div class="agenda-legend">
                            <span><i class="legend-dot approved"></i> Aprobada</span>
                            <span><i class="legend-dot pending"></i> Pendiente</span>
                            <span><i class="legend-dot conflict"></i> Pendiente con conflicto</span>
                        </div>
                    </div>

                    <div class="agenda-scroll">
                        <div class="agenda-day">
                            @foreach($horas as $hora)
                                <div class="agenda-hour-row" onclick="abrirModalReserva('{{ $fecha }}', {{ $hora }})" title="Clic para reservar a las {{ str_pad($hora,2,'0',STR_PAD_LEFT) }}:00">
                                    <div class="agenda-hour-label">{{ str_pad($hora,2,'0',STR_PAD_LEFT) }}:00</div>
                                    <div class="agenda-hour-line">
                                        <span class="agenda-slot-hint">＋ Reservar {{ str_pad($hora,2,'0',STR_PAD_LEFT) }}:00</span>
                                    </div>
                                    <div class="agenda-events">
                                        @foreach($reservas as $reserva)
                                            @php
                                                $inicio = $reserva->FECHA_INICIO_RES->copy();
                                                $fin = $reserva->FECHA_FIN_RES->copy();
                                                $slotStart = $dia->copy()->setTime($hora,0,0);
                                                $slotEnd = $slotStart->copy()->addHour();
                                                $coincide = $inicio < $slotEnd && $fin > $slotStart;
                                                $empiezaAqui = $inicio >= $slotStart && $inicio < $slotEnd;
                                                $continuaDesdeAntes = $hora === 6 && $inicio < $slotStart && $fin > $slotStart;
                                            @endphp
                                            @if($coincide && ($empiezaAqui || $continuaDesdeAntes))
                                                @php
                                                    $visibleInicio = $inicio->greaterThan($dia->copy()->setTime(6,0)) ? $inicio : $dia->copy()->setTime(6,0);
                                                    $visibleFin = $fin->lessThan($dia->copy()->setTime(23,0)) ? $fin : $dia->copy()->setTime(23,0);
                                                    $duracionMin = max(30, $visibleInicio->diffInMinutes($visibleFin));
                                                    $offsetMin = max(0, $slotStart->diffInMinutes($visibleInicio, false));
                                                    $alto = max(42, ($duracionMin / 60) * 64 - 6);
                                                    $top = max(2, ($offsetMin / 60) * 64 + 2);
                                                    $clase = $reserva->ESTADO_RES === 'APROBADA' ? 'approved' : ($reserva->tiene_conflicto ? 'conflict' : 'pending');
                                                @endphp
                                                <a href="{{ route('reservas.show', $reserva) }}" onclick="event.stopPropagation()" class="agenda-event {{ $clase }}" style="top:{{ $top }}px;height:{{ $alto }}px" title="Ver detalle de {{ $reserva->TITULO_RES }}">
                                                    <strong>{{ $reserva->TITULO_RES }}</strong>
                                                    <span>{{ $reserva->FECHA_INICIO_RES->format('H:i') }}–{{ $reserva->FECHA_FIN_RES->format('H:i') }}</span>
                                                    <small>{{ $reserva->solicitante?->nombre_completo ?? 'Usuario' }}</small>
                                                    @if($reserva->tiene_conflicto)<em>⚠ Conflicto</em>@endif
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <h2>Reservas del día</h2>
                    @forelse($reservas as $reserva)
                        <div class="agenda-list-item">
                            <div class="agenda-time">{{ $reserva->FECHA_INICIO_RES->format('H:i') }}<span>–</span>{{ $reserva->FECHA_FIN_RES->format('H:i') }}</div>
                            <div class="agenda-list-body">
                                <strong>{{ $reserva->TITULO_RES }}</strong>
                                <span>{{ $reserva->solicitante?->nombre_completo ?? 'Usuario' }} · {{ $reserva->NUMERO_RES }}</span>
                            </div>
                            <div class="actions">
                                <span class="badge reserva-{{ $reserva->ESTADO_RES }}">{{ $reserva->ESTADO_RES }}</span>
                                @if($reserva->tiene_conflicto)<span class="badge conflict-badge">⚠ CONFLICTO</span>@endif
                                <a class="btn" href="{{ route('reservas.show',$reserva) }}">Ver</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty">No hay reservas aprobadas ni solicitudes pendientes para este espacio en la fecha seleccionada.</div>
                    @endforelse
                </section>
            @endif
        </div>
    </main>
</div>

<!-- Modal Flotante de Reserva Rápida (Estilo Google Calendar) -->
<dialog id="modal-reserva" class="calendar-modal" aria-labelledby="modal-reserva-titulo">
    <form method="POST" action="{{ route('reservas.store') }}" id="modal-reserva-form">
        @csrf
        <div class="calendar-modal-header">
            <div>
                <h3 id="modal-reserva-titulo" class="calendar-modal-title">Nueva solicitud de reserva</h3>
                <p class="calendar-modal-subtitle" id="modal-reserva-subtitulo">Selecciona los detalles para tu espacio</p>
            </div>
            <button type="button" class="calendar-modal-close" onclick="cerrarModalReserva()" aria-label="Cerrar modal">&times;</button>
        </div>
        <div class="calendar-modal-body">
            <div class="form-grid">
                <div class="span-2 form-group">
                    <label for="modal-SERIAL_ESP">Espacio</label>
                    <select name="SERIAL_ESP" id="modal-SERIAL_ESP" required>
                        @foreach($espacios as $espacio)
                            <option value="{{ $espacio->SERIAL_ESP }}" @selected((int)$espacioId === (int)$espacio->SERIAL_ESP)>
                                {{ $espacio->NOMBRE_ESP }}@if($espacio->UBICACION_ESP) — {{ $espacio->UBICACION_ESP }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal-FECHA_INICIO_RES">Desde</label>
                    <input type="datetime-local" name="FECHA_INICIO_RES" id="modal-FECHA_INICIO_RES" required>
                </div>
                <div class="form-group">
                    <label for="modal-FECHA_FIN_RES">Hasta</label>
                    <input type="datetime-local" name="FECHA_FIN_RES" id="modal-FECHA_FIN_RES" required>
                </div>
                <div class="span-2" id="modal-conflicto-box" style="display:none;margin-bottom:16px;"></div>
                <div class="span-2 form-group">
                    <label for="modal-TITULO_RES">Actividad / motivo</label>
                    <input type="text" name="TITULO_RES" id="modal-TITULO_RES" maxlength="180" placeholder="Ej: Conferencia de bienvenida, Capacitación..." required>
                </div>
                <div class="span-2 form-group" style="margin-bottom:0!important">
                    <label for="modal-DESCRIPCION_RES">Descripción adicional (opcional)</label>
                    <textarea name="DESCRIPCION_RES" id="modal-DESCRIPCION_RES" rows="3" placeholder="Detalle de la actividad, recursos requeridos, asistentes previstos..."></textarea>
                </div>
            </div>
        </div>
        <div class="calendar-modal-footer">
            <button type="button" class="btn" onclick="cerrarModalReserva()">Cancelar</button>
            <button type="submit" class="btn btn-gold">Confirmar y solicitar</button>
        </div>
    </form>
</dialog>

<script>
const modalReserva = document.getElementById('modal-reserva');
const csrfToken = document.querySelector('input[name="_token"]')?.value;
const boxConflicto = document.getElementById('modal-conflicto-box');
const inputEspacio = document.getElementById('modal-SERIAL_ESP');
const inputInicio = document.getElementById('modal-FECHA_INICIO_RES');
const inputFin = document.getElementById('modal-FECHA_FIN_RES');
const inputTitulo = document.getElementById('modal-TITULO_RES');
const subtituloModal = document.getElementById('modal-reserva-subtitulo');

function pad(n) { return String(n).padStart(2, '0'); }

function abrirModalReserva(fechaStr, hora) {
    if (!modalReserva) return;
    const inicioStr = `${fechaStr}T${pad(hora)}:00`;
    const horaFin = hora + 1;
    const finStr = horaFin <= 23 ? `${fechaStr}T${pad(horaFin)}:00` : `${fechaStr}T23:59`;
    
    inputInicio.value = inicioStr;
    inputFin.value = finStr;
    subtituloModal.textContent = `Horario propuesto: ${pad(hora)}:00 a ${horaFin <= 23 ? pad(horaFin) + ':00' : '23:59'}`;
    
    modalReserva.showModal();
    verificarConflictoModal();
    setTimeout(() => inputTitulo.focus(), 60);
}

function cerrarModalReserva() {
    if (modalReserva && modalReserva.open) {
        modalReserva.close();
    }
}

if (modalReserva) {
    modalReserva.addEventListener('click', (e) => {
        const rect = modalReserva.getBoundingClientRect();
        if (
            e.clientX < rect.left ||
            e.clientX > rect.right ||
            e.clientY < rect.top ||
            e.clientY > rect.bottom
        ) {
            cerrarModalReserva();
        }
    });
}

async function verificarConflictoModal() {
    const esp = inputEspacio?.value;
    const ini = inputInicio?.value;
    const fin = inputFin?.value;
    if (!esp || !ini || !fin || !boxConflicto) {
        if (boxConflicto) boxConflicto.style.display = 'none';
        return;
    }
    try {
        const r = await fetch(@json(route('reservas.verificar')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ SERIAL_ESP: esp, FECHA_INICIO_RES: ini, FECHA_FIN_RES: fin })
        });
        if (!r.ok) return;
        const d = await r.json();
        boxConflicto.style.display = 'block';
        if (d.hay_conflicto) {
            boxConflicto.className = 'conflict-alert';
            boxConflicto.innerHTML = `<strong>⚠ Horario con ocupación (${d.cantidad} reserva(s) coincidentes).</strong><br>Puedes enviar la solicitud igualmente. El encargado del espacio decidirá si mantiene la anterior o la cancela para aprobar esta.`;
        } else {
            boxConflicto.className = 'availability-ok';
            boxConflicto.innerHTML = '<strong>✓ Horario disponible sin conflictos aprobados.</strong>';
        }
    } catch (e) {
        if (boxConflicto) boxConflicto.style.display = 'none';
    }
}

[inputEspacio, inputInicio, inputFin].forEach(el => {
    if (el) el.addEventListener('change', verificarConflictoModal);
});
</script>
</body>
</html>
