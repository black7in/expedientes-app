<?php

namespace App\Livewire\Config;

use App\Models\TipoProceso;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Tipos de proceso')]
class TiposProceso extends Component
{
    public string $expandido = '';

    public function toggleExpandido(string $id): void
    {
        $this->expandido = ($this->expandido === $id) ? '' : $id;
    }

    public function toggleActivo(string $id): void
    {
        $tipo = TipoProceso::findOrFail($id);
        $tipo->update(['activo' => ! $tipo->activo]);
    }

    public function render()
    {
        return view('livewire.config.tipos-proceso', [
            'tiposProceso' => TipoProceso::with('etapas')->orderBy('nombre')->get(),
        ]);
    }
}
