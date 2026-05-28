<?php

namespace App\Livewire\Generacion;

use App\Models\Generacion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Historial de generaciones')]
class Index extends Component
{
    public function render()
    {
        $generaciones = Generacion::where('usuario_id', (string) auth()->id())
            ->latest()
            ->get();

        return view('livewire.generacion.index', compact('generaciones'));
    }
}
