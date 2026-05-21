<?php

namespace App\Livewire\Generacion;

use App\Services\GeneracionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Ver documento generado')]
class Show extends Component
{
    public string  $generacion_id;
    public array   $generacion    = [];
    public array   $secciones     = [];
    public ?string $error         = null;

    // Edición del contenido completo
    public bool    $editando      = false;
    public string  $contenido_edit = '';
    public bool    $guardando     = false;

    // Regeneración de sección
    public ?string $seccion_regenerando = null;
    public string  $feedback            = '';
    public bool    $regenerando         = false;

    public function mount(string $generacion_id): void
    {
        $this->generacion_id = $generacion_id;
        $this->cargar();
    }

    public function cargar(): void
    {
        $svc = new GeneracionService();
        try {
            $this->generacion = $svc->getGeneracion($this->generacion_id);
            $this->secciones  = $svc->getSecciones($this->generacion_id);
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    // ── Edición del documento completo ────────────────────────────────────────

    public function iniciarEdicion(): void
    {
        $this->contenido_edit = $this->generacion['contenido_actual'] ?? '';
        $this->editando = true;
    }

    public function cancelarEdicion(): void
    {
        $this->editando = false;
        $this->contenido_edit = '';
    }

    public function guardarEdicion(): void
    {
        $this->guardando = true;
        try {
            (new GeneracionService())->actualizarContenido(
                $this->generacion_id,
                $this->contenido_edit
            );
            $this->generacion['contenido_actual'] = $this->contenido_edit;
            $this->generacion['estado']           = 'editado';
            $this->editando = false;
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->guardando = false;
        }
    }

    // ── Regeneración de sección ───────────────────────────────────────────────

    public function abrirRegeneracion(string $seccion_id): void
    {
        $this->seccion_regenerando = $seccion_id;
        $this->feedback = '';
    }

    public function cancelarRegeneracion(): void
    {
        $this->seccion_regenerando = null;
        $this->feedback = '';
    }

    public function regenerar(): void
    {
        if (!$this->seccion_regenerando) return;

        $this->regenerando = true;
        $this->error = null;

        try {
            (new GeneracionService())->regenerarSeccion(
                $this->generacion_id,
                $this->seccion_regenerando,
                $this->feedback ?: null
            );
            $this->seccion_regenerando = null;
            $this->feedback = '';
            $this->cargar();
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->regenerando = false;
        }
    }

    public function render()
    {
        return view('livewire.generacion.show');
    }
}
