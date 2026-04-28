<?php

namespace App\Livewire\Expedientes;

use App\Models\Actuacion;
use App\Models\Expediente;
use App\Models\Persona;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Expediente')]
class Show extends Component
{
    public Expediente $expediente;

    public string $descripcion = '';
    public string $fecha = '';
    public string $tipo_actuacion = 'escrito';
    public bool $showActuacionForm = false;

    // Modal agregar parte
    public bool   $showModalParte       = false;
    public string $buscarPersonaModal   = '';
    public array  $resultadosModal      = [];
    public string $personaSeleccionadaId   = '';
    public string $personaSeleccionadaNombre = '';
    public string $rolProcesal          = 'demandante';
    public bool   $esCliente            = false;

    public function mount(Expediente $expediente): void
    {
        $this->expediente = $expediente->load([
            'juzgado', 'tipoProceso', 'abogado',
            'partes.persona', 'actuaciones.usuario', 'documentos.usuario',
        ]);
        $this->fecha = now()->format('Y-m-d\TH:i');
    }

    public function toggleActuacionForm(): void
    {
        $this->showActuacionForm = !$this->showActuacionForm;
        $this->resetErrorBag();
    }

    public function guardarActuacion(): void
    {
        $this->validate([
            'descripcion'    => 'required|min:10',
            'fecha'          => 'required|date',
            'tipo_actuacion' => 'required|in:escrito,audiencia,resolucion,notificacion,recurso,otro',
        ]);

        $this->expediente->actuaciones()->create([
            'usuario_id'     => auth()->id(),
            'descripcion'    => $this->descripcion,
            'fecha'          => $this->fecha,
            'tipo_actuacion' => $this->tipo_actuacion,
        ]);

        $this->descripcion = '';
        $this->fecha = now()->format('Y-m-d\TH:i');
        $this->showActuacionForm = false;

        $this->expediente = $this->expediente->fresh([
            'juzgado', 'tipoProceso', 'abogado',
            'partes.persona', 'actuaciones.usuario', 'documentos.usuario',
        ]);
    }

    // ── Modal partes ──────────────────────────────────────────────────────────

    public function abrirModalParte(): void
    {
        $this->showModalParte = true;
        $this->reset('buscarPersonaModal', 'resultadosModal', 'personaSeleccionadaId', 'personaSeleccionadaNombre', 'rolProcesal', 'esCliente');
        $this->rolProcesal = 'demandante';
        $this->resetErrorBag();
    }

    public function cerrarModalParte(): void
    {
        $this->showModalParte = false;
    }

    public function updatedBuscarPersonaModal(): void
    {
        if (strlen($this->buscarPersonaModal) < 2) {
            $this->resultadosModal = [];
            return;
        }

        $term = '%' . $this->buscarPersonaModal . '%';
        $this->resultadosModal = Persona::where('nombre_completo', 'ilike', $term)
            ->orWhere('ci_nit', 'ilike', $term)
            ->limit(6)
            ->get(['id', 'nombre_completo', 'ci_nit'])
            ->toArray();
    }

    public function seleccionarPersonaModal(string $id, string $nombre): void
    {
        $this->personaSeleccionadaId     = $id;
        $this->personaSeleccionadaNombre = $nombre;
        $this->buscarPersonaModal        = '';
        $this->resultadosModal           = [];
    }

    public function agregarParte(): void
    {
        $this->validate([
            'personaSeleccionadaId' => 'required|exists:personas,id',
            'rolProcesal'           => 'required|in:demandante,demandado',
        ], [
            'personaSeleccionadaId.required' => 'Seleccioná una persona.',
        ]);

        // Evitar duplicados
        $existe = $this->expediente->partes()
            ->where('persona_id', $this->personaSeleccionadaId)
            ->exists();

        if ($existe) {
            $this->addError('personaSeleccionadaId', 'Esta persona ya es parte de este expediente.');
            return;
        }

        if ($this->esCliente) {
            $this->expediente->partes()->update(['es_cliente' => false]);
        }

        $this->expediente->partes()->create([
            'persona_id'   => $this->personaSeleccionadaId,
            'rol_procesal' => $this->rolProcesal,
            'es_cliente'   => $this->esCliente,
        ]);

        $this->showModalParte = false;
        $this->expediente = $this->expediente->fresh([
            'juzgado', 'tipoProceso', 'abogado',
            'partes.persona', 'actuaciones.usuario', 'documentos.usuario',
        ]);
    }

    public function render()
    {
        return view('livewire.expedientes.show');
    }
}
