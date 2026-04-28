<?php

namespace App\Livewire\Personas;

use App\Models\Persona;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Nueva persona')]
class Create extends Component
{
    public string $nombre_completo = '';
    public string $ci_nit          = '';
    public string $tipo_persona    = 'natural';
    public string $telefono        = '';
    public string $correo          = '';
    public string $direccion       = '';

    public function guardar(): void
    {
        $this->validate([
            'nombre_completo' => 'required|min:3|max:255',
            'ci_nit'          => 'required|max:50',
            'tipo_persona'    => 'required|in:natural,juridica',
            'telefono'        => 'nullable|max:20',
            'correo'          => 'nullable|email|max:255',
            'direccion'       => 'nullable|max:500',
        ]);

        Persona::create([
            'nombre_completo' => $this->nombre_completo,
            'ci_nit'          => $this->ci_nit,
            'tipo_persona'    => $this->tipo_persona,
            'telefono'        => $this->telefono ?: null,
            'correo'          => $this->correo ?: null,
            'direccion'       => $this->direccion ?: null,
        ]);

        session()->flash('success', 'Persona registrada correctamente.');
        $this->redirect(route('personas.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.personas.create');
    }
}
