<?php

namespace App\Livewire\Expedientes;

use App\Models\Expediente;
use App\Models\Juzgado;
use App\Models\TipoProceso;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Expedientes')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $busqueda = '';

    #[Url(history: true)]
    public string $estado = '';

    #[Url(history: true)]
    public string $tipoProceso = '';

    #[Url(history: true)]
    public string $juzgado = '';

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatedEstado(): void
    {
        $this->resetPage();
    }

    public function updatedTipoProceso(): void
    {
        $this->resetPage();
    }

    public function updatedJuzgado(): void
    {
        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->busqueda   = '';
        $this->estado     = '';
        $this->tipoProceso = '';
        $this->juzgado    = '';
        $this->resetPage();
    }

    public function render()
    {
        $user  = auth()->user();
        $query = Expediente::with(['juzgado', 'tipoProceso', 'partes.persona'])
            ->when(! $user->isAdmin(), fn ($q) => $q->where('abogado_id', $user->id));

        if ($this->busqueda) {
            $term = '%' . $this->busqueda . '%';
            $query->where(function ($q) use ($term) {
                $q->where('numero_expediente', 'ilike', $term)
                  ->orWhereHas('partes.persona', fn ($p) => $p->where('nombre_completo', 'ilike', $term))
                  ->orWhereHas('juzgado', fn ($j) => $j->where('nombre', 'ilike', $term));
            });
        }

        if ($this->estado) {
            $query->where('estado', $this->estado);
        }

        if ($this->tipoProceso) {
            $query->where('tipo_proceso_id', $this->tipoProceso);
        }

        if ($this->juzgado) {
            $query->where('juzgado_id', $this->juzgado);
        }

        return view('livewire.expedientes.index', [
            'expedientes'   => $query->latest()->paginate(15),
            'tiposProceso'  => TipoProceso::where('activo', true)->orderBy('nombre')->get(),
            'juzgados'      => Juzgado::where('activo', true)->orderBy('nombre')->get(),
            'hayFiltros'    => $this->busqueda || $this->estado || $this->tipoProceso || $this->juzgado,
        ]);
    }
}
