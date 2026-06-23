<?php

namespace App\Jobs;

use App\Models\Generacion;
use App\Services\GeneradorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerarDocumentoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 1;

    public function __construct(
        public string  $generacionId,
        public string  $narracion,
        public bool    $incluirJurisprudencia,
        public ?string $expedienteId = null,
    ) {}

    public function handle(GeneradorService $service): void
    {
        $gen = Generacion::findOrFail($this->generacionId);

        $gen->update(['estado' => 'analizando']);
        $analisis = $service->analizarHechos($this->narracion);

        $gen->update(['estado' => 'recuperando']);
        $recuperacion = $service->recuperar($analisis, $this->incluirJurisprudencia);

        $gen->update(['estado' => 'generando']);
        $resultado = $service->generar($analisis, $recuperacion, $this->expedienteId);

        // Construir fuentes a partir del resultado de recuperación
        $fuentes = [
            'leyes' => array_map(fn($l) => [
                'ley'     => $l['ley'] ?? '',
                'articulo'=> $l['numero_articulo'] ?? '',
                'titulo'  => $l['titulo_articulo'] ?? null,
            ], $recuperacion['leyes'] ?? []),
            'leyes_procesales' => array_map(fn($l) => [
                'ley'     => $l['ley'] ?? '',
                'articulo'=> $l['numero_articulo'] ?? '',
                'titulo'  => $l['titulo_articulo'] ?? null,
            ], $recuperacion['leyes_procesales'] ?? []),
            'molde_formato' => $recuperacion['molde']['formato'] ?? null,
        ];

        $gen->update([
            'estado'         => 'completado',
            'documento_html' => $resultado['documento_html'],
            'molde_usado_id' => $resultado['molde_usado_id'] ?? null,
            'advertencias'   => $resultado['advertencias'] ?? [],
            'validaciones'   => $resultado['validaciones'] ?? [],
            'fuentes'        => $fuentes,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Generacion::where('id', $this->generacionId)->update([
            'estado'    => 'error',
            'error_msg' => $e->getMessage(),
        ]);
    }
}
