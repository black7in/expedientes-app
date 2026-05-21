<?php

namespace App\Livewire\Generacion;

use App\Services\GeneracionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Historial de generaciones')]
class Index extends Component
{
    public array   $generaciones = [];
    public ?string $error        = null;

    public function mount(): void
    {
        $this->cargar();
    }

    public function cargar(): void
    {
        try {
            $this->generaciones = (new GeneracionService())->listarGeneraciones((string) auth()->id());
        } catch (\Throwable $e) {
            $this->error        = $e->getMessage();
            $this->generaciones = [];
        }
    }

    public function render()
    {
        return view('livewire.generacion.index');
    }
}
