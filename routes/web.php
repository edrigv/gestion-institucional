<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TipoRequerimientoController;
use App\Http\Controllers\RequerimientoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\ReservaEspacioController;
use App\Http\Controllers\EspacioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Middleware\UsuarioAutenticado;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1') // Máximo 5 intentos de inicio de sesión por minuto por IP (Rate Limiting / Anti-Brute-Force)
    ->name('login.store');

Route::middleware([UsuarioAutenticado::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('tipos-requerimiento', TipoRequerimientoController::class);
    Route::resource('requerimientos', RequerimientoController::class);

    Route::post('/requerimientos/{requerimiento}/derivar', [RequerimientoController::class, 'derivar'])->name('requerimientos.derivar');
    Route::post('/requerimientos/{requerimiento}/cambiar-estado', [RequerimientoController::class, 'cambiarEstado'])->name('requerimientos.cambiar-estado');
    Route::post('/requerimientos/{requerimiento}/archivos', [RequerimientoController::class, 'subirArchivo'])->name('requerimientos.archivos.store');
    Route::post('/requerimientos/{requerimiento}/asignar', [RequerimientoController::class, 'asignar'])->name('requerimientos.asignar');
    Route::post('/requerimientos/{requerimiento}/solicitar-correccion', [RequerimientoController::class, 'solicitarCorreccion'])->name('requerimientos.solicitar-correccion');
    Route::post('/requerimientos/{requerimiento}/corregir', [RequerimientoController::class, 'corregir'])->name('requerimientos.corregir');
    Route::post('/requerimientos/{requerimiento}/atender', [RequerimientoController::class, 'atender'])->name('requerimientos.atender');
    Route::post('/requerimientos/{requerimiento}/cerrar', [RequerimientoController::class, 'cerrar'])->name('requerimientos.cerrar');
    Route::post('/requerimientos/{requerimiento}/solicitar-aprobacion', [RequerimientoController::class, 'solicitarAprobacion'])->name('requerimientos.solicitar-aprobacion');
    Route::post('/requerimientos/{requerimiento}/aprobar', [RequerimientoController::class, 'aprobar'])->name('requerimientos.aprobar');
    Route::post('/requerimientos/{requerimiento}/rechazar', [RequerimientoController::class, 'rechazar'])->name('requerimientos.rechazar');
    Route::post('/requerimientos/{requerimiento}/solicitar-firma', [RequerimientoController::class, 'solicitarFirma'])->name('requerimientos.solicitar-firma');
    Route::post('/requerimientos/{requerimiento}/documentos', [RequerimientoController::class, 'crearDocumento'])->name('requerimientos.documentos.store');
    Route::post('/documentos/{documento}/archivo', [RequerimientoController::class, 'subirArchivoDocumento'])->name('documentos.archivo.store');

    Route::get('/mensajes', [MensajeController::class, 'index'])->name('mensajes.index');
    Route::get('/mensajes/enviados', [MensajeController::class, 'enviados'])->name('mensajes.enviados');
    Route::get('/mensajes/nuevo', [MensajeController::class, 'create'])->name('mensajes.create');
    Route::post('/mensajes', [MensajeController::class, 'store'])->name('mensajes.store');
    Route::get('/mensajes/{mensaje}', [MensajeController::class, 'show'])->name('mensajes.show');

    // Reservas de espacios
    Route::get('/reservas', [ReservaEspacioController::class, 'index'])->name('reservas.index');
    Route::get('/reservas/nueva', [ReservaEspacioController::class, 'create'])->name('reservas.create');
    Route::post('/reservas/verificar', [ReservaEspacioController::class, 'verificar'])->name('reservas.verificar');
    Route::post('/reservas', [ReservaEspacioController::class, 'store'])->name('reservas.store');
    Route::get('/reservas/gestion', [ReservaEspacioController::class, 'gestion'])->name('reservas.gestion');
    Route::get('/reservas/horario', [ReservaEspacioController::class, 'horario'])->name('reservas.horario');
    Route::get('/reservas/horario/datos', [ReservaEspacioController::class, 'horarioDatos'])->name('reservas.horario.datos');
    Route::get('/reservas/{reserva}', [ReservaEspacioController::class, 'show'])->name('reservas.show');
    Route::post('/reservas/{reserva}/aprobar', [ReservaEspacioController::class, 'aprobar'])->name('reservas.aprobar');
    Route::post('/reservas/{reserva}/rechazar', [ReservaEspacioController::class, 'rechazar'])->name('reservas.rechazar');
    Route::post('/reservas/{reserva}/reemplazar', [ReservaEspacioController::class, 'reemplazar'])->name('reservas.reemplazar');
    Route::post('/reservas/{reserva}/cancelar', [ReservaEspacioController::class, 'cancelarPropia'])->name('reservas.cancelar');

    // Configuración y Administración Institucional
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/configuracion', fn() => redirect()->route('admin.index'))->name('configuracion.index');
    Route::post('/admin/departamentos', [DepartamentoController::class, 'store'])->name('departamentos.store');
    Route::put('/admin/departamentos/{departamento}', [DepartamentoController::class, 'update'])->name('departamentos.update');

    // Configuración de espacios (administrador)
    Route::get('/espacios', [EspacioController::class, 'index'])->name('espacios.index');
    Route::get('/espacios/nuevo', [EspacioController::class, 'create'])->name('espacios.create');
    Route::post('/espacios', [EspacioController::class, 'store'])->name('espacios.store');
    Route::get('/espacios/{espacio}/editar', [EspacioController::class, 'edit'])->name('espacios.edit');
    Route::put('/espacios/{espacio}', [EspacioController::class, 'update'])->name('espacios.update');
});
