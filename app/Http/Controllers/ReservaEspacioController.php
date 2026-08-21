<?php

namespace App\Http\Controllers;

use App\Models\Espacio;
use App\Models\MovimientoReserva;
use App\Models\ReservaEspacio;
use App\Models\UsuarioInstitucional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReservaEspacioController extends Controller
{
    private function usuarioActual(Request $request): UsuarioInstitucional
    {
        return $request->attributes->get('usuarioSesion');
    }

    private function esAdministrador(UsuarioInstitucional $usuario): bool
    {
        $codigo = strtoupper(trim((string) ($usuario->perfil?->CODIGO_PFL ?? '')));
        $nombre = strtoupper(trim((string) ($usuario->perfil?->NOMBRE_PFL ?? '')));
        return $codigo === 'ADM' || str_contains($nombre, 'ADMIN');
    }

    private function puedeGestionar(UsuarioInstitucional $usuario, ReservaEspacio $reserva): bool
    {
        if ($this->esAdministrador($usuario)) {
            return true;
        }

        return (int) $reserva->espacio?->SERIAL_USR_ENCARGADO === (int) $usuario->SERIAL_USR;
    }

    private function registrarMovimiento(
        ReservaEspacio $reserva,
        int $usuario,
        string $accion,
        ?string $anterior,
        ?string $nuevo,
        ?string $observacion = null
    ): void {
        MovimientoReserva::create([
            'SERIAL_RES' => $reserva->SERIAL_RES,
            'SERIAL_USR' => $usuario,
            'ACCION_MRES' => $accion,
            'ESTADO_ANTERIOR_MRES' => $anterior,
            'ESTADO_NUEVO_MRES' => $nuevo,
            'OBSERVACION_MRES' => $observacion,
            'FECHA_HORA_MRES' => now(),
        ]);
    }

    private function conflictosPara(int $espacio, $inicio, $fin, ?int $excluir = null)
    {
        return ReservaEspacio::with(['solicitante.empleado'])
            ->where('SERIAL_ESP', $espacio)
            ->when($excluir, fn ($q) => $q->where('SERIAL_RES', '<>', $excluir))
            ->where('ESTADO_RES', 'APROBADA')
            ->where('FECHA_INICIO_RES', '<', $fin)
            ->where('FECHA_FIN_RES', '>', $inicio)
            ->orderBy('FECHA_INICIO_RES')
            ->get();
    }

    public function index(Request $request)
    {
        $usuario = $this->usuarioActual($request);

        $reservas = ReservaEspacio::with(['espacio', 'solicitante.empleado'])
            ->where('SERIAL_USR_SOLICITA', $usuario->SERIAL_USR)
            ->orderByDesc('FECHA_CREACION_RES')
            ->get();

        return view('reservas.index', compact('reservas', 'usuario'));
    }

    public function create(Request $request)
    {
        return redirect()->route('reservas.horario');
    }

    public function verificar(Request $request)
    {
        $datos = $request->validate([
            'SERIAL_ESP' => 'required|integer|exists:espacio,SERIAL_ESP',
            'FECHA_INICIO_RES' => 'required|date',
            'FECHA_FIN_RES' => 'required|date|after:FECHA_INICIO_RES',
        ]);

        $conflictos = $this->conflictosPara(
            (int) $datos['SERIAL_ESP'],
            $datos['FECHA_INICIO_RES'],
            $datos['FECHA_FIN_RES']
        );

        return response()->json([
            'hay_conflicto' => $conflictos->isNotEmpty(),
            'cantidad' => $conflictos->count(),
            'conflictos' => $conflictos->map(fn ($r) => [
                'numero' => $r->NUMERO_RES,
                'inicio' => optional($r->FECHA_INICIO_RES)->format('d/m/Y H:i'),
                'fin' => optional($r->FECHA_FIN_RES)->format('d/m/Y H:i'),
                'solicitante' => $r->solicitante?->nombre_completo ?? 'Usuario #' . $r->SERIAL_USR_SOLICITA,
            ])->values(),
        ]);
    }

    public function store(Request $request)
    {
        $usuario = $this->usuarioActual($request);

        $datos = $request->validate([
            'SERIAL_ESP' => 'required|integer|exists:espacio,SERIAL_ESP',
            'TITULO_RES' => 'required|string|max:180',
            'DESCRIPCION_RES' => 'nullable|string|max:2000',
            'FECHA_INICIO_RES' => 'required|date',
            'FECHA_FIN_RES' => 'required|date|after:FECHA_INICIO_RES',
        ]);

        $conflictos = $this->conflictosPara(
            (int) $datos['SERIAL_ESP'],
            $datos['FECHA_INICIO_RES'],
            $datos['FECHA_FIN_RES']
        );

        $reserva = DB::transaction(function () use ($datos, $usuario, $conflictos) {
            $reserva = ReservaEspacio::create([
                'NUMERO_RES' => 'TMP-' . Str::random(20),
                'SERIAL_ESP' => $datos['SERIAL_ESP'],
                'SERIAL_USR_SOLICITA' => $usuario->SERIAL_USR,
                'TITULO_RES' => $datos['TITULO_RES'],
                'DESCRIPCION_RES' => $datos['DESCRIPCION_RES'] ?? null,
                'FECHA_INICIO_RES' => $datos['FECHA_INICIO_RES'],
                'FECHA_FIN_RES' => $datos['FECHA_FIN_RES'],
                'ESTADO_RES' => 'PENDIENTE',
                'OBSERVACION_RES' => null,
                'FECHA_CREACION_RES' => now(),
                'FECHA_RESOLUCION_RES' => null,
                'SERIAL_USR_RESUELVE' => null,
            ]);

            $reserva->NUMERO_RES = 'RES-' . now()->format('Y') . '-' . str_pad($reserva->SERIAL_RES, 6, '0', STR_PAD_LEFT);
            $reserva->save();

            $this->registrarMovimiento(
                $reserva,
                (int) $usuario->SERIAL_USR,
                'SOLICITUD',
                null,
                'PENDIENTE',
                'Solicitud de reserva creada.'
            );

            if ($conflictos->isNotEmpty()) {
                $numeros = $conflictos->pluck('NUMERO_RES')->implode(', ');
                $this->registrarMovimiento(
                    $reserva,
                    (int) $usuario->SERIAL_USR,
                    'CONFLICTO_DETECTADO',
                    'PENDIENTE',
                    'PENDIENTE',
                    'La solicitud coincide con reserva(s) aprobada(s): ' . $numeros . '. La solicitud fue registrada y deberá ser resuelta por el encargado.'
                );
            }

            return $reserva;
        });

        $mensaje = $conflictos->isNotEmpty()
            ? 'Solicitud registrada. Existe conflicto de horario; el encargado decidirá cuál reserva mantener.'
            : 'Solicitud de reserva registrada correctamente.';

        return redirect()->route('reservas.show', $reserva)->with('success', $mensaje);
    }

    public function show(Request $request, ReservaEspacio $reserva)
    {
        $usuario = $this->usuarioActual($request);
        $reserva->load(['espacio.encargado.empleado', 'solicitante.empleado', 'resuelve.empleado', 'movimientos.usuario.empleado']);

        $esSolicitante = (int) $reserva->SERIAL_USR_SOLICITA === (int) $usuario->SERIAL_USR;
        $puedeGestionar = $this->puedeGestionar($usuario, $reserva);

        $conflictos = $this->conflictosPara(
            (int) $reserva->SERIAL_ESP,
            $reserva->FECHA_INICIO_RES,
            $reserva->FECHA_FIN_RES,
            (int) $reserva->SERIAL_RES
        );

        return view('reservas.show', compact('reserva', 'conflictos', 'puedeGestionar', 'esSolicitante'));
    }

    public function horario(Request $request)
    {
        $espacios = Espacio::where('ESTADO_ESP', 'ACTIVO')
            ->orderBy('NOMBRE_ESP')
            ->get();

        $fecha = $request->input('fecha', now()->format('Y-m-d'));
        try {
            $dia = Carbon::createFromFormat('Y-m-d', $fecha)->startOfDay();
        } catch (\Throwable $e) {
            $dia = now()->startOfDay();
            $fecha = $dia->format('Y-m-d');
        }

        $espacioId = (int) $request->input('espacio', $espacios->first()?->SERIAL_ESP ?? 0);
        $espacioSeleccionado = $espacios->firstWhere('SERIAL_ESP', $espacioId);
        if (!$espacioSeleccionado && $espacios->isNotEmpty()) {
            $espacioSeleccionado = $espacios->first();
            $espacioId = (int) $espacioSeleccionado->SERIAL_ESP;
        }

        $inicioDia = $dia->copy()->startOfDay();
        $finDia = $dia->copy()->endOfDay();

        $reservas = collect();
        if ($espacioId) {
            $reservas = ReservaEspacio::with(['solicitante.empleado'])
                ->where('SERIAL_ESP', $espacioId)
                ->whereIn('ESTADO_RES', ['PENDIENTE', 'APROBADA'])
                ->where('FECHA_INICIO_RES', '<=', $finDia)
                ->where('FECHA_FIN_RES', '>=', $inicioDia)
                ->orderBy('FECHA_INICIO_RES')
                ->get()
                ->map(function ($reserva) use ($dia) {
                    $reserva->tiene_conflicto = $reserva->ESTADO_RES === 'PENDIENTE'
                        && $this->conflictosPara(
                            (int) $reserva->SERIAL_ESP,
                            $reserva->FECHA_INICIO_RES,
                            $reserva->FECHA_FIN_RES,
                            (int) $reserva->SERIAL_RES
                        )->isNotEmpty();
                    return $reserva;
                });
        }

        $horas = range(6, 22);

        return view('reservas.horario', compact(
            'espacios', 'espacioSeleccionado', 'espacioId', 'fecha', 'dia', 'reservas', 'horas'
        ));
    }

    public function horarioDatos(Request $request)
    {
        $datos = $request->validate([
            'espacio' => 'required|integer|exists:espacio,SERIAL_ESP',
            'fecha' => 'required|date_format:Y-m-d',
        ]);

        $dia = Carbon::createFromFormat('Y-m-d', $datos['fecha'])->startOfDay();
        $inicioDia = $dia->copy()->startOfDay();
        $finDia = $dia->copy()->endOfDay();

        $reservas = ReservaEspacio::with(['solicitante.empleado'])
            ->where('SERIAL_ESP', $datos['espacio'])
            ->whereIn('ESTADO_RES', ['PENDIENTE', 'APROBADA'])
            ->where('FECHA_INICIO_RES', '<=', $finDia)
            ->where('FECHA_FIN_RES', '>=', $inicioDia)
            ->orderBy('FECHA_INICIO_RES')
            ->get();

        return response()->json([
            'fecha' => $datos['fecha'],
            'reservas' => $reservas->map(function ($r) {
                $conflicto = $r->ESTADO_RES === 'PENDIENTE'
                    && $this->conflictosPara(
                        (int) $r->SERIAL_ESP,
                        $r->FECHA_INICIO_RES,
                        $r->FECHA_FIN_RES,
                        (int) $r->SERIAL_RES
                    )->isNotEmpty();

                return [
                    'id' => $r->SERIAL_RES,
                    'numero' => $r->NUMERO_RES,
                    'titulo' => $r->TITULO_RES,
                    'estado' => $r->ESTADO_RES,
                    'inicio' => optional($r->FECHA_INICIO_RES)->format('H:i'),
                    'fin' => optional($r->FECHA_FIN_RES)->format('H:i'),
                    'solicitante' => $r->solicitante?->nombre_completo ?? 'Usuario #' . $r->SERIAL_USR_SOLICITA,
                    'conflicto' => $conflicto,
                ];
            })->values(),
        ]);
    }

    public function gestion(Request $request)
    {
        $usuario = $this->usuarioActual($request);
        $query = ReservaEspacio::with(['espacio', 'solicitante.empleado'])
            ->orderByDesc('FECHA_CREACION_RES');

        if (!$this->esAdministrador($usuario)) {
            $query->whereHas('espacio', fn ($q) => $q->where('SERIAL_USR_ENCARGADO', $usuario->SERIAL_USR));
        }

        if ($request->filled('estado')) {
            $query->where('ESTADO_RES', $request->estado);
        }

        $reservas = $query->get()->map(function ($reserva) {
            $reserva->cantidad_conflictos = $this->conflictosPara(
                (int) $reserva->SERIAL_ESP,
                $reserva->FECHA_INICIO_RES,
                $reserva->FECHA_FIN_RES,
                (int) $reserva->SERIAL_RES
            )->count();
            return $reserva;
        });

        return view('reservas.gestion', compact('reservas', 'usuario'));
    }

    public function aprobar(Request $request, ReservaEspacio $reserva)
    {
        $usuario = $this->usuarioActual($request);
        $reserva->load('espacio');
        abort_unless($this->puedeGestionar($usuario, $reserva), 403);

        if ($reserva->ESTADO_RES !== 'PENDIENTE') {
            return back()->with('error', 'Solo se pueden aprobar solicitudes pendientes.');
        }

        $conflictos = $this->conflictosPara((int) $reserva->SERIAL_ESP, $reserva->FECHA_INICIO_RES, $reserva->FECHA_FIN_RES, (int) $reserva->SERIAL_RES);
        if ($conflictos->isNotEmpty()) {
            return back()->with('error', 'No se puede aprobar directamente porque existe una reserva aprobada en conflicto. Rechaza la nueva o usa “Cancelar anteriores y aprobar esta”.');
        }

        $obs = $request->validate(['observacion' => 'nullable|string|max:1000'])['observacion'] ?? 'Reserva aprobada por el encargado.';
        $anterior = $reserva->ESTADO_RES;
        $reserva->update([
            'ESTADO_RES' => 'APROBADA',
            'OBSERVACION_RES' => $obs,
            'FECHA_RESOLUCION_RES' => now(),
            'SERIAL_USR_RESUELVE' => $usuario->SERIAL_USR,
        ]);
        $this->registrarMovimiento($reserva, (int) $usuario->SERIAL_USR, 'APROBACION', $anterior, 'APROBADA', $obs);

        return back()->with('success', 'Reserva aprobada correctamente.');
    }

    public function rechazar(Request $request, ReservaEspacio $reserva)
    {
        $usuario = $this->usuarioActual($request);
        $reserva->load('espacio');
        abort_unless($this->puedeGestionar($usuario, $reserva), 403);
        $datos = $request->validate(['observacion' => 'required|string|max:1000']);

        if ($reserva->ESTADO_RES !== 'PENDIENTE') {
            return back()->with('error', 'Solo se pueden rechazar solicitudes pendientes.');
        }

        $anterior = $reserva->ESTADO_RES;
        $reserva->update([
            'ESTADO_RES' => 'RECHAZADA',
            'OBSERVACION_RES' => $datos['observacion'],
            'FECHA_RESOLUCION_RES' => now(),
            'SERIAL_USR_RESUELVE' => $usuario->SERIAL_USR,
        ]);
        $this->registrarMovimiento($reserva, (int) $usuario->SERIAL_USR, 'RECHAZO', $anterior, 'RECHAZADA', $datos['observacion']);

        return back()->with('success', 'Solicitud de reserva rechazada.');
    }

    public function reemplazar(Request $request, ReservaEspacio $reserva)
    {
        $usuario = $this->usuarioActual($request);
        $reserva->load('espacio');
        abort_unless($this->puedeGestionar($usuario, $reserva), 403);
        $datos = $request->validate(['observacion' => 'required|string|max:1000']);

        if ($reserva->ESTADO_RES !== 'PENDIENTE') {
            return back()->with('error', 'La nueva solicitud debe estar pendiente.');
        }

        $conflictos = $this->conflictosPara((int) $reserva->SERIAL_ESP, $reserva->FECHA_INICIO_RES, $reserva->FECHA_FIN_RES, (int) $reserva->SERIAL_RES);
        if ($conflictos->isEmpty()) {
            return back()->with('error', 'Ya no existen reservas aprobadas en conflicto. Puede aprobar la solicitud normalmente.');
        }

        DB::transaction(function () use ($conflictos, $reserva, $usuario, $datos) {
            foreach ($conflictos as $anterior) {
                $anterior->update([
                    'ESTADO_RES' => 'CANCELADA',
                    'OBSERVACION_RES' => 'Cancelada por conflicto a favor de ' . $reserva->NUMERO_RES . '. ' . $datos['observacion'],
                    'FECHA_RESOLUCION_RES' => now(),
                    'SERIAL_USR_RESUELVE' => $usuario->SERIAL_USR,
                ]);
                $this->registrarMovimiento(
                    $anterior,
                    (int) $usuario->SERIAL_USR,
                    'CANCELACION_POR_CONFLICTO',
                    'APROBADA',
                    'CANCELADA',
                    'Cancelada para dar prioridad a ' . $reserva->NUMERO_RES . '. ' . $datos['observacion']
                );
            }

            $reserva->update([
                'ESTADO_RES' => 'APROBADA',
                'OBSERVACION_RES' => $datos['observacion'],
                'FECHA_RESOLUCION_RES' => now(),
                'SERIAL_USR_RESUELVE' => $usuario->SERIAL_USR,
            ]);
            $this->registrarMovimiento(
                $reserva,
                (int) $usuario->SERIAL_USR,
                'APROBACION_CON_REEMPLAZO',
                'PENDIENTE',
                'APROBADA',
                'Se cancelaron ' . $conflictos->count() . ' reserva(s) aprobada(s) en conflicto. ' . $datos['observacion']
            );
        });

        return back()->with('success', 'Reserva aprobada. Las reservas anteriores en conflicto fueron canceladas y todo quedó registrado en el historial.');
    }

    public function cancelarPropia(Request $request, ReservaEspacio $reserva)
    {
        $usuario = $this->usuarioActual($request);
        abort_unless((int) $reserva->SERIAL_USR_SOLICITA === (int) $usuario->SERIAL_USR, 403);

        if (!in_array($reserva->ESTADO_RES, ['PENDIENTE', 'APROBADA'], true)) {
            return back()->with('error', 'Esta reserva ya no puede cancelarse.');
        }

        $datos = $request->validate(['observacion' => 'required|string|max:1000']);
        $anterior = $reserva->ESTADO_RES;
        $reserva->update([
            'ESTADO_RES' => 'CANCELADA',
            'OBSERVACION_RES' => $datos['observacion'],
            'FECHA_RESOLUCION_RES' => now(),
            'SERIAL_USR_RESUELVE' => $usuario->SERIAL_USR,
        ]);
        $this->registrarMovimiento($reserva, (int) $usuario->SERIAL_USR, 'CANCELACION_SOLICITANTE', $anterior, 'CANCELADA', $datos['observacion']);

        return back()->with('success', 'Reserva cancelada.');
    }
}
