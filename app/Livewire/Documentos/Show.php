<?php

namespace App\Livewire\Documentos;

use App\Models\Documento;
use App\Services\GeneracionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Documento')]
class Show extends Component
{
    public Documento $documento;
    public bool      $indexando     = false;
    public ?string   $mensajeIndex  = null;
    public bool      $indexExito    = false;

    public function mount(Documento $documento): void
    {
        $this->documento = $documento->load(['expediente', 'usuario']);
    }

    public function refrescar(): void
    {
        $this->documento = Documento::with(['expediente', 'usuario'])->find($this->documento->id);
    }

    public function indexar(): void
    {
        if (!$this->documento->isProcesado()) return;

        $this->indexando    = true;
        $this->mensajeIndex = null;

        try {
            $resultado = (new GeneracionService())->indexarDocumento(
                (string) $this->documento->id,
                $this->documento->tipo_documento,
            );

            $chunks = $resultado['chunks'] ?? 0;
            $this->mensajeIndex = "Indexado correctamente — {$chunks} fragmentos generados.";
            $this->indexExito   = true;
            $this->documento    = Documento::with(['expediente', 'usuario'])->find($this->documento->id);
        } catch (\Throwable $e) {
            $this->mensajeIndex = 'Error al indexar: ' . $e->getMessage();
            $this->indexExito   = false;
        } finally {
            $this->indexando = false;
        }
    }

    public function render()
    {
        return view('livewire.documentos.show');
    }
}
