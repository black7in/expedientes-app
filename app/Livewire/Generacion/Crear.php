<?php

namespace App\Livewire\Generacion;

use App\Jobs\GenerarDocumentoJob;
use App\Models\Expediente;
use App\Models\Generacion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Generar documento')]
class Crear extends Component
{
    // ── Estado del componente ─────────────────────────────────────────────────
    public string $estado = 'input';  // input | procesando | editor

    // ── Formulario ────────────────────────────────────────────────────────────
    public string $narracion    = '';
    public bool   $incluirJuris = false;

    // ── D1: Expediente vinculado ──────────────────────────────────────────────
    public ?string $expedienteId          = null;
    public string  $expedienteBusqueda    = '';
    public array   $expedientesResultados = [];
    public ?array  $expedienteSeleccionado = null;

    // ── Seguimiento del job ───────────────────────────────────────────────────
    public ?string $generacionId = null;
    public string  $pasoActual   = '';

    // ── Resultado ─────────────────────────────────────────────────────────────
    public ?string $documentoHtml    = null;
    public array   $advertencias     = [];
    public array   $validaciones     = [];
    public string  $contenidoEditado = '';

    // ── Error ─────────────────────────────────────────────────────────────────
    public ?string $errorMsg = null;

    // ── D1: Buscar expedientes (typeahead) ────────────────────────────────────
    public function updatedExpedienteBusqueda(string $valor): void
    {
        if (mb_strlen(trim($valor)) < 2) {
            $this->expedientesResultados = [];
            return;
        }

        $this->expedientesResultados = Expediente::with(['partes.persona'])
            ->where(function ($q) use ($valor) {
                $q->where('numero_expediente', 'ilike', "%{$valor}%");
            })
            ->orWhereHas('partes.persona', fn($q) => $q->where('nombre_completo', 'ilike', "%{$valor}%"))
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(fn($exp) => [
                'id'               => (string) $exp->id,
                'numero'           => $exp->numero_expediente ?? 'Sin número',
                'estado'           => $exp->estado,
                'partes_resumen'   => $exp->partes->take(2)
                    ->map(fn($p) => $p->persona->nombre_completo ?? '?')
                    ->implode(' / '),
            ])
            ->toArray();
    }

    public function seleccionarExpediente(string $id, string $numero, string $partesResumen): void
    {
        $this->expedienteId          = $id;
        $this->expedienteSeleccionado = ['numero' => $numero, 'partes' => $partesResumen];
        $this->expedienteBusqueda    = '';
        $this->expedientesResultados = [];
    }

    public function limpiarExpediente(): void
    {
        $this->expedienteId           = null;
        $this->expedienteSeleccionado = null;
        $this->expedienteBusqueda     = '';
        $this->expedientesResultados  = [];
    }

    // ── Generar ───────────────────────────────────────────────────────────────
    public function generar(): void
    {
        $this->validate(
            ['narracion' => 'required|min:100'],
            ['narracion.min' => 'Describe el caso con más detalle (mínimo 100 caracteres).']
        );

        $gen = Generacion::create([
            'usuario_id'             => (string) auth()->id(),
            'expediente_id'          => $this->expedienteId,
            'narracion'              => $this->narracion,
            'incluir_jurisprudencia' => $this->incluirJuris,
            'estado'                 => 'analizando',
        ]);

        $this->generacionId = (string) $gen->id;
        $this->pasoActual   = 'analizando';
        $this->estado       = 'procesando';
        $this->errorMsg     = null;

        GenerarDocumentoJob::dispatch($gen->id, $this->narracion, $this->incluirJuris, $this->expedienteId);
    }

    public function verificarEstado(): void
    {
        if (! $this->generacionId) return;

        $gen = Generacion::find($this->generacionId);
        if (! $gen) return;

        $this->pasoActual = $gen->estado;

        if ($gen->estado === 'completado') {
            $this->documentoHtml    = $gen->documento_html;
            $this->advertencias     = $gen->advertencias ?? [];
            $this->validaciones     = $gen->validaciones ?? [];
            $this->contenidoEditado = $gen->documento_html ?? '';
            $this->estado           = 'editor';
        } elseif ($gen->estado === 'error') {
            $this->errorMsg = $gen->error_msg ?? 'Error al generar el documento.';
            $this->estado   = 'input';
        }
    }

    public function guardar(): void
    {
        if (! $this->generacionId) return;

        Generacion::where('id', $this->generacionId)
            ->update(['documento_html' => $this->contenidoEditado]);

        $this->documentoHtml = $this->contenidoEditado;
    }

    public function calificar(int $rating): void
    {
        if (! $this->generacionId) return;

        Generacion::where('id', $this->generacionId)
            ->update(['calificacion' => $rating]);
    }

    public function nuevaGeneracion(): void
    {
        $this->reset([
            'narracion', 'incluirJuris', 'generacionId', 'pasoActual',
            'documentoHtml', 'advertencias', 'validaciones',
            'contenidoEditado', 'errorMsg',
            'expedienteId', 'expedienteBusqueda', 'expedientesResultados', 'expedienteSeleccionado',
        ]);
        $this->estado = 'input';
    }

    public function render()
    {
        return view('livewire.generacion.crear');
    }
}
