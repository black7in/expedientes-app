<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerarResumenExpedienteJob implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public int $backoff = 10;

    public function __construct(public readonly string $expedienteId) {}

    public function handle(): void
    {
        $baseUrl = rtrim(config('services.ai.url'), '/');

        try {
            $response = Http::timeout(120)
                ->post("{$baseUrl}/api/expedientes/{$this->expedienteId}/resumir");

            if (!$response->successful()) {
                Log::error('Error al generar resumen del expediente', [
                    'expediente_id' => $this->expedienteId,
                    'status'        => $response->status(),
                    'body'          => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Excepción al generar resumen del expediente', [
                'expediente_id' => $this->expedienteId,
                'error'         => $e->getMessage(),
            ]);
        }
    }
}
