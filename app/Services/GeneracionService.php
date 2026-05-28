<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeneracionService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.ai.url');
    }

    // ── Generaciones ───────────────────────────────────────────────────────────

    /**
     * Crea una generación y la procesa (batch — bloquea hasta completar).
     * Retorna {generacion_id, estado, contenido, tokens_input, tokens_output, secciones}.
     *
     * @throws RuntimeException si el servicio IA falla.
     */
    public function generar(array $datos): array
    {
        $response = Http::timeout(180)
            ->post("{$this->baseUrl}/api/generaciones", $datos);

        if ($response->failed()) {
            $detalle = $response->json('detail') ?? $response->body();
            throw new RuntimeException("Servicio IA: {$detalle}");
        }

        return $response->json();
    }

    /**
     * Obtiene el detalle de una generación.
     */
    public function getGeneracion(string $id): array
    {
        $response = Http::timeout(15)->get("{$this->baseUrl}/api/generaciones/{$id}");

        if ($response->failed()) {
            throw new RuntimeException("Generación no encontrada");
        }

        return $response->json();
    }

    /**
     * Lista generaciones de un usuario.
     */
    public function listarGeneraciones(string $usuarioId): array
    {
        $response = Http::timeout(15)
            ->get("{$this->baseUrl}/api/generaciones", ['usuario_id' => $usuarioId]);

        if ($response->failed()) {
            throw new RuntimeException("Error al listar generaciones");
        }

        return $response->json();
    }

    /**
     * Devuelve las secciones con trazabilidad de una generación.
     */
    public function getSecciones(string $generacionId): array
    {
        $response = Http::timeout(15)
            ->get("{$this->baseUrl}/api/generaciones/{$generacionId}/secciones");

        if ($response->failed()) {
            throw new RuntimeException("Error al obtener secciones");
        }

        return $response->json();
    }

    /**
     * Regenera una sección con feedback opcional.
     */
    public function regenerarSeccion(string $generacionId, string $seccionId, ?string $feedback = null): array
    {
        $response = Http::timeout(120)
            ->post("{$this->baseUrl}/api/generaciones/{$generacionId}/secciones/{$seccionId}/regenerar", [
                'feedback' => $feedback,
            ]);

        if ($response->failed()) {
            $detalle = $response->json('detail') ?? $response->body();
            throw new RuntimeException("Error al regenerar sección: {$detalle}");
        }

        return $response->json();
    }

    /**
     * Guarda edición manual del documento completo.
     */
    public function actualizarContenido(string $generacionId, string $contenido): void
    {
        $response = Http::timeout(15)
            ->put("{$this->baseUrl}/api/generaciones/{$generacionId}", [
                'contenido' => $contenido,
            ]);

        if ($response->failed()) {
            throw new RuntimeException("Error al guardar contenido");
        }
    }

    /**
     * Guarda edición manual de una sección.
     */
    public function editarSeccion(string $generacionId, string $seccionId, string $contenido): void
    {
        $response = Http::timeout(15)
            ->put("{$this->baseUrl}/api/generaciones/{$generacionId}/secciones/{$seccionId}", [
                'contenido_editado' => $contenido,
            ]);

        if ($response->failed()) {
            throw new RuntimeException("Error al editar sección");
        }
    }

    /**
     * Cancela una generación en curso.
     */
    public function cancelar(string $generacionId): void
    {
        Http::timeout(10)->post("{$this->baseUrl}/api/generaciones/{$generacionId}/cancelar");
    }

    /**
     * Descarga el .docx de una generación. Retorna el contenido binario.
     */
    public function descargarDocx(string $generacionId): string
    {
        $response = Http::timeout(30)
            ->get("{$this->baseUrl}/api/generaciones/{$generacionId}/descargar");

        if ($response->failed()) {
            throw new RuntimeException("Error al descargar documento");
        }

        return $response->body();
    }

    /**
     * Elimina una generación.
     */
    public function eliminar(string $generacionId): void
    {
        Http::timeout(15)->delete("{$this->baseUrl}/api/generaciones/{$generacionId}");
    }

    // ── Plantillas ─────────────────────────────────────────────────────────────

    /**
     * Lista plantillas activas desde FastAPI.
     */
    public function getPlantillas(): array
    {
        $response = Http::timeout(15)
            ->get("{$this->baseUrl}/api/generaciones/plantillas/listar");

        if ($response->failed()) {
            throw new RuntimeException("Error al obtener plantillas");
        }

        return $response->json();
    }

    // ── Indexación de leyes y jurisprudencia (admin) ───────────────────────────

    /**
     * Indexa un PDF de ley boliviana (admin).
     */
    public function indexarLey($archivo, string $nombreLey, string $materia): array
    {
        $response = Http::timeout(300)
            ->attach('archivo', file_get_contents($archivo->getRealPath()), $archivo->getClientOriginalName())
            ->post("{$this->baseUrl}/api/admin/indexar-ley", [
                'nombre_ley' => $nombreLey,
                'materia'    => $materia,
            ]);

        if ($response->failed()) {
            $detalle = $response->json('detail') ?? $response->body();
            throw new RuntimeException("Error al indexar ley: {$detalle}");
        }

        return $response->json();
    }

    /**
     * Indexa un Auto Supremo del TSJ Bolivia (admin).
     */
    public function indexarJurisprudencia(array $datos): array
    {
        $response = Http::timeout(120)
            ->post("{$this->baseUrl}/api/admin/indexar-jurisprudencia", $datos);

        if ($response->failed()) {
            $detalle = $response->json('detail') ?? $response->body();
            throw new RuntimeException("Error al indexar jurisprudencia: {$detalle}");
        }

        return $response->json();
    }

    /**
     * Estadísticas de las colecciones RAG (admin).
     */
    public function getRagStats(): array
    {
        $response = Http::timeout(30)->get("{$this->baseUrl}/api/admin/rag-stats");

        if ($response->failed()) {
            throw new RuntimeException("Error al obtener estadísticas RAG");
        }

        return $response->json();
    }
}
