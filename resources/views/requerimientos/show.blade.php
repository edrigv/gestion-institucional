<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requerimiento {{ $requerimiento->NUMERO_REQ }} · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Detalle de Requerimiento</div>
        </header>
        <div class="page-content">

    {{-- MENSAJES --}}

    @if(session('success'))

        <div class="mensaje">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="error">
            {{ session('error') }}
        </div>

    @endif


    @if($errors->any())

        <div class="error">

            <strong>
                Revisa los siguientes errores:
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ENCABEZADO --}}

    <div class="page-header"><div><h1 class="page-title">{{ $requerimiento->NUMERO_REQ }}</h1><p class="page-subtitle">{{ $requerimiento->ASUNTO_REQ }}</p></div><div class="actions"><span class="badge estado-{{ $requerimiento->ESTADO_REQ }}">{{ str_replace('_', ' ', $requerimiento->ESTADO_REQ) }}</span><span class="priority priority-{{ $requerimiento->PRIORIDAD_REQ }}">{{ $requerimiento->PRIORIDAD_REQ }}</span></div></div>


    {{-- INFORMACIÓN GENERAL --}}

    <div class="bloque">

        <div class="fila">

            <span class="etiqueta">
                Asunto:
            </span>

            {{ $requerimiento->ASUNTO_REQ }}

        </div>


        <div class="fila">

            <span class="etiqueta">
                Tipo:
            </span>

            {{ $requerimiento->tipo->NOMBRE_TREQ ?? 'Sin tipo' }}

        </div>


        <div class="fila">

            <span class="etiqueta">
                Descripción:
            </span>

            {{ $requerimiento->DESCRIPCION_REQ }}

        </div>


        <div class="fila">

            <span class="etiqueta">
                Prioridad:
            </span>

            {{ $requerimiento->PRIORIDAD_REQ }}

        </div>


        <div class="fila">

            <span class="etiqueta">
                Estado:
            </span>

            <span class="badge estado-{{ $requerimiento->ESTADO_REQ }}">{{ str_replace('_', ' ', $requerimiento->ESTADO_REQ) }}</span>

        </div>


        <div class="fila">

            <span class="etiqueta">
                Usuario solicitante:
            </span>

            {{ $requerimiento->SERIAL_USR_SOLICITA }}

        </div>


        <div class="fila">
            <span class="etiqueta">
                Departamento origen:
            </span>
            {{ $requerimiento->departamentoOrigen?->DESCRIPCION_DEP ?? ($requerimiento->SERIAL_DEP_ORIGEN ? 'Departamento #'.$requerimiento->SERIAL_DEP_ORIGEN : 'No definido') }}
        </div>

        <div class="fila">
            <span class="etiqueta">
                Departamento destino:
            </span>
            {{ $requerimiento->departamentoDestino?->DESCRIPCION_DEP ?? ('Departamento #'.$requerimiento->SERIAL_DEP_DESTINO) }}
        </div>


        <div class="fila">

            <span class="etiqueta">
                Responsable:
            </span>

            {{ $requerimiento->SERIAL_USR_RESPONSABLE ?? 'Sin asignar' }}

        </div>


        <div class="fila">

            <span class="etiqueta">
                Fecha de creación:
            </span>

            {{
                optional(
                    $requerimiento->FECHA_CREACION_REQ
                )->format('d/m/Y H:i')
            }}

        </div>


        @if($requerimiento->FECHA_LIMITE_REQ)

            <div class="fila">

                <span class="etiqueta">
                    Fecha límite:
                </span>

                {{
                    optional(
                        $requerimiento->FECHA_LIMITE_REQ
                    )->format('d/m/Y H:i')
                }}

            </div>

        @endif


        @if($requerimiento->FECHA_CIERRE_REQ)

            <div class="fila">

                <span class="etiqueta">
                    Fecha de cierre:
                </span>

                {{
                    optional(
                        $requerimiento->FECHA_CIERRE_REQ
                    )->format('d/m/Y H:i')
                }}

            </div>

        @endif


        @if($requerimiento->OBSERVACION_CIERRE_REQ)

            <div class="fila">

                <span class="etiqueta">
                    Observación de cierre:
                </span>

                {{ $requerimiento->OBSERVACION_CIERRE_REQ }}

            </div>

        @endif

    </div>


    {{-- BOTONES GENERALES --}}

    @if(!in_array(
        $requerimiento->ESTADO_REQ,
        ['CERRADO', 'ANULADO']
    ))

        <a
            href="{{ route(
                'requerimientos.edit',
                $requerimiento
            ) }}"
            class="btn"
        >
            Editar
        </a>

    @endif


    <a
        href="{{ route('requerimientos.index') }}"
        class="btn"
    >
        Volver
    </a>
	
	<a
		href="{{ route('dashboard') }}"
		class="btn"
	>
		Inicio
	</a>



    {{-- GESTIÓN DEL REQUERIMIENTO --}}

    <h2>
        Gestión del requerimiento
    </h2>

    <div class="bloque">

        <p>

            <strong>
                Estado actual:
            </strong>

            <span class="badge estado-{{ $requerimiento->ESTADO_REQ }}">{{ str_replace('_', ' ', $requerimiento->ESTADO_REQ) }}</span>
        </p>


        {{-- DERIVAR --}}

        @if(!in_array(
            $requerimiento->ESTADO_REQ,
            ['CERRADO', 'ANULADO', 'RECHAZADO']
        ))

            <hr>

            <h3>
                Derivar requerimiento
            </h3>

            <form
                action="{{ route(
                    'requerimientos.derivar',
                    $requerimiento
                ) }}"
                method="POST"
            >

                @csrf

                <p>
                    <label>
                        Departamento destino
                    </label>
                    <br>
                    <select name="SERIAL_DEP_DESTINO" required>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->SERIAL_DEP }}" @selected($requerimiento->SERIAL_DEP_DESTINO == $dep->SERIAL_DEP)>
                                {{ $dep->DESCRIPCION_DEP }} ({{ $dep->CODIGO_DEP }})
                            </option>
                        @endforeach
                    </select>
                </p>


                <p>

                    <label>
                        Usuario destino
                    </label>

                    <br>

                    <input
                        type="number"
                        name="SERIAL_USR_DESTINO"
                        min="1"
                    >

                </p>


                <p>

                    <label>
                        Observación
                    </label>

                    <br>

                    <textarea
                        name="OBSERVACION_MOV"
                        rows="3"
                        placeholder="Motivo o instrucción de la derivación..."
                    ></textarea>

                </p>


                <button type="submit">
                    Derivar
                </button>

            </form>

        @endif



        {{-- ASIGNAR RESPONSABLE --}}

        @if(!in_array(
            $requerimiento->ESTADO_REQ,
            ['CERRADO', 'ANULADO', 'RECHAZADO']
        ))

            <hr>

            <h3>
                Asignar responsable
            </h3>

            <form
                action="{{ route(
                    'requerimientos.asignar',
                    $requerimiento
                ) }}"
                method="POST"
            >

                @csrf

                <p>

                    <label>
                        ID del responsable
                    </label>

                    <br>

                    <input
                        type="number"
                        name="SERIAL_USR_RESPONSABLE"
                        min="1"
                        required
                    >

                </p>


                <p>

                    <label>
                        Observación
                    </label>

                    <br>

                    <textarea
                        name="OBSERVACION_MOV"
                        rows="3"
                        placeholder="Observación de asignación..."
                    ></textarea>

                </p>


                <button type="submit">
                    Asignar responsable
                </button>

            </form>

        @endif



        {{-- CAMBIAR ESTADO --}}

        @if(!in_array(
            $requerimiento->ESTADO_REQ,
            ['CERRADO', 'ANULADO']
        ))

            <hr>

            <h3>
                Cambiar estado
            </h3>

            <form
                action="{{ route(
                    'requerimientos.cambiar-estado',
                    $requerimiento
                ) }}"
                method="POST"
            >

                @csrf

                <p>

                    <select
                        name="ESTADO_REQ"
                        required
                    >

                        <option value="">
                            Seleccione...
                        </option>

                        <option value="RECIBIDO">
                            Recibido
                        </option>

                        <option value="DERIVADO">
                            Derivado
                        </option>

                        <option value="ASIGNADO">
                            Asignado
                        </option>

                        <option value="EN_PROCESO">
                            En proceso
                        </option>

                        <option value="PENDIENTE_CORRECCION">
                            Pendiente de corrección
                        </option>

                        <option value="PENDIENTE_APROBACION">
                            Pendiente de aprobación
                        </option>

                        <option value="APROBADO">
                            Aprobado
                        </option>

                        <option value="PENDIENTE_FIRMA">
                            Pendiente de firma
                        </option>

                        <option value="FIRMADO">
                            Firmado
                        </option>

                        <option value="ATENDIDO">
                            Atendido
                        </option>

                        <option value="CERRADO">
                            Cerrado
                        </option>

                        <option value="RECHAZADO">
                            Rechazado
                        </option>

                    </select>

                </p>


                <p>

                    <textarea
                        name="OBSERVACION_MOV"
                        rows="3"
                        placeholder="Observación del cambio..."
                    ></textarea>

                </p>


                <button type="submit">
                    Cambiar estado
                </button>

            </form>

        @endif



        {{-- SOLICITAR CORRECCIÓN --}}

        @if(in_array(
            $requerimiento->ESTADO_REQ,
            [
                'RECIBIDO',
                'DERIVADO',
                'ASIGNADO',
                'EN_PROCESO'
            ]
        ))

            <hr>

            <h3>
                Solicitar corrección
            </h3>

            <form
                action="{{ route(
                    'requerimientos.solicitar-correccion',
                    $requerimiento
                ) }}"
                method="POST"
            >

                @csrf

                <p>

                    <textarea
                        name="OBSERVACION_MOV"
                        rows="4"
                        placeholder="Indique qué debe corregirse..."
                        required
                    ></textarea>

                </p>


                <button type="submit">
                    Solicitar corrección
                </button>

            </form>

        @endif



        {{-- REGISTRAR CORRECCIÓN --}}

        @if(
            $requerimiento->ESTADO_REQ
            === 'PENDIENTE_CORRECCION'
        )

            <hr>

            <h3>
                Registrar corrección
            </h3>

            <form
                action="{{ route(
                    'requerimientos.corregir',
                    $requerimiento
                ) }}"
                method="POST"
            >

                @csrf

                <p>

                    <textarea
                        name="OBSERVACION_MOV"
                        rows="4"
                        placeholder="Detalle de la corrección realizada..."
                    ></textarea>

                </p>


                <button type="submit">
                    Corrección realizada
                </button>

            </form>

        @endif



        {{-- SOLICITAR APROBACIÓN --}}

        @if(in_array(
            $requerimiento->ESTADO_REQ,
            [
                'ASIGNADO',
                'EN_PROCESO'
            ]
        ))

            <hr>

            <h3>
                Enviar para aprobación
            </h3>

            <form
                action="{{ route(
                    'requerimientos.solicitar-aprobacion',
                    $requerimiento
                ) }}"
                method="POST"
            >

                @csrf

                <p>

                    <textarea
                        name="OBSERVACION_MOV"
                        rows="3"
                        placeholder="Observación para la aprobación..."
                    ></textarea>

                </p>


                <button type="submit">
                    Solicitar aprobación
                </button>

            </form>

        @endif



        {{-- RESOLVER APROBACIÓN --}}

        @if(
            $requerimiento->ESTADO_REQ
            === 'PENDIENTE_APROBACION'
        )

            <hr>

            <h3>
                Resolver aprobación
            </h3>


            <form
                action="{{ route(
                    'requerimientos.aprobar',
                    $requerimiento
                ) }}"
                method="POST"
            >

                @csrf

                <p>

                    <textarea
                        name="OBSERVACION_MOV"
                        rows="3"
                        placeholder="Observación de aprobación..."
                    ></textarea>

                </p>


                <button type="submit">
                    Aprobar
                </button>

            </form>


            <br>


            <form
                action="{{ route(
                    'requerimientos.rechazar',
                    $requerimiento
                ) }}"
                method="POST"
            >

                @csrf

                <p>

                    <textarea
                        name="OBSERVACION_MOV"
                        rows="3"
                        placeholder="Motivo del rechazo..."
                        required
                    ></textarea>

                </p>


                <button type="submit">
                    Rechazar
                </button>

            </form>

        @endif



        {{-- FIRMA --}}

        @if(
            $requerimiento->ESTADO_REQ
            === 'APROBADO'
        )

            <hr>

            <h3>
                Firma
            </h3>

            <form
                action="{{ route(
                    'requerimientos.solicitar-firma',
                    $requerimiento
                ) }}"
                method="POST"
            >

                @csrf

                <p>

                    <textarea
                        name="OBSERVACION_MOV"
                        rows="3"
                        placeholder="Observación para la firma..."
                    ></textarea>

                </p>


                <button type="submit">
                    Enviar para firma
                </button>

            </form>

        @endif



        {{-- PENDIENTE DE FIRMA --}}

        @if(
            $requerimiento->ESTADO_REQ
            === 'PENDIENTE_FIRMA'
        )

            <hr>

            <h3>
                Pendiente de firma
            </h3>

            <p>
                El requerimiento está esperando la firma correspondiente.
            </p>

            <p>
                La implementación de la firma electrónica real
                se conectará posteriormente.
            </p>

        @endif



        {{-- FIRMADO --}}

        @if(
            $requerimiento->ESTADO_REQ
            === 'FIRMADO'
        )

            <hr>

            <h3>
                Documento firmado
            </h3>

            <p>
                La firma del documento ha sido registrada.
            </p>

        @endif



        {{-- ATENDER --}}

        @if(in_array(
            $requerimiento->ESTADO_REQ,
            [
                'ASIGNADO',
                'EN_PROCESO',
                'FIRMADO'
            ]
        ))

            <hr>

            <h3>
                Finalizar atención
            </h3>

            <form
                action="{{ route(
                    'requerimientos.atender',
                    $requerimiento
                ) }}"
                method="POST"
            >

                @csrf

                <p>

                    <textarea
                        name="OBSERVACION_MOV"
                        rows="4"
                        placeholder="Resultado de la atención..."
                        required
                    ></textarea>

                </p>


                <button type="submit">
                    Marcar como atendido
                </button>

            </form>

        @endif



        {{-- CERRAR --}}

        @if(
            $requerimiento->ESTADO_REQ
            === 'ATENDIDO'
        )

            <hr>

            <h3>
                Cerrar requerimiento
            </h3>

            <form
                action="{{ route(
                    'requerimientos.cerrar',
                    $requerimiento
                ) }}"
                method="POST"
            >

                @csrf

                <p>

                    <textarea
                        name="OBSERVACION_CIERRE_REQ"
                        rows="4"
                        placeholder="Observación final del cierre..."
                        required
                    ></textarea>

                </p>


                <button type="submit">
                    Cerrar requerimiento
                </button>

            </form>

        @endif



        {{-- CERRADO --}}

        @if(
            $requerimiento->ESTADO_REQ
            === 'CERRADO'
        )

            <hr>

            <h3>
                Requerimiento cerrado
            </h3>

            <p>
                Este requerimiento ha finalizado su flujo.
            </p>

        @endif



        {{-- RECHAZADO --}}

        @if(
            $requerimiento->ESTADO_REQ
            === 'RECHAZADO'
        )

            <hr>

            <h3>
                Requerimiento rechazado
            </h3>

            <p>
                El requerimiento fue rechazado durante
                el proceso de aprobación.
            </p>

        @endif

    </div>


	<h2>
    Documentos
	</h2>

	<div class="bloque">

		@if(!in_array(
			$requerimiento->ESTADO_REQ,
			['CERRADO', 'ANULADO']
		))

			<h3>
				Nuevo documento
			</h3>

			<form
				action="{{ route(
					'requerimientos.documentos.store',
					$requerimiento
				) }}"
				method="POST"
			>
				@csrf

				<p>
					<label>
						Tipo de documento
					</label>

					<br>

					<select
						name="TIPO_DOC"
						required
					>
						<option value="">
							Seleccione...
						</option>

						<option value="OFICIO">
							Oficio
						</option>

						<option value="MEMORANDO">
							Memorando
						</option>

						<option value="INFORME">
							Informe
						</option>

						<option value="RESPUESTA">
							Respuesta
						</option>

						<option value="RESOLUCION">
							Resolución
						</option>

						<option value="OTRO">
							Otro
						</option>
					</select>
				</p>

				<p>
					<label>
						Asunto
					</label>

					<br>

					<input
						type="text"
						name="ASUNTO_DOC"
						maxlength="180"
						required
					>
				</p>

				<button type="submit">
					Crear documento
				</button>
			</form>

			<hr>

		@endif


		@forelse($requerimiento->documentos as $documento)

			<div style="margin-bottom: 20px;">

				<strong>
					{{ $documento->NUMERO_DOC }}
				</strong>

				<br>

				Tipo:
				{{ $documento->TIPO_DOC }}

				<br>

				Asunto:
				{{ $documento->ASUNTO_DOC }}

				<br>

				Estado:
				{{ $documento->ESTADO_DOC }}

				<br>

				Fecha:
				{{
					optional(
						$documento->FECHA_HORA_DOC
					)->format('d/m/Y H:i')
				}}
				
				@if($documento->RUTA_DOC)

					<br>

					<a
						href="{{ asset('storage/' . $documento->RUTA_DOC) }}"
						target="_blank"
						class="btn"
					>
						Ver archivo
					</a>

				@else

					<br><br>

					<form
						action="{{ route(
							'documentos.archivo.store',
							$documento
						) }}"
						method="POST"
						enctype="multipart/form-data"
					>
						@csrf

						<input
							type="file"
							name="archivo_documento"
							required
						>

						<button type="submit">
							Adjuntar archivo
						</button>

					</form>

				@endif
				
				

			</div>

			<hr>

		@empty

			<p>
				Este requerimiento todavía no tiene documentos asociados.
			</p>

		@endforelse

	</div>



    {{-- ARCHIVOS ADJUNTOS --}}

    <h2>
        Archivos adjuntos
    </h2>

    <div class="bloque">

        @if(!in_array(
            $requerimiento->ESTADO_REQ,
            ['CERRADO', 'ANULADO']
        ))

            <form
                action="{{ route(
                    'requerimientos.archivos.store',
                    $requerimiento
                ) }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf

                <p>

                    <label>
                        <strong>
                            Adjuntar archivo
                        </strong>
                    </label>

                    <br><br>

                    <input
                        type="file"
                        name="archivo"
                        required
                    >

                </p>


                <p>
                    Tamaño máximo: 10 MB.
                </p>


                <button type="submit">
                    Subir archivo
                </button>

            </form>


            <hr>

        @endif



        @forelse(
            $requerimiento->archivos
                ->where('ESTADO_AREQ', 'ACTIVO')
            as $archivo
        )

            <p>

                <strong>
                    {{ $archivo->NOMBRE_AREQ }}
                </strong>

                <br>


                {{
                    optional(
                        $archivo->FECHA_HORA_AREQ
                    )->format('d/m/Y H:i')
                }}

                <br>


                @if($archivo->TIPO_AREQ)

                    Tipo:
                    {{ $archivo->TIPO_AREQ }}

                    <br>

                @endif


                <a
                    href="{{ asset(
                        'storage/' .
                        $archivo->RUTA_AREQ
                    ) }}"
                    target="_blank"
                >
                    Ver archivo
                </a>

            </p>

        @empty

            <p>
                Este requerimiento todavía
                no tiene archivos adjuntos.
            </p>

        @endforelse

    </div>



    {{-- HISTORIAL --}}

    <h2>
        Historial del requerimiento
    </h2>

    <div class="bloque">

        @forelse(
            $requerimiento->movimientos
                ->sortByDesc('FECHA_HORA_MOV')
            as $movimiento
        )

            <div class="movimiento">

                <strong>
                    {{ $movimiento->ACCION_MOV }}
                </strong>

                <br>


                {{
                    optional(
                        $movimiento->FECHA_HORA_MOV
                    )->format('d/m/Y H:i:s')
                }}

                <br><br>


                @if(
                    $movimiento->ESTADO_ANTERIOR_MOV
                    ||
                    $movimiento->ESTADO_NUEVO_MOV
                )

                    <strong>
                        Estado:
                    </strong>

                    {{
                        $movimiento->ESTADO_ANTERIOR_MOV
                        ?? '—'
                    }}

                    →

                    {{
                        $movimiento->ESTADO_NUEVO_MOV
                        ?? '—'
                    }}

                    <br>

                @endif


                @if($movimiento->SERIAL_DEP_DESTINO)
                    <strong>
                        Departamento destino:
                    </strong>
                    {{ $movimiento->departamentoDestino?->DESCRIPCION_DEP ?? ('Departamento #'.$movimiento->SERIAL_DEP_DESTINO) }}
                    <br>
                @endif


                @if($movimiento->SERIAL_USR_DESTINO)

                    <strong>
                        Usuario destino:
                    </strong>

                    {{ $movimiento->SERIAL_USR_DESTINO }}

                    <br>

                @endif


                @if($movimiento->OBSERVACION_MOV)

                    <br>

                    {{ $movimiento->OBSERVACION_MOV }}

                @endif

            </div>

        @empty

            <p>
                No existen movimientos registrados.
            </p>

        @endforelse

    </div>


        </div>
    </main>
</div>
</body>

</html>