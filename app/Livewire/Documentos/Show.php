<?php

namespace App\Livewire\Documentos;

use App\Models\Documento;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Attributes\Computed;

#[Layout('layouts.app')]
#[Title('Documento')]
class Show extends Component
{
    public Documento $documento;

    public function mount(Documento $documento): void
    {
        $this->documento = $documento->load(['expediente', 'usuario']);
    }

    public function refrescar(): void
    {
        $this->documento = Documento::with(['expediente', 'usuario'])->find($this->documento->id);
    }

    public function render()
    {
        return view('livewire.documentos.show');
    }
}
