<?php

namespace App\Http\Controllers;

use App\Models\UsuarioInstitucional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->session()->has('usuario_serial')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|max:50',
            'clave' => 'required|string|max:255',
        ]);

        $usuario = UsuarioInstitucional::with(['empleado', 'perfil'])
            ->where('CODIGO_USR', $datos['codigo'])
            ->first();

        if (!$usuario || !$this->claveValida($datos['clave'], (string) $usuario->CLAVE_USR)) {
            return back()
                ->withInput($request->only('codigo'))
                ->withErrors(['codigo' => 'Usuario o contraseña incorrectos.']);
        }

        if ($usuario->ESTADO_USR && strtoupper(trim((string) $usuario->ESTADO_USR)) !== 'ACTIVO') {
            return back()
                ->withInput($request->only('codigo'))
                ->withErrors(['codigo' => 'El usuario no se encuentra activo.']);
        }

        $request->session()->regenerate();
        $request->session()->put('usuario_serial', (int) $usuario->SERIAL_USR);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function claveValida(string $ingresada, string $guardada): bool
    {
        if ($guardada === '') {
            return false;
        }

        // Permite hashes modernos si la base institucional los utiliza.
        if (str_starts_with($guardada, '$2y$') || str_starts_with($guardada, '$2a$') || str_starts_with($guardada, '$argon2')) {
            return Hash::check($ingresada, $guardada);
        }

        // La copia local de pruebas actualmente utiliza valores directos (ej. PRUEBA123).
        // Cuando se conozca el mecanismo real de cifrado institucional, este punto es el único que debe adaptarse.
        return hash_equals($guardada, $ingresada);
    }
}
