<?php

namespace App\Jobs;

use App\Models\Documento;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcesarDocumento implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(public readonly string $documentoId) {}

    public function handle(): void
    {
        $url = rtrim(config('services.ai.url'), '/') . "/api/documentos/{$this->documentoId}/extraer";

        try {
            $response = Http::timeout(300)->post($url);

            if (!$response->successful()) {
                Log::error('FastAPI extracción fallida', [
                    'documento_id' => $this->documentoId,
                    'status'       => $response->status(),
                    'body'         => $response->body(),
                ]);

                Documento::where('id', $this->documentoId)
                    ->update(['estado_extraccion' => 'error']);
            }
        } catch (\Exception $e) {
            Log::error('Error llamando a FastAPI', [
                'documento_id' => $this->documentoId,
                'error'        => $e->getMessage(),
            ]);

            Documento::where('id', $this->documentoId)
                ->update(['estado_extraccion' => 'error']);
        }
    }
}
