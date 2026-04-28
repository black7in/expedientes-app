<?php

namespace App\Livewire;

use App\Models\Documento;
use App\Models\Expediente;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public int $totalExpedientes = 0;
    public int $expedientesActivos = 0;
    public int $documentosPendientes = 0;
    public int $expedientesMes = 0;

    public function mount(): void
    {
        $user = auth()->user();

        $query = $user->isAdmin()
            ? Expediente::query()
            : Expediente::where('abogado_id', $user->id);

        $this->totalExpedientes     = $query->count();
        $this->expedientesActivos   = (clone $query)->where('estado', 'activo')->count();
        $this->expedientesMes       = (clone $query)->whereMonth('created_at', now()->month)->count();
        $this->documentosPendientes = Documento::where('estado_extraccion', 'pendiente')->count();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
