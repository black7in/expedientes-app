<?php

namespace App\Livewire\Config;

use App\Models\Juzgado;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Juzgados')]
class Juzgados extends Component
{
    // Form nuevo juzgado
    public bool   $showForm = false;
    public string $nombre   = '';
    public string $ciudad   = 'Cochabamba';

    // Edición inline
    public string $editandoId     = '';
    public string $editNombre     = '';
    public string $editCiudad     = '';

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;
        $this->reset('nombre', 'ciudad');
        $this->ciudad = 'Cochabamba';
        $this->resetErrorBag();
    }

    public function guardar(): void
    {
        $this->validate([
            'nombre' => 'required|min:3|max:255',
            'ciudad' => 'required|max:100',
        ]);

        Juzgado::create([
            'nombre' => $this->nombre,
            'ciudad' => $this->ciudad,
            'activo' => true,
        ]);

        $this->showForm = false;
        $this->reset('nombre', 'ciudad');
    }

    public function editarJuzgado(string $id): void
    {
        $juzgado = Juzgado::findOrFail($id);
        $this->editandoId = $id;
        $this->editNombre = $juzgado->nombre;
        $this->editCiudad = $juzgado->ciudad;
        $this->resetErrorBag();
    }

    public function guardarEdicion(): void
    {
        $this->validate([
            'editNombre' => 'required|min:3|max:255',
            'editCiudad' => 'required|max:100',
        ]);

        Juzgado::findOrFail($this->editandoId)->update([
            'nombre' => $this->editNombre,
            'ciudad' => $this->editCiudad,
        ]);

        $this->editandoId = '';
    }

    public function cancelarEdicion(): void
    {
        $this->editandoId = '';
        $this->resetErrorBag();
    }

    public function toggleActivo(string $id): void
    {
        $juzgado = Juzgado::findOrFail($id);
        $juzgado->update(['activo' => ! $juzgado->activo]);
    }

    public function render()
    {
        return view('livewire.config.juzgados', [
            'juzgados' => Juzgado::orderBy('ciudad')->orderBy('nombre')->get(),
        ]);
    }
}
