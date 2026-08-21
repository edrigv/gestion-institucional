<?php

namespace App\Http\Controllers;

use App\Models\MensajeUsuario;
use App\Models\UsuarioInstitucional;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    private function usuarioActual(Request $request): UsuarioInstitucional
    {
        return $request->attributes->get('usuarioSesion');
    }

    private function usuariosDestino(int $actual)
    {
        return UsuarioInstitucional::with(['empleado', 'perfil'])
            ->where('SERIAL_USR', '<>', $actual)
            ->whereNotNull('SERIAL_EPL')
            ->get()
            ->sortBy(fn ($usuario) => mb_strtolower($usuario->nombre_completo))
            ->values();
    }

    public function index(Request $request)
    {
        $usuarioActual = $this->usuarioActual($request);

        $mensajes = MensajeUsuario::with(['remitente.empleado', 'destinatario.empleado', 'requerimiento'])
            ->where('SERIAL_USR_RECIBE', $usuarioActual->SERIAL_USR)
            ->orderByDesc('FECHA_HORA_MEN')
            ->get();

        $noLeidos = $mensajes->whereNull('FECHA_LECTURA_MEN')->count();

        return view('mensajes.index', compact('usuarioActual', 'mensajes', 'noLeidos'));
    }

    public function enviados(Request $request)
    {
        $usuarioActual = $this->usuarioActual($request);

        $mensajes = MensajeUsuario::with(['remitente.empleado', 'destinatario.empleado', 'requerimiento'])
            ->where('SERIAL_USR_ENVIA', $usuarioActual->SERIAL_USR)
            ->orderByDesc('FECHA_HORA_MEN')
            ->get();

        return view('mensajes.enviados', compact('usuarioActual', 'mensajes'));
    }

    public function create(Request $request)
    {
        $usuarioActual = $this->usuarioActual($request);
        $usuarios = $this->usuariosDestino((int) $usuarioActual->SERIAL_USR);
        $destinatario = $request->integer('destinatario') ?: null;
        $serialReq = $request->integer('requerimiento') ?: null;

        return view('mensajes.create', compact('usuarios', 'usuarioActual', 'destinatario', 'serialReq'));
    }

    public function store(Request $request)
    {
        $usuarioActual = $this->usuarioActual($request);

        $datos = $request->validate([
            'SERIAL_USR_RECIBE' => [
                'required',
                'integer',
                'exists:usuario,SERIAL_USR',
                'not_in:' . $usuarioActual->SERIAL_USR,
            ],
            'ASUNTO_MEN' => 'required|string|max:180',
            'CONTENIDO_MEN' => 'required|string|max:5000',
            'SERIAL_REQ' => 'nullable|integer|exists:requerimiento,SERIAL_REQ',
        ]);

        $mensaje = MensajeUsuario::create([
            'SERIAL_USR_ENVIA' => $usuarioActual->SERIAL_USR,
            'SERIAL_USR_RECIBE' => $datos['SERIAL_USR_RECIBE'],
            'ASUNTO_MEN' => $datos['ASUNTO_MEN'],
            'CONTENIDO_MEN' => $datos['CONTENIDO_MEN'],
            'ESTADO_MEN' => 'ENVIADO',
            'FECHA_HORA_MEN' => now(),
            'FECHA_LECTURA_MEN' => null,
            'SERIAL_REQ' => $datos['SERIAL_REQ'] ?? null,
        ]);

        return redirect()
            ->route('mensajes.show', $mensaje)
            ->with('success', 'Mensaje enviado correctamente.');
    }

    public function show(Request $request, MensajeUsuario $mensaje)
    {
        $usuarioActual = $this->usuarioActual($request);

        if (!in_array((int) $usuarioActual->SERIAL_USR, [
            (int) $mensaje->SERIAL_USR_ENVIA,
            (int) $mensaje->SERIAL_USR_RECIBE,
        ], true)) {
            abort(403, 'No tienes permiso para ver este mensaje.');
        }

        $mensaje->load(['remitente.empleado', 'destinatario.empleado', 'requerimiento']);

        if ((int) $usuarioActual->SERIAL_USR === (int) $mensaje->SERIAL_USR_RECIBE && !$mensaje->FECHA_LECTURA_MEN) {
            $mensaje->update([
                'ESTADO_MEN' => 'LEIDO',
                'FECHA_LECTURA_MEN' => now(),
            ]);
        }

        return view('mensajes.show', compact('mensaje', 'usuarioActual'));
    }
}
