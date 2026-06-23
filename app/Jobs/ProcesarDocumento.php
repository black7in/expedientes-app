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
        $baseUrl = rtrim(config('services.ai.url'), '/');

        if (!$this->_llamarExtraer($baseUrl)) {
            return;
        }

        $this->_llamarNer($baseUrl);
    }

    private function _llamarExtraer(string $baseUrl): bool
    {
        try {
            $response = Http::timeout(300)->post("{$baseUrl}/api/documentos/{$this->documentoId}/extraer");

            if (!$response->successful()) {
                Log::error('FastAPI extracción fallida', [
                    'documento_id' => $this->documentoId,
                    'status'       => $response->status(),
                    'body'         => $response->body(),
                ]);
                Documento::where('id', $this->documentoId)->update(['estado_extraccion' => 'error']);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error llamando a FastAPI (extraer)', [
                'documento_id' => $this->documentoId,
                'error'        => $e->getMessage(),
            ]);
            Documento::where('id', $this->documentoId)->update(['estado_extraccion' => 'error']);
            return false;
        }
    }

    private function _llamarNer(string $baseUrl): void
    {
        try {
            $response = Http::timeout(120)->post("{$baseUrl}/api/documentos/{$this->documentoId}/ner");

            if (!$response->successful()) {
                Log::error('FastAPI NER fallido', [
                    'documento_id' => $this->documentoId,
                    'status'       => $response->status(),
                    'body'         => $response->body(),
                ]);
                Documento::where('id', $this->documentoId)->update(['estado_extraccion' => 'error']);
            }
        } catch (\Exception $e) {
            Log::error('Error llamando a FastAPI (ner)', [
                'documento_id' => $this->documentoId,
                'error'        => $e->getMessage(),
            ]);
            Documento::where('id', $this->documentoId)->update(['estado_extraccion' => 'error']);
        }
    }
}
