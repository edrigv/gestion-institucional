<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva {{ $reserva->NUMERO_RES }} · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Detalle de Reserva</div>
        </header>
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ $reserva->NUMERO_RES }}</h1>
                    <p class="page-subtitle">{{ $reserva->TITULO_RES }}</p>
                </div>
                <div class="actions">
                    <span class="badge reserva-{{ $reserva->ESTADO_RES }}">{{ $reserva->ESTADO_RES }}</span>
                    <a class="btn btn-primary" href="{{ route('reservas.horario', ['espacio' => $reserva->SERIAL_ESP, 'fecha' => optional($reserva->FECHA_INICIO_RES)->format('Y-m-d')]) }}">▦ Ver en Horario</a>
                    @if($esSolicitante)
                        <a class="btn" href="{{ route('reservas.index') }}">Mis reservas</a>
                    @elseif($puedeGestionar)
                        <a class="btn" href="{{ route('reservas.gestion') }}">Gestionar reservas</a>
                    @endif
                </div>
            </div>

            <section class="panel">
                <div class="detail-grid">
                    <div class="fila"><span class="etiqueta">Espacio</span><strong>{{ $reserva->espacio?->NOMBRE_ESP }}</strong></div>
                    <div class="fila"><span class="etiqueta">Solicitante</span>{{ $reserva->solicitante?->nombre_completo ?? 'Usuario #'.$reserva->SERIAL_USR_SOLICITA }}</div>
                    <div class="fila"><span class="etiqueta">Encargado</span>{{ $reserva->espacio?->encargado?->nombre_completo ?? 'Sin asignar' }}</div>
                    <div class="fila"><span class="etiqueta">Desde</span>{{ optional($reserva->FECHA_INICIO_RES)->format('d/m/Y H:i') }}</div>
                    <div class="fila"><span class="etiqueta">Hasta</span>{{ optional($reserva->FECHA_FIN_RES)->format('d/m/Y H:i') }}</div>
                    <div class="fila"><span class="etiqueta">Fecha de solicitud</span>{{ optional($reserva->FECHA_CREACION_RES)->format('d/m/Y H:i') }}</div>
                </div>
                @if($reserva->DESCRIPCION_RES)
                    <hr>
                    <div>
                        <span class="etiqueta">Descripción / Requerimientos</span>
                        <p style="margin:4px 0 0;line-height:1.5">{{ $reserva->DESCRIPCION_RES }}</p>
                    </div>
                @endif
            </section>

            @if(!$esSolicitante && !$puedeGestionar)
                <section class="panel" style="background:var(--brand-soft)!important;border-color:var(--brand-border)!important">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px">
                        <div>
                            <h3 style="margin:0 0 4px;color:var(--brand)">¿Necesitas este espacio en este mismo horario?</h3>
                            <p style="margin:0;font-size:13px;color:var(--muted)">Puedes registrar tu solicitud. El sistema registrará el conflicto y el encargado del espacio o administrador decidirá cuál aprobar.</p>
                        </div>
                        <a href="{{ route('reservas.horario', ['espacio' => $reserva->SERIAL_ESP, 'fecha' => optional($reserva->FECHA_INICIO_RES)->format('Y-m-d')]) }}" class="btn btn-gold">
                            ▦ Ir al Horario para solicitar
                        </a>
                    </div>
                </section>
            @endif

            @if($conflictos->isNotEmpty())
                <section class="panel conflict-panel">
                    <h2>⚠ Conflicto de horario</h2>
                    <p style="color:#92400e;margin:0 0 12px;font-size:13px">La solicitud coincide con {{ $conflictos->count() }} reserva(s) actualmente aprobada(s):</p>
                    @foreach($conflictos as $c)
                        <div class="conflict-item">
                            <div><strong>{{ $c->NUMERO_RES }}</strong> · {{ $c->solicitante?->nombre_completo ?? 'Usuario' }} ({{ $c->TITULO_RES }})</div>
                            <div>{{ optional($c->FECHA_INICIO_RES)->format('d/m/Y H:i') }} — {{ optional($c->FECHA_FIN_RES)->format('d/m/Y H:i') }}</div>
                        </div>
                    @endforeach
                </section>
            @endif

            @if($puedeGestionar && $reserva->ESTADO_RES === 'PENDIENTE')
                <section class="panel">
                    <h2>Resolución de la solicitud</h2>
                    @if($conflictos->isEmpty())
                        <form method="POST" action="{{ route('reservas.aprobar', $reserva) }}">
                            @csrf
                            <div class="form-group">
                                <label>Observación opcional</label>
                                <textarea name="observacion" rows="3" placeholder="Comentarios de aprobación..."></textarea>
                            </div>
                            <button class="btn btn-success" type="submit">✓ Aprobar reserva</button>
                        </form>
                        <hr>
                    @endif

                    <form method="POST" action="{{ route('reservas.rechazar', $reserva) }}">
                        @csrf
                        <div class="form-group">
                            <label>Motivo del rechazo (obligatorio)</label>
                            <textarea name="observacion" rows="3" placeholder="Indica el motivo por el cual no se puede aprobar..." required></textarea>
                        </div>
                        <button class="btn btn-danger" type="submit">Rechazar solicitud</button>
                    </form>

                    @if($conflictos->isNotEmpty())
                        <hr>
                        <div class="danger-zone">
                            <h3>Cancelar reserva(s) anterior(es) y aprobar esta solicitud</h3>
                            <p>Esta acción cancelará todas las reservas aprobadas que coinciden con este horario y aprobará <strong>{{ $reserva->NUMERO_RES }}</strong>. La operación quedará registrada en el historial de cada reserva.</p>
                            <form method="POST" action="{{ route('reservas.reemplazar', $reserva) }}" onsubmit="return confirm('¿Confirmas que deseas cancelar las reservas anteriores en conflicto y aprobar esta solicitud prioritaria?');">
                                @csrf
                                <div class="form-group">
                                    <label>Justificación obligatoria de prioridad institucional</label>
                                    <textarea name="observacion" rows="3" placeholder="Detalla la razón por la cual se da prioridad a esta reserva..." required></textarea>
                                </div>
                                <button class="btn btn-danger" type="submit">Cancelar anteriores y aprobar esta</button>
                            </form>
                        </div>
                    @endif
                </section>
            @endif

            @if($esSolicitante && in_array($reserva->ESTADO_RES, ['PENDIENTE', 'APROBADA']))
                <section class="panel">
                    <h2>Cancelar mi solicitud</h2>
                    <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}" onsubmit="return confirm('¿Deseas cancelar esta reserva?');">
                        @csrf
                        <div class="form-group">
                            <label>Motivo de cancelación</label>
                            <textarea name="observacion" rows="3" placeholder="Explica por qué cancelas la reserva..." required></textarea>
                        </div>
                        <button class="btn btn-danger" type="submit">Cancelar reserva</button>
                    </form>
                </section>
            @endif

            <section class="panel">
                <h2>Historial de Movimientos</h2>
                @forelse($reserva->movimientos->sortByDesc('FECHA_HORA_MRES') as $m)
                    <div class="movimiento">
                        <strong>{{ str_replace('_', ' ', $m->ACCION_MRES) }}</strong><br>
                        <span class="muted">{{ optional($m->FECHA_HORA_MRES)->format('d/m/Y H:i:s') }} · {{ $m->usuario?->nombre_completo ?? 'Usuario #'.$m->SERIAL_USR }}</span>
                        @if($m->ESTADO_ANTERIOR_MRES || $m->ESTADO_NUEVO_MRES)
                            <br><span style="font-size:12px;font-weight:700">{{ $m->ESTADO_ANTERIOR_MRES ?? '—' }} → {{ $m->ESTADO_NUEVO_MRES ?? '—' }}</span>
                        @endif
                        @if($m->OBSERVACION_MRES)
                            <p style="margin:6px 0 0;font-size:13px">{{ $m->OBSERVACION_MRES }}</p>
                        @endif
                    </div>
                @empty
                    <div class="empty">Sin movimientos registrados.</div>
                @endforelse
            </section>
        </div>
    </main>
</div>
</body>
</html>
