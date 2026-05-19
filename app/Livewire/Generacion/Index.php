<?php

namespace App\Livewire\Generacion;

use App\Models\Generacion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Historial de generaciones')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $generaciones = Generacion::with(['creadoPor', 'expediente'])
            ->where('creado_por', auth()->id())
            ->latest()
            ->paginate(15);

        return view('livewire.generacion.index', compact('generaciones'));
    }
}
