<?php

namespace App\Livewire\Documentos;

use App\Models\Documento;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Documentos')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $busqueda = '';

    #[Url(history: true)]
    public string $formato = '';

    #[Url(history: true)]
    public string $estado = '';

    public function updatedBusqueda(): void { $this->resetPage(); }
    public function updatedFormato(): void  { $this->resetPage(); }
    public function updatedEstado(): void   { $this->resetPage(); }

    public function render()
    {
        $query = Documento::with(['expediente', 'usuario'])
            ->when($this->busqueda, fn($q) => $q->where('nombre_archivo', 'ilike', '%'.$this->busqueda.'%'))
            ->when($this->formato,  fn($q) => $q->where('formato', $this->formato))
            ->when($this->estado,   fn($q) => $q->where('estado_extraccion', $this->estado));

        return view('livewire.documentos.index', [
            'documentos' => $query->latest()->paginate(15),
            'hayFiltros' => $this->busqueda || $this->formato || $this->estado,
        ]);
    }
}
