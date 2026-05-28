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
            $gen = \App\Models\Generacion::where('id', $generacion_id)
                ->where('usuario_id', (string) auth()->id())
                ->firstOrFail();

            abort_if(! $gen->documento_html, 404, 'Documento no disponible.');

            $bytes  = (new \App\Services\GeneradorService())->exportarDocx($gen->documento_html, "memorial_{$generacion_id}");
            return response($bytes, 200, [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => "attachment; filename=\"memorial_{$generacion_id}.docx\"",
            ]);
        })->name('descargar');
    });

    // Jurisprudencia TSJ — proxy SSE + interfaz
    Route::get('/jurisprudencia/stream', function (Illuminate\Http\Request $request) {
        $pregunta = $request->get('pregunta', '');
        $tsjUrl   = config('services.tsj.url');

        return response()->stream(function () use ($pregunta, $tsjUrl) {
            try {
                $response = \Illuminate\Support\Facades\Http::withOptions(['stream' => true])
                    ->timeout(180)
                    ->get("{$tsjUrl}/consulta/stream", ['pregunta' => $pregunta]);

                $body = $response->getBody();
                while (! $body->eof()) {
                    $chunk = $body->read(512);
                    if ($chunk) {
                        echo $chunk;
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                }
            } catch (\Exception $e) {
                echo 'data: ' . json_encode(['tipo' => 'error', 'mensaje' => 'Servicio de jurisprudencia no disponible.']) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    })->name('jurisprudencia.stream');

    Route::get('/jurisprudencia', \App\Livewire\Jurisprudencia\Consulta::class)->name('jurisprudencia.consulta');

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
