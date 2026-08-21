<?php

namespace App\Http\Controllers;

use App\Models\Requerimiento;
use App\Models\ArchivoRequerimiento;
use App\Models\TipoRequerimiento;
use App\Models\MovimientoRequerimiento;
use App\Models\DocumentoGestion;
use App\Models\UsuarioInstitucional;
use App\Models\DepartamentoInstitucional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class RequerimientoController extends Controller
{
    private function usuarioActual(Request $request): UsuarioInstitucional
    {
        return $request->attributes->get('usuarioSesion');
    }

    private function departamentoOrigenUsuario(UsuarioInstitucional $usuario): ?int
    {
        if (!$usuario->SERIAL_EPL) {
            return null;
        }

        return DB::table('empleado as e')
            ->leftJoin('sucursaldepartamentos as sd', 'sd.SERIAL_DESC', '=', 'e.SERIAL_DESC')
            ->where('e.SERIAL_EPL', $usuario->SERIAL_EPL)
            ->value('sd.SERIAL_DEP');
    }

    /**
     * Mostrar todos los requerimientos.
     */
    public function index(Request $request)
	{
		$query = Requerimiento::with(['tipo', 'departamentoDestino', 'departamentoOrigen']);

		if ($request->filled('buscar')) {
			$buscar = $request->buscar;

			$query->where(function ($q) use ($buscar) {
				$q->where(
					'NUMERO_REQ',
					'like',
					'%' . $buscar . '%'
				)
				->orWhere(
					'ASUNTO_REQ',
					'like',
					'%' . $buscar . '%'
				);
			});
		}

		if ($request->filled('estado')) {
			$query->where(
				'ESTADO_REQ',
				$request->estado
			);
		}

		if ($request->filled('prioridad')) {
			$query->where(
				'PRIORIDAD_REQ',
				$request->prioridad
			);
		}

		if ($request->filled('departamento')) {
			$query->where('SERIAL_DEP_DESTINO', $request->departamento);
		}

		if ($request->filled('fecha_desde')) {
			$query->whereDate('FECHA_CREACION_REQ', '>=', $request->fecha_desde);
		}

		if ($request->filled('fecha_hasta')) {
			$query->whereDate('FECHA_CREACION_REQ', '<=', $request->fecha_hasta);
		}

		$requerimientos = $query
			->orderByDesc('FECHA_CREACION_REQ')
			->get();

		$departamentos = DepartamentoInstitucional::orderBy('DESCRIPCION_DEP')->get();

		return view(
			'requerimientos.index',
			compact('requerimientos', 'departamentos')
		);
	}

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $tipos = TipoRequerimiento::where(
            'ESTADO_TREQ',
            'ACTIVO'
        )
        ->orderBy('NOMBRE_TREQ')
        ->get();

        $departamentos = DepartamentoInstitucional::orderBy('DESCRIPCION_DEP')->get();

        return view(
            'requerimientos.create',
            compact('tipos', 'departamentos')
        );
    }

    /**
     * Guardar un nuevo requerimiento.
     */
    public function store(Request $request)
    {
        $usuarioActual = $this->usuarioActual($request);
        $departamentoOrigen = $this->departamentoOrigenUsuario($usuarioActual);

        $datos = $request->validate([
            'SERIAL_DEP_DESTINO' => 'required|integer|min:1',

            'SERIAL_TREQ' => 'required|integer|exists:tipo_requerimiento,SERIAL_TREQ',

            'ASUNTO_REQ' => 'required|string|max:150',
            'DESCRIPCION_REQ' => 'required|string',

            'PRIORIDAD_REQ' => 'required|in:BAJA,MEDIA,ALTA,URGENTE',

            'FECHA_LIMITE_REQ' => 'nullable|date',
        ]);

        $requerimiento = DB::transaction(function () use ($datos, $usuarioActual, $departamentoOrigen) {

            /*
             * Temporalmente se genera primero un código único.
             * Una vez creado el registro conocemos SERIAL_REQ y
             * generamos el número institucional definitivo.
             */
            $requerimiento = Requerimiento::create([
                'NUMERO_REQ' =>
					'TMP-' . Str::random(20),

                'SERIAL_USR_SOLICITA' =>
                    $usuarioActual->SERIAL_USR,

                'SERIAL_DEP_ORIGEN' =>
                    $departamentoOrigen,

                'SERIAL_DEP_DESTINO' =>
                    $datos['SERIAL_DEP_DESTINO'],

                'SERIAL_USR_RESPONSABLE' => null,

                'SERIAL_TREQ' =>
                    $datos['SERIAL_TREQ'],

                'ASUNTO_REQ' =>
                    $datos['ASUNTO_REQ'],

                'DESCRIPCION_REQ' =>
                    $datos['DESCRIPCION_REQ'],

                'PRIORIDAD_REQ' =>
                    $datos['PRIORIDAD_REQ'],

                /*
                 * El requerimiento se considera enviado
                 * desde el momento en que el usuario lo registra.
                 */
                'ESTADO_REQ' => 'ENVIADO',

                'FECHA_CREACION_REQ' => now(),

                'FECHA_LIMITE_REQ' =>
                    $datos['FECHA_LIMITE_REQ'] ?? null,

                'FECHA_CIERRE_REQ' => null,

                'OBSERVACION_CIERRE_REQ' => null,
            ]);

            /*
             * Número definitivo.
             *
             * Ejemplo:
             * REQ-2026-000001
             */
            $requerimiento->NUMERO_REQ =
                'REQ-' .
                now()->format('Y') .
                '-' .
                str_pad(
                    $requerimiento->SERIAL_REQ,
                    6,
                    '0',
                    STR_PAD_LEFT
                );

            $requerimiento->save();

            /*
             * Primer registro del historial.
             */
            MovimientoRequerimiento::create([
                'SERIAL_REQ' =>
                    $requerimiento->SERIAL_REQ,

                'SERIAL_USR' =>
                    session('usuario_serial'),

                'ACCION_MOV' =>
                    'CREACION',

                'SERIAL_USR_DESTINO' =>
                    null,

                'SERIAL_DEP_DESTINO' =>
                    $requerimiento->SERIAL_DEP_DESTINO,

                'ESTADO_ANTERIOR_MOV' =>
                    null,

                'ESTADO_NUEVO_MOV' =>
                    'ENVIADO',

                'OBSERVACION_MOV' =>
                    'Requerimiento creado y enviado.',

                'FECHA_HORA_MOV' =>
                    now(),
            ]);

            return $requerimiento;
        });

        return redirect()
            ->route(
                'requerimientos.show',
                $requerimiento
            )
            ->with(
                'success',
                'Requerimiento creado correctamente.'
            );
    }

    /**
     * Mostrar un requerimiento y su historial.
     */
    public function show(Requerimiento $requerimiento)
    {
        $requerimiento->load([
            'tipo',
            'departamentoOrigen',
            'departamentoDestino',
            'movimientos.departamentoDestino',
            'movimientos.usuario.empleado',
            'archivos',
            'documentos',
        ]);

        $departamentos = DepartamentoInstitucional::orderBy('DESCRIPCION_DEP')->get();

        return view(
            'requerimientos.show',
            compact('requerimiento', 'departamentos')
        );
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Requerimiento $requerimiento)
    {
        /*
         * Inicialmente solo permitiremos editar
         * requerimientos que todavía no estén cerrados
         * ni anulados.
         */
        if (
            in_array(
                $requerimiento->ESTADO_REQ,
                ['CERRADO', 'ANULADO']
            )
        ) {
            return redirect()
                ->route(
                    'requerimientos.show',
                    $requerimiento
                )
                ->with(
                    'error',
                    'Este requerimiento ya no puede modificarse.'
                );
        }

        $tipos = TipoRequerimiento::where(
            'ESTADO_TREQ',
            'ACTIVO'
        )->get();

        $departamentos = DepartamentoInstitucional::orderBy('DESCRIPCION_DEP')->get();

        return view(
            'requerimientos.edit',
            compact(
                'requerimiento',
                'tipos',
                'departamentos'
            )
        );
    }

    /**
     * Actualizar requerimiento.
     */
    public function update(
        Request $request,
        Requerimiento $requerimiento
    ) {
        $datos = $request->validate([
            'SERIAL_DEP_DESTINO' => 'required|integer|min:1',

            'SERIAL_TREQ' =>
                'required|integer|exists:tipo_requerimiento,SERIAL_TREQ',

            'ASUNTO_REQ' =>
                'required|string|max:150',

            'DESCRIPCION_REQ' =>
                'required|string',

            'PRIORIDAD_REQ' =>
                'required|in:BAJA,MEDIA,ALTA,URGENTE',

            'FECHA_LIMITE_REQ' =>
                'nullable|date',
        ]);

        DB::transaction(
            function () use (
                $datos,
                $requerimiento
            ) {

                $requerimiento->update($datos);

                MovimientoRequerimiento::create([
                    'SERIAL_REQ' =>
                        $requerimiento->SERIAL_REQ,

                    'SERIAL_USR' =>
                        $requerimiento->SERIAL_USR_SOLICITA,

                    'ACCION_MOV' =>
                        'MODIFICACION',

                    'SERIAL_USR_DESTINO' =>
                        null,

                    'SERIAL_DEP_DESTINO' =>
                        $requerimiento->SERIAL_DEP_DESTINO,

                    'ESTADO_ANTERIOR_MOV' =>
                        $requerimiento->ESTADO_REQ,

                    'ESTADO_NUEVO_MOV' =>
                        $requerimiento->ESTADO_REQ,

                    'OBSERVACION_MOV' =>
                        'Se modificaron los datos del requerimiento.',

                    'FECHA_HORA_MOV' =>
                        now(),
                ]);
            }
        );

        return redirect()
            ->route(
                'requerimientos.show',
                $requerimiento
            )
            ->with(
                'success',
                'Requerimiento actualizado correctamente.'
            );
    }

    /**
     * Anular requerimiento.
     */
    public function destroy(Requerimiento $requerimiento)
    {
        if ($requerimiento->ESTADO_REQ === 'CERRADO') {
            return redirect()
                ->route(
                    'requerimientos.show',
                    $requerimiento
                )
                ->with(
                    'error',
                    'Un requerimiento cerrado no puede anularse.'
                );
        }

        DB::transaction(function () use ($requerimiento) {

            $estadoAnterior =
                $requerimiento->ESTADO_REQ;

            $requerimiento->update([
                'ESTADO_REQ' => 'ANULADO',
            ]);

            MovimientoRequerimiento::create([
                'SERIAL_REQ' =>
                    $requerimiento->SERIAL_REQ,

                'SERIAL_USR' =>
                    session('usuario_serial'),

                'ACCION_MOV' =>
                    'ANULACION',

                'SERIAL_USR_DESTINO' =>
                    null,

                'SERIAL_DEP_DESTINO' =>
                    $requerimiento->SERIAL_DEP_DESTINO,

                'ESTADO_ANTERIOR_MOV' =>
                    $estadoAnterior,

                'ESTADO_NUEVO_MOV' =>
                    'ANULADO',

                'OBSERVACION_MOV' =>
                    'El requerimiento fue anulado.',

                'FECHA_HORA_MOV' =>
                    now(),
            ]);
        });

        return redirect()
            ->route('requerimientos.index')
            ->with(
                'success',
                'Requerimiento anulado correctamente.'
            );
    }
	
	public function derivar(Request $request, Requerimiento $requerimiento) 
	{
		$datos = $request->validate([
			'SERIAL_DEP_DESTINO' => 'required|integer|min:1',
			'SERIAL_USR_DESTINO' => 'nullable|integer|min:1',
			'OBSERVACION_MOV' => 'nullable|string',
		]);

		DB::transaction(function () use (
			$datos,
			$requerimiento
		) {
			$estadoAnterior = $requerimiento->ESTADO_REQ;

			$requerimiento->update([
				'SERIAL_DEP_DESTINO' => $datos['SERIAL_DEP_DESTINO'],
				'SERIAL_USR_RESPONSABLE' =>
					$datos['SERIAL_USR_DESTINO'] ?? null,
				'ESTADO_REQ' => 'DERIVADO',
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' => $requerimiento->SERIAL_REQ,
				'SERIAL_USR' => session('usuario_serial'),
				'ACCION_MOV' => 'DERIVACION',

				'SERIAL_USR_DESTINO' =>
					$datos['SERIAL_USR_DESTINO'] ?? null,

				'SERIAL_DEP_DESTINO' =>
					$datos['SERIAL_DEP_DESTINO'],

				'ESTADO_ANTERIOR_MOV' =>
					$estadoAnterior,

				'ESTADO_NUEVO_MOV' =>
					'DERIVADO',

				'OBSERVACION_MOV' =>
					$datos['OBSERVACION_MOV']
					?? 'Requerimiento derivado.',

				'FECHA_HORA_MOV' => now(),
			]);
		});

		return redirect()
			->route('requerimientos.show', $requerimiento)
			->with(
				'success',
				'Requerimiento derivado correctamente.'
			);
	}
	
	public function cambiarEstado(Request $request, Requerimiento $requerimiento) {
		$datos = $request->validate([
			'ESTADO_REQ' => 'required|in:RECIBIDO,DERIVADO,ASIGNADO,EN_PROCESO,PENDIENTE_CORRECCION,PENDIENTE_APROBACION,APROBADO,PENDIENTE_FIRMA,FIRMADO,ATENDIDO,CERRADO,RECHAZADO',
			'OBSERVACION_MOV' => 'nullable|string',
		]);

		DB::transaction(function () use (
			$datos,
			$requerimiento
		) {
			$estadoAnterior = $requerimiento->ESTADO_REQ;

			$requerimiento->update([
				'ESTADO_REQ' => $datos['ESTADO_REQ'],
				'FECHA_CIERRE_REQ' =>
					$datos['ESTADO_REQ'] === 'CERRADO'
						? now()
						: $requerimiento->FECHA_CIERRE_REQ,
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' =>
					$requerimiento->SERIAL_REQ,

				'SERIAL_USR' =>
					$requerimiento->SERIAL_USR_SOLICITA,

				'ACCION_MOV' =>
					'CAMBIO_ESTADO',

				'SERIAL_USR_DESTINO' =>
					$requerimiento->SERIAL_USR_RESPONSABLE,

				'SERIAL_DEP_DESTINO' =>
					$requerimiento->SERIAL_DEP_DESTINO,

				'ESTADO_ANTERIOR_MOV' =>
					$estadoAnterior,

				'ESTADO_NUEVO_MOV' =>
					$datos['ESTADO_REQ'],

				'OBSERVACION_MOV' =>
					$datos['OBSERVACION_MOV']
					?? 'Cambio de estado del requerimiento.',

				'FECHA_HORA_MOV' => now(),
			]);
		});

		return redirect()
			->route('requerimientos.show', $requerimiento)
			->with(
				'success',
				'Estado actualizado correctamente.'
			);
	}
	
	public function subirArchivo(Request $request, Requerimiento $requerimiento) {
		$request->validate([
			'archivo' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
		]);

		$archivo = $request->file('archivo');

		$ruta = $archivo->store(
			'requerimientos/' . $requerimiento->SERIAL_REQ,
			'public'
		);

		DB::transaction(function () use (
			$archivo,
			$ruta,
			$requerimiento
		) {
			ArchivoRequerimiento::create([
				'SERIAL_REQ' =>
					$requerimiento->SERIAL_REQ,

				/*
				 * Temporalmente utilizamos el usuario solicitante
				 * Luego se reemplazará por el usuario autenticado
				 */
				'SERIAL_USR' =>
					$requerimiento->SERIAL_USR_SOLICITA,

				'NOMBRE_AREQ' =>
					$archivo->getClientOriginalName(),

				'RUTA_AREQ' =>
					$ruta,

				'TIPO_AREQ' =>
					$archivo->getClientMimeType(),

				'FECHA_HORA_AREQ' =>
					now(),

				'ESTADO_AREQ' =>
					'ACTIVO',
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' =>
					$requerimiento->SERIAL_REQ,

				'SERIAL_USR' =>
					$requerimiento->SERIAL_USR_SOLICITA,

				'ACCION_MOV' =>
					'ARCHIVO_ADJUNTADO',

				'SERIAL_USR_DESTINO' =>
					null,

				'SERIAL_DEP_DESTINO' =>
					$requerimiento->SERIAL_DEP_DESTINO,

				'ESTADO_ANTERIOR_MOV' =>
					$requerimiento->ESTADO_REQ,

				'ESTADO_NUEVO_MOV' =>
					$requerimiento->ESTADO_REQ,

				'OBSERVACION_MOV' =>
					'Se adjuntó el archivo: ' .
					$archivo->getClientOriginalName(),

				'FECHA_HORA_MOV' =>
					now(),
			]);
		});

		return redirect()
			->route(
				'requerimientos.show',
				$requerimiento
			)
			->with(
				'success',
				'Archivo adjuntado correctamente.'
			);
	}
	
	public function asignar(Request $request, Requerimiento $requerimiento) {
		$datos = $request->validate([
			'SERIAL_USR_RESPONSABLE' => 'required|integer|min:1',
			'OBSERVACION_MOV' => 'nullable|string',
		]);

		DB::transaction(function () use ($datos, $requerimiento) {

			$estadoAnterior = $requerimiento->ESTADO_REQ;

			$requerimiento->update([
				'SERIAL_USR_RESPONSABLE' =>
					$datos['SERIAL_USR_RESPONSABLE'],

				'ESTADO_REQ' =>
					'ASIGNADO',
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' =>
					$requerimiento->SERIAL_REQ,

				'SERIAL_USR' =>
					$requerimiento->SERIAL_USR_SOLICITA,

				'ACCION_MOV' =>
					'ASIGNACION',

				'SERIAL_USR_DESTINO' =>
					$datos['SERIAL_USR_RESPONSABLE'],

				'SERIAL_DEP_DESTINO' =>
					$requerimiento->SERIAL_DEP_DESTINO,

				'ESTADO_ANTERIOR_MOV' =>
					$estadoAnterior,

				'ESTADO_NUEVO_MOV' =>
					'ASIGNADO',

				'OBSERVACION_MOV' =>
					$datos['OBSERVACION_MOV']
					?? 'Se asignó un responsable al requerimiento.',

				'FECHA_HORA_MOV' =>
					now(),
			]);
		});

		return redirect()
			->route('requerimientos.show', $requerimiento)
			->with(
				'success',
				'Responsable asignado correctamente.'
			);
	}
	
	public function solicitarCorreccion(Request $request, Requerimiento $requerimiento) {
		$datos = $request->validate([
			'OBSERVACION_MOV' => 'required|string|max:1000',
		]);

		DB::transaction(function () use ($datos, $requerimiento) {

			$estadoAnterior = $requerimiento->ESTADO_REQ;

			$requerimiento->update([
				'ESTADO_REQ' =>
					'PENDIENTE_CORRECCION',
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' =>
					$requerimiento->SERIAL_REQ,

				'SERIAL_USR' =>
					$requerimiento->SERIAL_USR_SOLICITA,

				'ACCION_MOV' =>
					'CORRECCION_SOLICITADA',

				'SERIAL_USR_DESTINO' =>
					$requerimiento->SERIAL_USR_SOLICITA,

				'SERIAL_DEP_DESTINO' =>
					$requerimiento->SERIAL_DEP_DESTINO,

				'ESTADO_ANTERIOR_MOV' =>
					$estadoAnterior,

				'ESTADO_NUEVO_MOV' =>
					'PENDIENTE_CORRECCION',

				'OBSERVACION_MOV' =>
					$datos['OBSERVACION_MOV'],

				'FECHA_HORA_MOV' =>
					now(),
			]);
		});

		return redirect()
			->route('requerimientos.show', $requerimiento)
			->with(
				'success',
				'Corrección solicitada correctamente.'
			);
	}
	
	
	public function corregir(Request $request,Requerimiento $requerimiento) {
		$datos = $request->validate([
			'OBSERVACION_MOV' => 'nullable|string|max:1000',
		]);

		DB::transaction(function () use ($datos, $requerimiento) {

			$estadoAnterior = $requerimiento->ESTADO_REQ;

			$requerimiento->update([
				'ESTADO_REQ' =>
					'EN_PROCESO',
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' =>
					$requerimiento->SERIAL_REQ,

				'SERIAL_USR' =>
					$requerimiento->SERIAL_USR_SOLICITA,

				'ACCION_MOV' =>
					'CORRECCION_REALIZADA',

				'SERIAL_USR_DESTINO' =>
					$requerimiento->SERIAL_USR_RESPONSABLE,

				'SERIAL_DEP_DESTINO' =>
					$requerimiento->SERIAL_DEP_DESTINO,

				'ESTADO_ANTERIOR_MOV' =>
					$estadoAnterior,

				'ESTADO_NUEVO_MOV' =>
					'EN_PROCESO',

				'OBSERVACION_MOV' =>
					$datos['OBSERVACION_MOV']
					?? 'La información solicitada fue corregida.',

				'FECHA_HORA_MOV' =>
					now(),
			]);
		});

		return redirect()
			->route('requerimientos.show', $requerimiento)
			->with(
				'success',
				'Corrección registrada correctamente.'
			);
	}
	
	public function atender(Request $request,Requerimiento $requerimiento) {
		$datos = $request->validate([
			'OBSERVACION_MOV' => 'required|string|max:1000',
		]);

		DB::transaction(function () use ($datos, $requerimiento) {

			$estadoAnterior = $requerimiento->ESTADO_REQ;

			$requerimiento->update([
				'ESTADO_REQ' =>
					'ATENDIDO',
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' =>
					$requerimiento->SERIAL_REQ,

				'SERIAL_USR' =>
					$requerimiento->SERIAL_USR_SOLICITA,

				'ACCION_MOV' =>
					'ATENCION',

				'SERIAL_USR_DESTINO' =>
					$requerimiento->SERIAL_USR_RESPONSABLE,

				'SERIAL_DEP_DESTINO' =>
					$requerimiento->SERIAL_DEP_DESTINO,

				'ESTADO_ANTERIOR_MOV' =>
					$estadoAnterior,

				'ESTADO_NUEVO_MOV' =>
					'ATENDIDO',

				'OBSERVACION_MOV' =>
					$datos['OBSERVACION_MOV'],

				'FECHA_HORA_MOV' =>
					now(),
			]);
		});

		return redirect()
			->route('requerimientos.show', $requerimiento)
			->with(
				'success',
				'Requerimiento marcado como atendido.'
			);
	}
	
	public function cerrar(Request $request, Requerimiento $requerimiento) {
		$datos = $request->validate([
			'OBSERVACION_CIERRE_REQ' =>
				'required|string|max:1000',
		]);

		DB::transaction(function () use ($datos, $requerimiento) {

			$estadoAnterior = $requerimiento->ESTADO_REQ;

			$requerimiento->update([
				'ESTADO_REQ' =>
					'CERRADO',

				'FECHA_CIERRE_REQ' =>
					now(),

				'OBSERVACION_CIERRE_REQ' =>
					$datos['OBSERVACION_CIERRE_REQ'],
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' =>
					$requerimiento->SERIAL_REQ,

				'SERIAL_USR' =>
					$requerimiento->SERIAL_USR_SOLICITA,

				'ACCION_MOV' =>
					'CIERRE',

				'SERIAL_USR_DESTINO' =>
					null,

				'SERIAL_DEP_DESTINO' =>
					$requerimiento->SERIAL_DEP_DESTINO,

				'ESTADO_ANTERIOR_MOV' =>
					$estadoAnterior,

				'ESTADO_NUEVO_MOV' =>
					'CERRADO',

				'OBSERVACION_MOV' =>
					$datos['OBSERVACION_CIERRE_REQ'],

				'FECHA_HORA_MOV' =>
					now(),
			]);
		});

		return redirect()
			->route('requerimientos.show', $requerimiento)
			->with(
				'success',
				'Requerimiento cerrado correctamente.'
			);
	}
	
	
	public function solicitarAprobacion(Request $request,Requerimiento $requerimiento) {
		$datos = $request->validate([
			'OBSERVACION_MOV' => 'nullable|string|max:1000',
		]);

		DB::transaction(function () use ($datos, $requerimiento) {

			$estadoAnterior = $requerimiento->ESTADO_REQ;

			$requerimiento->update([
				'ESTADO_REQ' => 'PENDIENTE_APROBACION',
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' => $requerimiento->SERIAL_REQ,
				'SERIAL_USR' => session('usuario_serial'),
				'ACCION_MOV' => 'SOLICITUD_APROBACION',
				'SERIAL_USR_DESTINO' => null,
				'SERIAL_DEP_DESTINO' => $requerimiento->SERIAL_DEP_DESTINO,
				'ESTADO_ANTERIOR_MOV' => $estadoAnterior,
				'ESTADO_NUEVO_MOV' => 'PENDIENTE_APROBACION',
				'OBSERVACION_MOV' =>
					$datos['OBSERVACION_MOV']
					?? 'El requerimiento fue enviado para aprobación.',
				'FECHA_HORA_MOV' => now(),
			]);
		});

		return redirect()
			->route('requerimientos.show', $requerimiento)
			->with('success', 'Requerimiento enviado para aprobación.');
	}
	
	public function aprobar(Request $request,Requerimiento $requerimiento) {
		$datos = $request->validate([
			'OBSERVACION_MOV' => 'nullable|string|max:1000',
		]);

		DB::transaction(function () use ($datos, $requerimiento) {

			$estadoAnterior = $requerimiento->ESTADO_REQ;

			$requerimiento->update([
				'ESTADO_REQ' => 'APROBADO',
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' => $requerimiento->SERIAL_REQ,
				'SERIAL_USR' => session('usuario_serial'),
				'ACCION_MOV' => 'APROBACION',
				'SERIAL_USR_DESTINO' => null,
				'SERIAL_DEP_DESTINO' => $requerimiento->SERIAL_DEP_DESTINO,
				'ESTADO_ANTERIOR_MOV' => $estadoAnterior,
				'ESTADO_NUEVO_MOV' => 'APROBADO',
				'OBSERVACION_MOV' =>
					$datos['OBSERVACION_MOV']
					?? 'Requerimiento aprobado.',
				'FECHA_HORA_MOV' => now(),
			]);
		});

		return redirect()
			->route('requerimientos.show', $requerimiento)
			->with('success', 'Requerimiento aprobado.');
	}
	
	public function rechazar(Request $request,Requerimiento $requerimiento) {
		$datos = $request->validate([
			'OBSERVACION_MOV' => 'required|string|max:1000',
		]);

		DB::transaction(function () use ($datos, $requerimiento) {

			$estadoAnterior = $requerimiento->ESTADO_REQ;

			$requerimiento->update([
				'ESTADO_REQ' => 'RECHAZADO',
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' => $requerimiento->SERIAL_REQ,
				'SERIAL_USR' => session('usuario_serial'),
				'ACCION_MOV' => 'RECHAZO',
				'SERIAL_USR_DESTINO' => $requerimiento->SERIAL_USR_SOLICITA,
				'SERIAL_DEP_DESTINO' => $requerimiento->SERIAL_DEP_DESTINO,
				'ESTADO_ANTERIOR_MOV' => $estadoAnterior,
				'ESTADO_NUEVO_MOV' => 'RECHAZADO',
				'OBSERVACION_MOV' => $datos['OBSERVACION_MOV'],
				'FECHA_HORA_MOV' => now(),
			]);
		});

		return redirect()
			->route('requerimientos.show', $requerimiento)
			->with('success', 'Requerimiento rechazado.');
	}
	
	public function solicitarFirma(Request $request,Requerimiento $requerimiento) {
		$datos = $request->validate([
			'OBSERVACION_MOV' => 'nullable|string|max:1000',
		]);

		DB::transaction(function () use ($datos, $requerimiento) {

			$estadoAnterior = $requerimiento->ESTADO_REQ;

			$requerimiento->update([
				'ESTADO_REQ' => 'PENDIENTE_FIRMA',
			]);

			MovimientoRequerimiento::create([
				'SERIAL_REQ' => $requerimiento->SERIAL_REQ,
				'SERIAL_USR' => session('usuario_serial'),
				'ACCION_MOV' => 'SOLICITUD_FIRMA',
				'SERIAL_USR_DESTINO' => null,
				'SERIAL_DEP_DESTINO' => $requerimiento->SERIAL_DEP_DESTINO,
				'ESTADO_ANTERIOR_MOV' => $estadoAnterior,
				'ESTADO_NUEVO_MOV' => 'PENDIENTE_FIRMA',
				'OBSERVACION_MOV' =>
					$datos['OBSERVACION_MOV']
					?? 'Documento enviado para firma.',
				'FECHA_HORA_MOV' => now(),
			]);
		});

		return redirect()
			->route('requerimientos.show', $requerimiento)
			->with('success', 'Requerimiento enviado para firma.');
	}
	
	
	public function crearDocumento(Request $request, Requerimiento $requerimiento) 
	{
		$datos = $request->validate([
			'TIPO_DOC' => 'required|in:OFICIO,MEMORANDO,INFORME,RESPUESTA,RESOLUCION,OTRO',
			'ASUNTO_DOC' => 'required|string|max:180',
		]);

		$documento = DB::transaction(function () use (
			$datos,
			$requerimiento
		) {
			$documento = DocumentoGestion::create([
				'NUMERO_DOC' => null,

				'SERIAL_REQ' =>
					$requerimiento->SERIAL_REQ,

				'SERIAL_USR_AUTOR' =>
					$requerimiento->SERIAL_USR_SOLICITA,

				'SERIAL_USR_DESTINO' =>
					$requerimiento->SERIAL_USR_RESPONSABLE,

				'SERIAL_DEP_ORIGEN' =>
					$requerimiento->SERIAL_DEP_ORIGEN,

				'SERIAL_DEP_DESTINO' =>
					$requerimiento->SERIAL_DEP_DESTINO,

				'TIPO_DOC' =>
					$datos['TIPO_DOC'],

				'ASUNTO_DOC' =>
					$datos['ASUNTO_DOC'],

				'RUTA_DOC' =>
					null,

				'ESTADO_DOC' =>
					'BORRADOR',

				'FECHA_HORA_DOC' =>
					now(),
			]);

			$documento->NUMERO_DOC =
				'DOC-' .
				now()->format('Y') .
				'-' .
				str_pad(
					$documento->SERIAL_DOC_GES,
					6,
					'0',
					STR_PAD_LEFT
				);

			$documento->save();

			MovimientoRequerimiento::create([
				'SERIAL_REQ' =>
					$requerimiento->SERIAL_REQ,

				'SERIAL_USR' =>
					$requerimiento->SERIAL_USR_SOLICITA,

				'ACCION_MOV' =>
					'DOCUMENTO_CREADO',

				'SERIAL_USR_DESTINO' =>
					$requerimiento->SERIAL_USR_RESPONSABLE,

				'SERIAL_DEP_DESTINO' =>
					$requerimiento->SERIAL_DEP_DESTINO,

				'ESTADO_ANTERIOR_MOV' =>
					$requerimiento->ESTADO_REQ,

				'ESTADO_NUEVO_MOV' =>
					$requerimiento->ESTADO_REQ,

				'OBSERVACION_MOV' =>
					'Se creó el documento ' .
					$documento->NUMERO_DOC .
					'.',

				'FECHA_HORA_MOV' =>
					now(),
			]);

			return $documento;
		});

		return redirect()
			->route(
				'requerimientos.show',
				$requerimiento
			)
			->with(
				'success',
				'Documento creado correctamente.'
			);
	}
	
	
	public function subirArchivoDocumento(Request $request, DocumentoGestion $documento) 
	{
		$request->validate([
			'archivo_documento' => 'required|file|max:10240',
		]);

		$archivo = $request->file('archivo_documento');

		$ruta = $archivo->store(
			'documentos/' . $documento->SERIAL_DOC_GES,
			'public'
		);

		DB::transaction(function () use (
			$documento,
			$ruta
		) {
			$documento->update([
				'RUTA_DOC' => $ruta,
			]);

			if ($documento->SERIAL_REQ) {

				MovimientoRequerimiento::create([
					'SERIAL_REQ' =>
						$documento->SERIAL_REQ,

					'SERIAL_USR' =>
						$documento->SERIAL_USR_AUTOR,

					'ACCION_MOV' =>
						'ARCHIVO_DOCUMENTO_ADJUNTADO',

					'SERIAL_USR_DESTINO' =>
						$documento->SERIAL_USR_DESTINO,

					'SERIAL_DEP_DESTINO' =>
						$documento->SERIAL_DEP_DESTINO,

					'ESTADO_ANTERIOR_MOV' =>
						null,

					'ESTADO_NUEVO_MOV' =>
						null,

					'OBSERVACION_MOV' =>
						'Se adjuntó el archivo del documento ' .
						$documento->NUMERO_DOC . '.',

					'FECHA_HORA_MOV' =>
						now(),
				]);
			}
		});

		return back()->with(
			'success',
			'Archivo del documento cargado correctamente.'
		);
	}
	
	
}