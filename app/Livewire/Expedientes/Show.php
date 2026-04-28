<?php

namespace App\Livewire\Expedientes;

use App\Models\Actuacion;
use App\Models\Expediente;
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

    public function render()
    {
        return view('livewire.expedientes.show');
    }
}
