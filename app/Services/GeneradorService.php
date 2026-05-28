<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeneradorService
{
    private string $base;

    public function __construct()
    {
        $this->base = rtrim(config('services.ai.url'), '/');
    }

    public function analizarHechos(string $narracion): array
    {
        $response = Http::timeout(60)
            ->post("{$this->base}/analisis/hechos", ['narracion' => $narracion]);

        if ($response->failed()) {
            $detail = $response->json('detail');
            $msg    = is_array($detail) ? ($detail['mensaje'] ?? json_encode($detail)) : ($detail ?? $response->body());
            throw new RuntimeException("Análisis fallido: {$msg}");
        }

        return $response->json('analisis');
    }

    public function recuperar(array $analisis, bool $incluirJuris): array
    {
        $response = Http::timeout(90)
            ->post("{$this->base}/recuperacion", [
                'analisis'               => $analisis,
                'incluir_jurisprudencia' => $incluirJuris,
            ]);

        if ($response->failed()) {
            $detail = $response->json('detail') ?? $response->body();
            throw new RuntimeException("Recuperación fallida: " . (is_string($detail) ? $detail : json_encode($detail)));
        }

        return $response->json();
    }

    public function generar(array $analisis, array $recuperacion): array
    {
        $response = Http::timeout(120)
            ->post("{$this->base}/generacion", [
                'analisis'    => $analisis,
                'recuperacion' => $recuperacion,
            ]);

        if ($response->failed()) {
            $detail = $response->json('detail');
            $msg    = is_array($detail) ? ($detail['mensaje'] ?? json_encode($detail)) : ($detail ?? $response->body());
            throw new RuntimeException("Generación fallida: {$msg}");
        }

        return $response->json();
    }

    public function exportarDocx(string $html, string $nombre = 'memorial'): string
    {
        $response = Http::timeout(30)
            ->post("{$this->base}/export/docx", [
                'html'   => $html,
                'nombre' => $nombre,
            ]);

        if ($response->failed()) {
            throw new RuntimeException("Error al exportar el documento.");
        }

        return $response->body();
    }
}
