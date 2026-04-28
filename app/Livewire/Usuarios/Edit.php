<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Editar usuario')]
class Edit extends Component
{
    public User $usuario;

    public string $nombre = '';
    public string $correo = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $rol = '';
    public bool $activo = true;

    public function mount(User $usuario): void
    {
        $this->usuario = $usuario;
        $this->nombre  = $usuario->nombre;
        $this->correo  = $usuario->correo;
        $this->rol     = $usuario->rol;
        $this->activo  = $usuario->activo;
    }

    protected function rules(): array
    {
        return [
            'nombre'   => 'required|string|max:255',
            'correo'   => 'required|email|unique:users,correo,' . $this->usuario->id,
            'password' => 'nullable|string|min:8|confirmed',
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
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'rol.required'       => 'El rol es obligatorio.',
            'rol.in'             => 'Seleccioná un rol válido.',
        ];
    }

    public function guardar(): void
    {
        $this->validate();

        $datos = [
            'nombre' => $this->nombre,
            'correo' => $this->correo,
            'rol'    => $this->rol,
            'activo' => $this->activo,
        ];

        if ($this->password) {
            $datos['password'] = $this->password;
        }

        $this->usuario->update($datos);

        session()->flash('success', 'Usuario actualizado correctamente.');
        $this->redirect(route('usuarios.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.usuarios.edit');
    }
}
