<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Nuevo usuario')]
class Create extends Component
{
    public string $nombre = '';
    public string $correo = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $rol = 'abogado';
    public bool $activo = true;

    protected function rules(): array
    {
        return [
            'nombre'   => 'required|string|max:255',
            'correo'   => 'required|email|unique:users,correo',
            'password' => 'required|string|min:8|confirmed',
            'rol'      => 'required|in:administrador,abogado,pasante',
            'activo'   => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required'    => 'El nombre es obligatorio.',
            'correo.required'    => 'El correo es obligatorio.',
            'correo.email'       => 'Ingresá un correo válido.',
            'correo.unique'      => 'Ya existe un usuario con ese correo.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'rol.required'       => 'El rol es obligatorio.',
            'rol.in'             => 'Seleccioná un rol válido.',
        ];
    }

    public function guardar(): void
    {
        $this->validate();

        User::create([
            'nombre'   => $this->nombre,
            'correo'   => $this->correo,
            'password' => $this->password,
            'rol'      => $this->rol,
            'activo'   => $this->activo,
        ]);

        session()->flash('success', 'Usuario creado correctamente.');
        $this->redirect(route('usuarios.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.usuarios.create');
    }
}
