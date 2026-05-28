<?php

namespace App\Livewire\Generacion;

use App\Models\Generacion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Documento generado')]
class Show extends Component
{
    public string      $generacion_id;
    public ?Generacion $generacion      = null;
    public string      $contenidoEditado = '';
    public bool        $guardando        = false;
    public bool        $guardado         = false;

    public function mount(string $generacion_id): void
    {
        $gen = Generacion::where('id', $generacion_id)
            ->where('usuario_id', (string) auth()->id())
            ->firstOrFail();

        $this->generacion_id    = $generacion_id;
        $this->generacion       = $gen;
        $this->contenidoEditado = $gen->documento_html ?? '';
    }

    public function guardar(): void
    {
        $this->guardando = true;

        $this->generacion->update([
            'documento_html' => $this->contenidoEditado,
        ]);

        $this->guardando = false;
        $this->guardado  = true;
    }

    public function render()
    {
        return view('livewire.generacion.show');
    }
}
