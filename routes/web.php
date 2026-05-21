<?php

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirige raíz según estado de auth
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// ── Rutas públicas (solo invitados) ───────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// ── Logout ────────────────────────────────────────────────────────────────────
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

// ── Rutas protegidas (autenticado + activo) ───────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Expedientes (PB-13, PB-14, PB-18)
    Route::prefix('expedientes')->name('expedientes.')->group(function () {
        Route::get('/', \App\Livewire\Expedientes\Index::class)->name('index');
        Route::get('/nuevo', \App\Livewire\Expedientes\Create::class)->name('create');
        Route::get('/{expediente}', \App\Livewire\Expedientes\Show::class)->name('show');
        Route::get('/{expediente}/editar', \App\Livewire\Expedientes\Edit::class)->name('edit');
    });

    // Partes procesales — Personas
    Route::prefix('personas')->name('personas.')->group(function () {
        Route::get('/', \App\Livewire\Personas\Index::class)->name('index');
        Route::get('/nueva', \App\Livewire\Personas\Create::class)->name('create');
        Route::get('/{persona}/editar', \App\Livewire\Personas\Edit::class)->name('edit');
    });

    // Documentos (PB-4, PB-5, PB-6, PB-7)
    Route::prefix('documentos')->name('documentos.')->group(function () {
        Route::get('/', \App\Livewire\Documentos\Index::class)->name('index');
        Route::get('/subir', \App\Livewire\Documentos\Upload::class)->name('upload');
        Route::get('/{documento}', \App\Livewire\Documentos\Show::class)->name('show');
    });

    // Generación de documentos con IA (RAG)
    Route::prefix('generacion')->name('generacion.')->group(function () {
        Route::get('/', \App\Livewire\Generacion\Index::class)->name('index');
        Route::get('/nueva', \App\Livewire\Generacion\Crear::class)->name('crear');
        Route::get('/{generacion_id}', \App\Livewire\Generacion\Show::class)->name('show');
        Route::get('/{generacion_id}/descargar', function (string $generacion_id) {
            $bytes  = (new \App\Services\GeneracionService())->descargarDocx($generacion_id);
            $nombre = "documento_{$generacion_id}.docx";
            return response($bytes, 200, [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => "attachment; filename=\"{$nombre}\"",
            ]);
        })->name('descargar');
    });

    // Desde expediente → generar documento
    Route::get('/expedientes/{expediente}/generar', \App\Livewire\Generacion\Crear::class)
        ->name('generacion.desde-expediente');

    // Administración (solo admin)
    Route::middleware('role:administrador')->group(function () {
        Route::get('/usuarios', \App\Livewire\Usuarios\Index::class)->name('usuarios.index');
        Route::get('/usuarios/nuevo', \App\Livewire\Usuarios\Create::class)->name('usuarios.create');
        Route::get('/usuarios/{usuario}/editar', \App\Livewire\Usuarios\Edit::class)->name('usuarios.edit');

        Route::prefix('configuracion')->name('config.')->group(function () {
            Route::get('/tipos-proceso', \App\Livewire\Config\TiposProceso::class)->name('tipos-proceso');
            Route::get('/juzgados', \App\Livewire\Config\Juzgados::class)->name('juzgados');
            Route::get('/base-conocimiento', \App\Livewire\Config\BaseConocimiento::class)->name('base-conocimiento');
        });
    });
});
