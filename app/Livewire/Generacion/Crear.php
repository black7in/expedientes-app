<?php

namespace App\Livewire\Generacion;

use App\Models\Expediente;
use App\Services\GeneracionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Generar Documento')]
class Crear extends Component
{
    // ── Modo: 'expediente' (A) | 'manual' (B) ────────────────────────────────
    public string $modo = 'manual';

    // Modo A — desde expediente
    public ?string    $expediente_id = null;
    public ?Expediente $expediente   = null;

    // ── Configuración de generación ───────────────────────────────────────────
    public string $plantilla_id   = '';
    public string $formato_salida = 'estructurado';

    // ── Campos formulario ─────────────────────────────────────────────────────
    public string $demandante          = '';
    public string $demandado           = '';
    public string $juzgado             = '';
    public string $ciudad              = 'Santa Cruz de la Sierra';
    public string $tipo_proceso        = '';
    public string $hechos              = '';
    public string $instrucciones_extra = '';

    // ── Estado ────────────────────────────────────────────────────────────────
    public bool    $generando     = false;
    public ?string $contenido     = null;
    public ?string $generacion_id = null;
    public ?int    $tokens_total  = null;
    public ?array  $secciones     = null;
    public ?string $error         = null;

    // ── Datos cargados desde API ──────────────────────────────────────────────
    public array $plantillas = [];

    public function mount(?Expediente $expediente = null): void
    {
        try {
            $this->plantillas = (new GeneracionService())->getPlantillas();
            if (!empty($this->plantillas)) {
                $this->plantilla_id = $this->plantillas[0]['id'];
            }
        } catch (\Throwable) {
            $this->plantillas = [];
        }

        if ($expediente && $expediente->exists) {
            $this->expediente    = $expediente->load(['juzgado', 'tipoProceso', 'partes.persona']);
            $this->expediente_id = (string) $expediente->id;
            $this->modo          = 'expediente';
        }
    }

    public function generar(): void
    {
        $this->validate($this->rules());

        $this->generando = true;
        $this->error     = null;
        $this->contenido = null;
        $this->secciones = null;

        try {
            $resultado = (new GeneracionService())->generar([
                'usuario_id'         => (string) auth()->id(),
                'plantilla_id'       => $this->plantilla_id,
                'formato_salida'     => $this->formato_salida,
                'expediente_id'      => $this->expediente_id,
                'demandante'         => $this->demandante,
                'demandado'          => $this->demandado,
                'juzgado'            => $this->juzgado,
                'ciudad'             => $this->ciudad,
                'tipo_proceso'       => $this->tipo_proceso,
                'hechos'             => $this->hechos,
                'instrucciones_extra'=> $this->instrucciones_extra ?: null,
            ]);

            $this->contenido     = $resultado['contenido'];
            $this->generacion_id = $resultado['generacion_id'];
            $this->secciones     = $resultado['secciones'] ?? [];
            $this->tokens_total  = ($resultado['tokens_input'] ?? 0) + ($resultado['tokens_output'] ?? 0);

            // Navegar al Show page para poder editar y regenerar secciones
            $this->redirect(route('generacion.show', $resultado['generacion_id']), navigate: true);
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->generando = false;
        }
    }

    public function nuevaGeneracion(): void
    {
        $this->contenido          = null;
        $this->generacion_id      = null;
        $this->tokens_total       = null;
        $this->secciones          = null;
        $this->error              = null;
        $this->instrucciones_extra = '';
    }

    protected function rules(): array
    {
        return [
            'plantilla_id'   => 'required|uuid',
            'formato_salida' => 'required|in:estructurado,corrido',
            'demandante'     => 'required|min:3',
            'demandado'      => 'required|min:3',
            'hechos'         => 'required|min:50',
        ];
    }

    public function render()
    {
        return view('livewire.generacion.crear');
    }
}
