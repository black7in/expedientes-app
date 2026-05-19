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

    /**
     * Genera un borrador jurídico vía RAG.
     * Modo A: expediente_id presente → contexto desde DB.
     * Modo B: datos manuales del formulario.
     *
     * @throws RuntimeException si el servicio IA falla.
     */
    public function generar(array $datos): array
    {
        $response = Http::timeout(120)
            ->post("{$this->baseUrl}/api/generar", $datos);

        if ($response->failed()) {
            $detalle = $response->json('detail') ?? $response->body();
            throw new RuntimeException("Servicio IA: {$detalle}");
        }

        return $response->json();
    }

    /**
     * Indexa un documento del estudio para búsqueda semántica futura.
     *
     * @throws RuntimeException si el servicio IA falla.
     */
    public function indexarDocumento(string $documentoId, string $tipoDoc): array
    {
        $response = Http::timeout(60)
            ->post("{$this->baseUrl}/api/documentos/{$documentoId}/indexar", [
                'tipo_doc' => $tipoDoc,
            ]);

        if ($response->failed()) {
            $detalle = $response->json('detail') ?? $response->body();
            throw new RuntimeException("Error al indexar: {$detalle}");
        }

        return $response->json();
    }

    /**
     * Indexa un PDF de ley boliviana (admin).
     *
     * @throws RuntimeException si el servicio IA falla.
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
     *
     * @throws RuntimeException si el servicio IA falla.
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
     *
     * @throws RuntimeException si el servicio IA falla.
     */
    public function getRagStats(): array
    {
        $response = Http::timeout(30)
            ->get("{$this->baseUrl}/api/admin/rag-stats");

        if ($response->failed()) {
            throw new RuntimeException("Error al obtener estadísticas RAG");
        }

        return $response->json();
    }

    // ── Plantillas ─────────────────────────────────────────────────────────────

    /**
     * Lista plantillas activas desde FastAPI (opcionalmente filtradas por materia).
     */
    public function getPlantillas(?string $materia = null): array
    {
        $response = Http::timeout(15)
            ->get("{$this->baseUrl}/api/admin/plantillas", $materia ? ['materia' => $materia] : []);

        if ($response->failed()) {
            throw new RuntimeException("Error al obtener plantillas");
        }

        return $response->json();
    }

    /**
     * Obtiene el contenido completo de una plantilla.
     */
    public function getPlantilla(string $id): array
    {
        $response = Http::timeout(15)->get("{$this->baseUrl}/api/admin/plantillas/{$id}");

        if ($response->failed()) {
            throw new RuntimeException("Plantilla no encontrada");
        }

        return $response->json();
    }

    /**
     * Crea o actualiza una plantilla.
     */
    public function guardarPlantilla(array $datos, ?string $id = null): array
    {
        if ($id) {
            $response = Http::timeout(15)->put("{$this->baseUrl}/api/admin/plantillas/{$id}", $datos);
        } else {
            $response = Http::timeout(15)->post("{$this->baseUrl}/api/admin/plantillas", $datos);
        }

        if ($response->failed()) {
            $detalle = $response->json('detail') ?? $response->body();
            throw new RuntimeException("Error al guardar plantilla: {$detalle}");
        }

        return $response->json();
    }

    /**
     * Marca una plantilla como default para su materia+tipo_documento.
     */
    public function setPlantillaDefault(string $id): array
    {
        $response = Http::timeout(15)->post("{$this->baseUrl}/api/admin/plantillas/{$id}/default");

        if ($response->failed()) {
            throw new RuntimeException("Error al establecer plantilla como default");
        }

        return $response->json();
    }

    /**
     * Desactiva (soft-delete) una plantilla.
     */
    public function eliminarPlantilla(string $id): void
    {
        $response = Http::timeout(15)->delete("{$this->baseUrl}/api/admin/plantillas/{$id}");

        if ($response->failed()) {
            throw new RuntimeException("Error al eliminar plantilla");
        }
    }
}
