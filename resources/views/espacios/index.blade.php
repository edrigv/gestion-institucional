<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espacios Institucionales · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Configuración de Espacios</div>
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
                    <h1 class="page-title">Espacios Reservables</h1>
                    <p class="page-subtitle">Administración de lugares, capacidades y personal encargado del plantel.</p>
                </div>
                <div class="actions">
                    <button class="btn btn-gold" type="button" onclick="abrirModalNuevoEspacio()">＋ Nuevo espacio</button>
                </div>
            </div>

            <section class="panel" style="padding:0!important;overflow:hidden">
                @if($espacios->count())
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
                                @foreach($espacios as $e)
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty" style="margin:24px">No hay espacios configurados en la institución.</div>
                @endif
            </section>
        </div>
    </main>
</div>

<!-- MODAL FLOTANTE INSTITUCIONAL PARA CREAR / EDITAR ESPACIO -->
<dialog class="calendar-modal" id="modalEspacio" style="max-width: 620px;">
    <div class="calendar-modal-header">
        <div>
            <h3 class="calendar-modal-title" id="modalTitulo">Nuevo Espacio Reservable</h3>
            <p class="calendar-modal-subtitle" id="modalSubtitulo">Registro y configuración de lugares del plantel</p>
        </div>
        <button class="calendar-modal-close" type="button" onclick="cerrarModalEspacio()">✕</button>
    </div>

    <form id="formEspacio" method="POST" action="{{ route('espacios.store') }}">
        @csrf
        <div id="methodContainer"></div>

        <div class="calendar-modal-body">
            <div class="form-grid">
                <div class="span-2 form-group">
                    <label for="inputNombre">Nombre del espacio *</label>
                    <input type="text" id="inputNombre" name="NOMBRE_ESP" maxlength="120" placeholder="Ej. Auditorio San Ignacio, Cancha Sintética..." required>
                </div>

                <div class="form-group">
                    <label for="inputUbicacion">Ubicación</label>
                    <input type="text" id="inputUbicacion" name="UBICACION_ESP" placeholder="Ej. Bloque A - 2do Piso">
                </div>

                <div class="form-group">
                    <label for="inputCapacidad">Capacidad (personas)</label>
                    <input type="number" id="inputCapacidad" name="CAPACIDAD_ESP" min="1" max="10000" placeholder="Ej. 80">
                </div>

                <div class="form-group">
                    <label for="selectEncargado">Usuario encargado</label>
                    <select id="selectEncargado" name="SERIAL_USR_ENCARGADO">
                        <option value="">Sin asignar</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->SERIAL_USR }}">{{ $u->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="selectEstado">Estado *</label>
                    <select id="selectEstado" name="ESTADO_ESP" required>
                        <option value="ACTIVO">ACTIVO</option>
                        <option value="INACTIVO">INACTIVO</option>
                    </select>
                </div>

                <div class="span-2 form-group">
                    <label for="textareaDescripcion">Descripción o equipamiento disponible</label>
                    <textarea id="textareaDescripcion" name="DESCRIPCION_ESP" rows="3" placeholder="Detalla recursos disponibles como proyector, audio, aire acondicionado..."></textarea>
                </div>
            </div>
        </div>

        <div class="calendar-modal-footer">
            <button type="button" class="btn" onclick="cerrarModalEspacio()">Cancelar</button>
            <button type="submit" class="btn btn-gold" id="btnGuardar">Guardar espacio</button>
        </div>
    </form>
</dialog>

<script>
const modalEspacio = document.getElementById('modalEspacio');
const formEspacio = document.getElementById('formEspacio');
const methodContainer = document.getElementById('methodContainer');
const modalTitulo = document.getElementById('modalTitulo');
const modalSubtitulo = document.getElementById('modalSubtitulo');
const btnGuardar = document.getElementById('btnGuardar');

const inputNombre = document.getElementById('inputNombre');
const inputUbicacion = document.getElementById('inputUbicacion');
const inputCapacidad = document.getElementById('inputCapacidad');
const selectEncargado = document.getElementById('selectEncargado');
const selectEstado = document.getElementById('selectEstado');
const textareaDescripcion = document.getElementById('textareaDescripcion');

function abrirModalNuevoEspacio() {
    formEspacio.action = "{{ route('espacios.store') }}";
    methodContainer.innerHTML = "";
    modalTitulo.textContent = "Nuevo Espacio Reservable";
    modalSubtitulo.textContent = "Registro y configuración de lugares del plantel";
    btnGuardar.textContent = "Crear espacio";

    inputNombre.value = "";
    inputUbicacion.value = "";
    inputCapacidad.value = "";
    selectEncargado.value = "";
    selectEstado.value = "ACTIVO";
    textareaDescripcion.value = "";

    modalEspacio.showModal();
}

function abrirModalEditarEspacio(data) {
    formEspacio.action = data.action;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    modalTitulo.textContent = "Editar Espacio";
    modalSubtitulo.textContent = "Modificación de características: " + data.nombre;
    btnGuardar.textContent = "Guardar cambios";

    inputNombre.value = data.nombre;
    inputUbicacion.value = data.ubicacion;
    inputCapacidad.value = data.capacidad;
    selectEncargado.value = data.encargado;
    selectEstado.value = data.estado;
    textareaDescripcion.value = data.descripcion;

    modalEspacio.showModal();
}

function cerrarModalEspacio() {
    modalEspacio.close();
}

modalEspacio.addEventListener('click', function(e) {
    if (e.target === modalEspacio) {
        cerrarModalEspacio();
    }
});
</script>
</body>
</html>
