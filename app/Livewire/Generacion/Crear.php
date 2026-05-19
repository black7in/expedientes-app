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
    public ?string $expediente_id = null;
    public ?Expediente $expediente = null;

    // ── Campos comunes ────────────────────────────────────────────────────────
    public string $tipo_documento = 'demanda';
    public string $instrucciones  = '';

    // ── Modo B — formulario manual ────────────────────────────────────────────
    public string $demandante   = '';
    public string $demandado    = '';
    public string $juzgado      = '';
    public string $ciudad       = 'Santa Cruz de la Sierra';
    public string $tipo_proceso = '';
    public string $hechos       = '';

    // ── Estado ────────────────────────────────────────────────────────────────
    public bool    $generando      = false;
    public ?string $borrador       = null;
    public ?string $generacion_id  = null;
    public ?int    $tokens_usados  = null;
    public ?int    $tiempo_ms      = null;
    public ?string $error          = null;

    public function mount(?Expediente $expediente = null): void
    {
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
        $this->borrador  = null;

        try {
            $datos = [
                'tipo_documento' => $this->tipo_documento,
                'creado_por'     => (string) auth()->id(),
                'instrucciones'  => $this->instrucciones ?: null,
            ];

            if ($this->modo === 'expediente') {
                $datos['expediente_id'] = $this->expediente_id;
            } else {
                $datos['demandante']   = $this->demandante;
                $datos['demandado']    = $this->demandado;
                $datos['juzgado']      = $this->juzgado;
                $datos['ciudad']       = $this->ciudad;
                $datos['tipo_proceso'] = $this->tipo_proceso;
                $datos['hechos']       = $this->hechos;
            }

            $resultado = (new GeneracionService())->generar($datos);

            $this->borrador      = $resultado['borrador'];
            $this->generacion_id = $resultado['generacion_id'];
            $this->tokens_usados = $resultado['tokens_usados'];
            $this->tiempo_ms     = $resultado['tiempo_ms'];
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->generando = false;
        }
    }

    public function copiarBorrador(): void
    {
        $this->dispatch('copiar-al-portapapeles', texto: $this->borrador);
    }

    public function nuevaGeneracion(): void
    {
        $this->borrador      = null;
        $this->generacion_id = null;
        $this->tokens_usados = null;
        $this->tiempo_ms     = null;
        $this->error         = null;
        $this->instrucciones = '';
    }

    protected function rules(): array
    {
        $reglas = [
            'tipo_documento' => 'required|in:demanda,memorial,contestacion,apelacion,nulidad,contrato',
        ];

        if ($this->modo === 'manual') {
            $reglas += [
                'demandante'   => 'required|min:3',
                'demandado'    => 'required|min:3',
                'tipo_proceso' => 'required|min:3',
                'hechos'       => 'required|min:20',
            ];
        }

        return $reglas;
    }

    public function render()
    {
        return view('livewire.generacion.crear');
    }
}
