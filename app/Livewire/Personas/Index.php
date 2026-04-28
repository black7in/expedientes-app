<?php

namespace App\Livewire\Personas;

use App\Models\Persona;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Partes / Personas')]
class Index extends Component
{
    use WithPagination;

    public string $buscar = '';
    public string $filtroTipo = '';

    public function updatedBuscar(): void { $this->resetPage(); }
    public function updatedFiltroTipo(): void { $this->resetPage(); }

    public function render()
    {
        $query = Persona::query();

        if ($this->buscar) {
            $term = '%' . $this->buscar . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre_completo', 'ilike', $term)
                  ->orWhere('ci_nit', 'ilike', $term)
                  ->orWhere('correo', 'ilike', $term);
            });
        }

        if ($this->filtroTipo) {
            $query->where('tipo_persona', $this->filtroTipo);
        }

        return view('livewire.personas.index', [
            'personas' => $query->orderBy('nombre_completo')->paginate(20),
        ]);
    }
}
