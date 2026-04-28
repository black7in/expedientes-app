<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Usuarios')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $busqueda = '';

    #[Url(history: true)]
    public string $rol = '';

    #[Url(history: true)]
    public string $activo = '';

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatedRol(): void
    {
        $this->resetPage();
    }

    public function updatedActivo(): void
    {
        $this->resetPage();
    }

    public function toggleActivo(string $id): void
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['activo' => ! $usuario->activo]);
    }

    public function render()
    {
        $query = User::query();

        if ($this->busqueda) {
            $term = '%' . $this->busqueda . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'ilike', $term)
                  ->orWhere('correo', 'ilike', $term);
            });
        }

        if ($this->rol) {
            $query->where('rol', $this->rol);
        }

        if ($this->activo !== '') {
            $query->where('activo', (bool) $this->activo);
        }

        return view('livewire.usuarios.index', [
            'usuarios' => $query->orderBy('nombre')->paginate(15),
        ]);
    }
}
