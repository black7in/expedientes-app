<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $correo = '';

    #[Validate('required|min:8')]
    public string $password = '';

    public bool $recordar = false;

    public function login(): void
    {
        $this->validate();

        $throttleKey = Str::lower($this->correo) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('correo', "Demasiados intentos. Intenta en {$seconds} segundos.");
            return;
        }

        if (! Auth::attempt(['correo' => $this->correo, 'password' => $this->password], $this->recordar)) {
            RateLimiter::hit($throttleKey, 300);
            $this->addError('correo', 'Las credenciales no son correctas.');
            return;
        }

        if (! auth()->user()->activo) {
            Auth::logout();
            $this->addError('correo', 'Tu cuenta está desactivada. Contacta al administrador.');
            return;
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
