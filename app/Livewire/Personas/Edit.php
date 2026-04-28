<?php

namespace App\Livewire\Personas;

use App\Models\Persona;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Editar persona')]
class Edit extends Component
{
    public Persona $persona;

    public string $nombre_completo = '';
    public string $ci_nit          = '';
    public string $tipo_persona    = 'natural';
    public string $telefono        = '';
    public string $correo          = '';
    public string $direccion       = '';

    public function mount(Persona $persona): void
    {
        $this->persona        = $persona;
        $this->nombre_completo = $persona->nombre_completo;
        $this->ci_nit          = $persona->ci_nit;
        $this->tipo_persona    = $persona->tipo_persona;
        $this->telefono        = $persona->telefono ?? '';
        $this->correo          = $persona->correo ?? '';
        $this->direccion       = $persona->direccion ?? '';
    }

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

        $this->persona->update([
            'nombre_completo' => $this->nombre_completo,
            'ci_nit'          => $this->ci_nit,
            'tipo_persona'    => $this->tipo_persona,
            'telefono'        => $this->telefono ?: null,
            'correo'          => $this->correo ?: null,
            'direccion'       => $this->direccion ?: null,
        ]);

        session()->flash('success', 'Datos actualizados correctamente.');
        $this->redirect(route('personas.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.personas.edit');
    }
}
