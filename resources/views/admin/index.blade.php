<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
    <style>
        .admin-tabs {
            display: flex;
            gap: 6px;
            background: var(--surface);
            padding: 6px;
            border-radius: 12px;
            border: 1px solid var(--line);
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            overflow-x: auto;
        }
        .admin-tab-btn {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            padding: 10px 18px !important;
            border-radius: 9px !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: var(--muted) !important;
            background: transparent !important;
            border: 1px solid transparent !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            white-space: nowrap !important;
        }
        .admin-tab-btn:hover {
            background: var(--surface-hover) !important;
            color: var(--ink) !important;
        }
        .admin-tab-btn.active {
            background: var(--brand) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 51, 153, 0.22) !important;
        }
        .admin-tab-btn.active .tab-count {
            background: var(--gold);
            color: #1a2238;
        }
        .tab-count {
            padding: 2px 7px;
            font-size: 11px;
            border-radius: 999px;
            background: var(--surface-hover);
            color: var(--muted);
            border: 1px solid var(--line);
            font-weight: 800;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.18s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    <script src="{{ asset('js/theme.js') }}"></script>
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Centro de Administración y Configuración</div>
            <div class="topbar-right">
                <button type="button" class="theme-toggle-btn" onclick="toggleTheme()" title="Alternar modo claro / oscuro" aria-label="Alternar tema">
                    <span class="theme-toggle-icon">🌙</span>
                </button>
                <span class="user-pill">Modo Administrador</span>
            </div>
        </header>
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
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
                    <h1 class="page-title">Panel de Administración</h1>
                    <p class="page-subtitle">Gestión centralizada de espacios, trámites del catálogo, departamentos y personal de la institución.</p>
                </div>
            </div>

            <!-- TARJETAS DE RESUMEN GLOBAL -->
            <div class="card-grid">
                <div class="stat-card" style="cursor:pointer" onclick="switchAdminTab('espacios')">
                    <div class="stat-value">{{ $stats['totalEspacios'] }}</div>
                    <div class="stat-label">Espacios Reservables</div>
                </div>
                <div class="stat-card gold" style="cursor:pointer" onclick="switchAdminTab('tipos')">
                    <div class="stat-value">{{ $stats['totalTipos'] }}</div>
                    <div class="stat-label">Tipos de Trámite</div>
                </div>
                <div class="stat-card green" style="cursor:pointer" onclick="switchAdminTab('departamentos')">
                    <div class="stat-value">{{ $stats['totalDepartamentos'] }}</div>
                    <div class="stat-label">Departamentos Plantel</div>
                </div>
                <div class="stat-card gray" style="cursor:pointer" onclick="switchAdminTab('usuarios')">
                    <div class="stat-value">{{ $stats['totalUsuarios'] }}</div>
                    <div class="stat-label">Personal Registrado</div>
                </div>
            </div>

            <!-- SELECTOR DE PESTAÑAS (TABS) -->
            <div class="admin-tabs">
                <button class="admin-tab-btn active" id="btn-tab-espacios" onclick="switchAdminTab('espacios')">
                    <span>🏢 Espacios Reservables</span>
                    <span class="tab-count">{{ $stats['totalEspacios'] }}</span>
                </button>
                <button class="admin-tab-btn" id="btn-tab-tipos" onclick="switchAdminTab('tipos')">
                    <span>📋 Catálogo de Requerimientos</span>
                    <span class="tab-count">{{ $stats['totalTipos'] }}</span>
                </button>
                <button class="admin-tab-btn" id="btn-tab-departamentos" onclick="switchAdminTab('departamentos')">
                    <span>🏛 Departamentos Institucionales</span>
                    <span class="tab-count">{{ $stats['totalDepartamentos'] }}</span>
                </button>
                <button class="admin-tab-btn" id="btn-tab-usuarios" onclick="switchAdminTab('usuarios')">
                    <span>👥 Directorio de Personal</span>
                    <span class="tab-count">{{ $stats['totalUsuarios'] }}</span>
                </button>
            </div>

            <!-- PESTAÑA 1: ESPACIOS -->
            <div id="tab-espacios" class="tab-content active">
                <div class="page-header">
                    <div>
                        <h2 style="margin:0">Espacios Institucionales</h2>
                        <div class="muted" style="font-size:13px;margin-top:4px">Lugares para eventos, clases especiales y reuniones del plantel.</div>
                    </div>
                    <div class="actions">
                        <button class="btn btn-gold" type="button" onclick="abrirModalNuevoEspacio()">＋ Nuevo espacio</button>
                        <a href="{{ route('reservas.horario') }}" class="btn btn-primary">▦ Ver agenda</a>
                    </div>
                </div>

                <section class="panel" style="padding:0!important;overflow:hidden">
                    <div class="table-wrap" style="border:0;border-radius:0;box-shadow:none">
                        <table>
                            <thead>
                                <tr>
                                    <th>Espacio</th>
                                    <th>Ubicación</th>
                                    <th>Capacidad</th>
                                    <th>Encargado</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($espacios as $e)
                                    <tr>
                                        <td><strong>{{ $e->NOMBRE_ESP }}</strong></td>
                                        <td>{{ $e->UBICACION_ESP ?? '—' }}</td>
                                        <td>{{ $e->CAPACIDAD_ESP ? $e->CAPACIDAD_ESP.' personas' : '—' }}</td>
                                        <td>{{ $e->encargado?->nombre_completo ?? 'Sin asignar' }}</td>
                                        <td>
                                            <span class="badge {{ $e->ESTADO_ESP === 'ACTIVO' ? 'estado-APROBADO' : 'estado-ANULADO' }}">
                                                {{ $e->ESTADO_ESP }}
                                            </span>
                                        </td>
                                        <td>
                                            <button
                                                class="btn"
                                                type="button"
                                                onclick="abrirModalEditarEspacio({
                                                    id: {{ $e->SERIAL_ESP }},
                                                    nombre: '{{ addslashes($e->NOMBRE_ESP) }}',
                                                    descripcion: '{{ addslashes($e->DESCRIPCION_ESP ?? '') }}',
                                                    ubicacion: '{{ addslashes($e->UBICACION_ESP ?? '') }}',
                                                    capacidad: '{{ $e->CAPACIDAD_ESP ?? '' }}',
                                                    encargado: '{{ $e->SERIAL_USR_ENCARGADO ?? '' }}',
                                                    estado: '{{ $e->ESTADO_ESP }}',
                                                    action: '{{ route('espacios.update', $e) }}'
                                                })"
                                            >Editar</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="empty">No hay espacios configurados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <!-- PESTAÑA 2: TIPOS DE REQUERIMIENTO -->
            <div id="tab-tipos" class="tab-content">
                <div class="page-header">
                    <div>
                        <h2 style="margin:0">Catálogo de Tipos de Requerimiento</h2>
                        <div class="muted" style="font-size:13px;margin-top:4px">Definición de trámites institucionales, flujos de aprobación y firma.</div>
                    </div>
                    <div class="actions">
                        <button class="btn btn-gold" type="button" onclick="abrirModalNuevoTipo()">＋ Nuevo tipo de trámite</button>
                    </div>
                </div>

                <section class="panel" style="padding:0!important;overflow:hidden">
                    <div class="table-wrap" style="border:0;border-radius:0;box-shadow:none">
                        <table>
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre del Trámite</th>
                                    <th>Departamento Asignado</th>
                                    <th>Firma</th>
                                    <th>Aprobación</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tipos as $t)
                                    <tr>
                                        <td class="req-number">#{{ $t->SERIAL_TREQ }}</td>
                                        <td><strong>{{ $t->NOMBRE_TREQ }}</strong></td>
                                        <td>{{ $t->departamento?->DESCRIPCION_DEP ?? 'General' }}</td>
                                        <td>
                                            <span class="badge {{ $t->REQUIERE_FIRMA_TREQ ? 'estado-APROBADO' : 'estado-ANULADO' }}">
                                                {{ $t->REQUIERE_FIRMA_TREQ ? 'Sí' : 'No' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $t->REQUIERE_APROBACION_TREQ ? 'estado-APROBADO' : 'estado-ANULADO' }}">
                                                {{ $t->REQUIERE_APROBACION_TREQ ? 'Sí' : 'No' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $t->ESTADO_TREQ === 'ACTIVO' ? 'estado-APROBADO' : 'estado-ANULADO' }}">
                                                {{ $t->ESTADO_TREQ }}
                                            </span>
                                        </td>
                                        <td>
                                            <button
                                                class="btn"
                                                type="button"
                                                onclick="abrirModalEditarTipo({
                                                    id: {{ $t->SERIAL_TREQ }},
                                                    nombre: '{{ addslashes($t->NOMBRE_TREQ) }}',
                                                    descripcion: '{{ addslashes($t->DESCRIPCION_TREQ ?? '') }}',
                                                    departamento: '{{ $t->SERIAL_DEP ?? '' }}',
                                                    firma: {{ $t->REQUIERE_FIRMA_TREQ ? 'true' : 'false' }},
                                                    aprobacion: {{ $t->REQUIERE_APROBACION_TREQ ? 'true' : 'false' }},
                                                    estado: '{{ $t->ESTADO_TREQ }}',
                                                    action: '{{ route('tipos-requerimiento.update', $t) }}'
                                                })"
                                            >Editar</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="empty">No hay tipos de requerimiento registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <!-- PESTAÑA 3: DEPARTAMENTOS INSTITUCIONALES -->
            <div id="tab-departamentos" class="tab-content">
                <div class="page-header">
                    <div>
                        <h2 style="margin:0">Departamentos Institucionales</h2>
                        <div class="muted" style="font-size:13px;margin-top:4px">Áreas académicas, administrativas y pastorales del plantel.</div>
                    </div>
                    <div class="actions">
                        <button class="btn btn-gold" type="button" onclick="abrirModalNuevoDepartamento()">＋ Nuevo departamento</button>
                    </div>
                </div>

                <section class="panel" style="padding:0!important;overflow:hidden">
                    <div class="table-wrap" style="border:0;border-radius:0;box-shadow:none">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Nombre del Departamento</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departamentos as $dep)
                                    <tr>
                                        <td class="req-number">#{{ $dep->SERIAL_DEP }}</td>
                                        <td><span class="badge estado-RECIBIDO">{{ $dep->CODIGO_DEP }}</span></td>
                                        <td><strong>{{ $dep->DESCRIPCION_DEP }}</strong></td>
                                        <td><span class="badge estado-APROBADO">ACTIVO</span></td>
                                        <td>
                                            <button
                                                class="btn"
                                                type="button"
                                                onclick="abrirModalEditarDepartamento({
                                                    id: {{ $dep->SERIAL_DEP }},
                                                    codigo: '{{ addslashes($dep->CODIGO_DEP) }}',
                                                    descripcion: '{{ addslashes($dep->DESCRIPCION_DEP) }}',
                                                    action: '{{ route('departamentos.update', $dep) }}'
                                                })"
                                            >Editar</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="empty">No hay departamentos registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <!-- PESTAÑA 4: DIRECTORIO DE PERSONAL Y USUARIOS -->
            <div id="tab-usuarios" class="tab-content">
                <div class="page-header">
                    <div>
                        <h2 style="margin:0">Directorio Institucional de Usuarios</h2>
                        <div class="muted" style="font-size:13px;margin-top:4px">Cuentas activas, roles y asignación de personal docente y administrativo.</div>
                    </div>
                </div>

                <section class="panel" style="padding:0!important;overflow:hidden">
                    <div class="table-wrap" style="border:0;border-radius:0;box-shadow:none">
                        <table>
                            <thead>
                                <tr>
                                    <th>Código Usuario</th>
                                    <th>Nombre Completo</th>
                                    <th>Perfil / Rol</th>
                                    <th>Correo Institucional</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($usuarios as $usr)
                                    <tr>
                                        <td><span class="req-number">{{ $usr->CODIGO_USR }}</span></td>
                                        <td><strong>{{ $usr->nombre_completo }}</strong></td>
                                        <td>
                                            <span class="badge {{ strtoupper($usr->perfil?->CODIGO_PFL ?? '') === 'ADM' ? 'estado-DERIVADO' : 'estado-RECIBIDO' }}">
                                                {{ $usr->perfil?->NOMBRE_PFL ?? 'Sin perfil' }}
                                            </span>
                                        </td>
                                        <td>{{ $usr->EMAIL_USR ?? $usr->empleado?->EMAIL_EPL ?? '—' }}</td>
                                        <td>
                                            <span class="badge {{ strtoupper($usr->ESTADO_USR ?? 'ACTIVO') === 'ACTIVO' ? 'estado-APROBADO' : 'estado-ANULADO' }}">
                                                {{ $usr->ESTADO_USR ?? 'ACTIVO' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="empty">No hay usuarios registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

        </div>
    </main>
</div>

<!-- =========================================================================
     MODALES FLOTANTES INSTITUCIONALES (ESPACIOS, TIPOS, DEPARTAMENTOS)
     ========================================================================= -->

<!-- MODAL ESPACIO -->
<dialog class="calendar-modal" id="modalEspacio" style="max-width: 620px;">
    <div class="calendar-modal-header">
        <div>
            <h3 class="calendar-modal-title" id="modalEspacioTitulo">Nuevo Espacio Reservable</h3>
            <p class="calendar-modal-subtitle" id="modalEspacioSubtitulo">Registro y configuración de lugares</p>
        </div>
        <button class="calendar-modal-close" type="button" onclick="cerrarModal('modalEspacio')">✕</button>
    </div>
    <form id="formEspacio" method="POST" action="{{ route('espacios.store') }}">
        @csrf
        <div id="methodEspacio"></div>
        <div class="calendar-modal-body">
            <div class="form-grid">
                <div class="span-2 form-group">
                    <label for="espNombre">Nombre del espacio *</label>
                    <input type="text" id="espNombre" name="NOMBRE_ESP" maxlength="120" placeholder="Ej. Auditorio San Ignacio, Cancha Sintética..." required>
                </div>
                <div class="form-group">
                    <label for="espUbicacion">Ubicación</label>
                    <input type="text" id="espUbicacion" name="UBICACION_ESP" placeholder="Ej. Bloque A - 2do Piso">
                </div>
                <div class="form-group">
                    <label for="espCapacidad">Capacidad (personas)</label>
                    <input type="number" id="espCapacidad" name="CAPACIDAD_ESP" min="1" max="10000" placeholder="Ej. 80">
                </div>
                <div class="form-group">
                    <label for="espEncargado">Usuario encargado</label>
                    <select id="espEncargado" name="SERIAL_USR_ENCARGADO">
                        <option value="">Sin asignar</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->SERIAL_USR }}">{{ $u->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="espEstado">Estado *</label>
                    <select id="espEstado" name="ESTADO_ESP" required>
                        <option value="ACTIVO">ACTIVO</option>
                        <option value="INACTIVO">INACTIVO</option>
                    </select>
                </div>
                <div class="span-2 form-group">
                    <label for="espDescripcion">Descripción o equipamiento</label>
                    <textarea id="espDescripcion" name="DESCRIPCION_ESP" rows="3" placeholder="Detalla proyector, audio, aire acondicionado..."></textarea>
                </div>
            </div>
        </div>
        <div class="calendar-modal-footer">
            <button type="button" class="btn" onclick="cerrarModal('modalEspacio')">Cancelar</button>
            <button type="submit" class="btn btn-gold" id="btnGuardarEspacio">Guardar espacio</button>
        </div>
    </form>
</dialog>

<!-- MODAL TIPO DE REQUERIMIENTO -->
<dialog class="calendar-modal" id="modalTipo" style="max-width: 620px;">
    <div class="calendar-modal-header">
        <div>
            <h3 class="calendar-modal-title" id="modalTipoTitulo">Nuevo Tipo de Requerimiento</h3>
            <p class="calendar-modal-subtitle" id="modalTipoSubtitulo">Definición de trámites institucionales</p>
        </div>
        <button class="calendar-modal-close" type="button" onclick="cerrarModal('modalTipo')">✕</button>
    </div>
    <form id="formTipo" method="POST" action="{{ route('tipos-requerimiento.store') }}">
        @csrf
        <div id="methodTipo"></div>
        <div class="calendar-modal-body">
            <div class="form-grid">
                <div class="span-2 form-group">
                    <label for="tipoNombre">Nombre del trámite *</label>
                    <input type="text" id="tipoNombre" name="NOMBRE_TREQ" maxlength="100" placeholder="Ej. Solicitud de Certificados, Soporte Técnico..." required>
                </div>
                <div class="span-2 form-group">
                    <label for="tipoDepartamento">Departamento Responsable</label>
                    <select id="tipoDepartamento" name="SERIAL_DEP">
                        <option value="">Sin departamento específico (General)</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->SERIAL_DEP }}">{{ $dep->DESCRIPCION_DEP }} ({{ $dep->CODIGO_DEP }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="span-2 form-group">
                    <label for="tipoDescripcion">Descripción del trámite</label>
                    <textarea id="tipoDescripcion" name="DESCRIPCION_TREQ" rows="3" placeholder="Breve explicación del propósito de este tipo de requerimiento..."></textarea>
                </div>
                <div class="span-2" style="display:flex;gap:24px;margin-bottom:14px">
                    <label class="checkbox-row" style="cursor:pointer">
                        <input type="checkbox" id="tipoFirma" name="REQUIERE_FIRMA_TREQ" value="1">
                        <span>Requiere firma digital / física</span>
                    </label>
                    <label class="checkbox-row" style="cursor:pointer">
                        <input type="checkbox" id="tipoAprobacion" name="REQUIERE_APROBACION_TREQ" value="1">
                        <span>Requiere aprobación previa</span>
                    </label>
                </div>
                <div class="span-2 form-group" id="divEstadoTipo" style="display:none">
                    <label for="tipoEstado">Estado *</label>
                    <select id="tipoEstado" name="ESTADO_TREQ">
                        <option value="ACTIVO">ACTIVO</option>
                        <option value="INACTIVO">INACTIVO</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="calendar-modal-footer">
            <button type="button" class="btn" onclick="cerrarModal('modalTipo')">Cancelar</button>
            <button type="submit" class="btn btn-gold" id="btnGuardarTipo">Guardar trámite</button>
        </div>
    </form>
</dialog>

<!-- MODAL DEPARTAMENTO -->
<dialog class="calendar-modal" id="modalDepartamento" style="max-width: 540px;">
    <div class="calendar-modal-header">
        <div>
            <h3 class="calendar-modal-title" id="modalDepTitulo">Nuevo Departamento</h3>
            <p class="calendar-modal-subtitle">Estructura organizativa del plantel</p>
        </div>
        <button class="calendar-modal-close" type="button" onclick="cerrarModal('modalDepartamento')">✕</button>
    </div>
    <form id="formDepartamento" method="POST" action="{{ route('departamentos.store') }}">
        @csrf
        <div id="methodDep"></div>
        <div class="calendar-modal-body">
            <div class="form-grid">
                <div class="span-2 form-group">
                    <label for="depCodigo">Código del departamento *</label>
                    <input type="text" id="depCodigo" name="CODIGO_DEP" maxlength="20" placeholder="Ej. PAS, REC, VIC, SEC, LAB..." required>
                    <small class="helper">Abreviatura o sigla institucional única.</small>
                </div>
                <div class="span-2 form-group">
                    <label for="depDescripcion">Nombre / Descripción del departamento *</label>
                    <input type="text" id="depDescripcion" name="DESCRIPCION_DEP" maxlength="120" placeholder="Ej. Pastoral e Identidad, Rectorado, Secretaría..." required>
                </div>
            </div>
        </div>
        <div class="calendar-modal-footer">
            <button type="button" class="btn" onclick="cerrarModal('modalDepartamento')">Cancelar</button>
            <button type="submit" class="btn btn-gold" id="btnGuardarDep">Guardar departamento</button>
        </div>
    </form>
</dialog>

<script>
// Manejo de Pestañas (Tabs)
function switchAdminTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.admin-tab-btn').forEach(el => el.classList.remove('active'));

    const targetTab = document.getElementById('tab-' + tabName);
    const targetBtn = document.getElementById('btn-tab-' + tabName);

    if (targetTab && targetBtn) {
        targetTab.classList.add('active');
        targetBtn.classList.add('active');
        window.location.hash = tabName;
    }
}

// Cargar pestaña según el hash de URL si existe
window.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash.replace('#', '');
    if (hash && document.getElementById('tab-' + hash)) {
        switchAdminTab(hash);
    }
});

// Utilidad para cerrar modales
function cerrarModal(modalId) {
    document.getElementById(modalId).close();
}

// Cierre al hacer click en el backdrop
['modalEspacio', 'modalTipo', 'modalDepartamento'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('click', (e) => { if (e.target === el) el.close(); });
    }
});

// --- LÓGICA MODAL ESPACIOS ---
function abrirModalNuevoEspacio() {
    const form = document.getElementById('formEspacio');
    form.action = "{{ route('espacios.store') }}";
    document.getElementById('methodEspacio').innerHTML = "";
    document.getElementById('modalEspacioTitulo').textContent = "Nuevo Espacio Reservable";
    document.getElementById('modalEspacioSubtitulo').textContent = "Registro y configuración de lugares";
    document.getElementById('btnGuardarEspacio').textContent = "Crear espacio";

    document.getElementById('espNombre').value = "";
    document.getElementById('espUbicacion').value = "";
    document.getElementById('espCapacidad').value = "";
    document.getElementById('espEncargado').value = "";
    document.getElementById('espEstado').value = "ACTIVO";
    document.getElementById('espDescripcion').value = "";

    document.getElementById('modalEspacio').showModal();
}

function abrirModalEditarEspacio(data) {
    const form = document.getElementById('formEspacio');
    form.action = data.action;
    document.getElementById('methodEspacio').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('modalEspacioTitulo').textContent = "Editar Espacio";
    document.getElementById('modalEspacioSubtitulo').textContent = "Modificación de características: " + data.nombre;
    document.getElementById('btnGuardarEspacio').textContent = "Guardar cambios";

    document.getElementById('espNombre').value = data.nombre;
    document.getElementById('espUbicacion').value = data.ubicacion;
    document.getElementById('espCapacidad').value = data.capacidad;
    document.getElementById('espEncargado').value = data.encargado;
    document.getElementById('espEstado').value = data.estado;
    document.getElementById('espDescripcion').value = data.descripcion;

    document.getElementById('modalEspacio').showModal();
}

// --- LÓGICA MODAL TIPOS DE TRÁMITE ---
function abrirModalNuevoTipo() {
    const form = document.getElementById('formTipo');
    form.action = "{{ route('tipos-requerimiento.store') }}";
    document.getElementById('methodTipo').innerHTML = "";
    document.getElementById('modalTipoTitulo').textContent = "Nuevo Tipo de Requerimiento";
    document.getElementById('modalTipoSubtitulo').textContent = "Definición de trámites institucionales";
    document.getElementById('btnGuardarTipo').textContent = "Crear tipo de trámite";
    document.getElementById('divEstadoTipo').style.display = "none";

    document.getElementById('tipoNombre').value = "";
    document.getElementById('tipoDepartamento').value = "";
    document.getElementById('tipoDescripcion').value = "";
    document.getElementById('tipoFirma').checked = false;
    document.getElementById('tipoAprobacion').checked = false;

    document.getElementById('modalTipo').showModal();
}

function abrirModalEditarTipo(data) {
    const form = document.getElementById('formTipo');
    form.action = data.action;
    document.getElementById('methodTipo').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('modalTipoTitulo').textContent = "Editar Tipo de Trámite";
    document.getElementById('modalTipoSubtitulo').textContent = "Modificación de requisitos: " + data.nombre;
    document.getElementById('btnGuardarTipo').textContent = "Guardar cambios";
    document.getElementById('divEstadoTipo').style.display = "block";

    document.getElementById('tipoNombre').value = data.nombre;
    document.getElementById('tipoDepartamento').value = data.departamento;
    document.getElementById('tipoDescripcion').value = data.descripcion;
    document.getElementById('tipoFirma').checked = data.firma;
    document.getElementById('tipoAprobacion').checked = data.aprobacion;
    document.getElementById('tipoEstado').value = data.estado;

    document.getElementById('modalTipo').showModal();
}

// --- LÓGICA MODAL DEPARTAMENTOS ---
function abrirModalNuevoDepartamento() {
    const form = document.getElementById('formDepartamento');
    form.action = "{{ route('departamentos.store') }}";
    document.getElementById('methodDep').innerHTML = "";
    document.getElementById('modalDepTitulo').textContent = "Nuevo Departamento Institucional";
    document.getElementById('btnGuardarDep').textContent = "Crear departamento";

    document.getElementById('depCodigo').value = "";
    document.getElementById('depDescripcion').value = "";

    document.getElementById('modalDepartamento').showModal();
}

function abrirModalEditarDepartamento(data) {
    const form = document.getElementById('formDepartamento');
    form.action = data.action;
    document.getElementById('methodDep').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('modalDepTitulo').textContent = "Editar Departamento";
    document.getElementById('btnGuardarDep').textContent = "Guardar cambios";

    document.getElementById('depCodigo').value = data.codigo;
    document.getElementById('depDescripcion').value = data.descripcion;

    document.getElementById('modalDepartamento').showModal();
}
</script>
</body>
</html>
