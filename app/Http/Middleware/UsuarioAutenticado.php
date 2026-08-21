<?php

namespace App\Http\Middleware;

use App\Models\UsuarioInstitucional;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class UsuarioAutenticado
{
    public function handle(Request $request, Closure $next): Response
    {
        $serial = $request->session()->get('usuario_serial');

        if (!$serial) {
            return redirect()->guest(route('login'));
        }

        $usuario = UsuarioInstitucional::with(['empleado', 'perfil'])->find($serial);

        if (!$usuario) {
            $request->session()->forget('usuario_serial');
            return redirect()->route('login')->withErrors([
                'codigo' => 'La sesión ya no corresponde a un usuario válido.',
            ]);
        }

        $request->attributes->set('usuarioSesion', $usuario);
        View::share('usuarioSesion', $usuario);

        try {
            $pendientesReq = \App\Models\Requerimiento::whereIn('ESTADO_REQ', ['ENVIADO', 'RECIBIDO', 'PENDIENTE_APROBACION', 'PENDIENTE_FIRMA', 'PENDIENTE_CORRECCION'])->count();
            $pendientesRes = \App\Models\ReservaEspacio::where('ESTADO_RES', 'PENDIENTE')->count();
            $totalMensajes = \App\Models\MensajeUsuario::where('SERIAL_USR_RECIBE', $usuario->SERIAL_USR)->count();

            View::share('badgeReqPendientes', $pendientesReq);
            View::share('badgeResPendientes', $pendientesRes);
            View::share('badgeTotalMensajes', $totalMensajes);
        } catch (\Throwable $e) {
            View::share('badgeReqPendientes', 0);
            View::share('badgeResPendientes', 0);
            View::share('badgeTotalMensajes', 0);
        }

        return $next($request);
    }
}
