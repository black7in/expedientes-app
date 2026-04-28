<?php

namespace App\Livewire\Documentos;

use App\Jobs\ProcesarDocumento;
use App\Models\Documento;
use App\Models\Expediente;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Subir documento')]
class Upload extends Component
{
    use WithFileUploads;

    #[Validate(['archivo' => 'required|file|mimes:pdf,docx|max:51200'])]
    public $archivo = null;

    public string $tipo_documento  = 'otro';
    public string $expediente_id   = '';
    public string $buscarExpediente = '';
    public array  $resultadosExpediente = [];
    public ?string $expedienteSeleccionado = null;
    public ?string $expedienteLabel = null;

    public function updatedBuscarExpediente(): void
    {
        if (strlen($this->buscarExpediente) < 2) {
            $this->resultadosExpediente = [];
            return;
        }

        $user  = auth()->user();
        $query = Expediente::with(['partes.persona'])
            ->when(!$user->isAdmin(), fn($q) => $q->where('abogado_id', $user->id));

        $term = '%' . $this->buscarExpediente . '%';
        $query->where(function ($q) use ($term) {
            $q->where('numero_expediente', 'ilike', $term)
              ->orWhereHas('partes.persona', fn($p) => $p->where('nombre_completo', 'ilike', $term));
        });

        $this->resultadosExpediente = $query->limit(6)->get()->map(fn($e) => [
            'id'     => $e->id,
            'label'  => $e->numero_expediente ?? 'Sin número',
            'partes' => $e->partes->take(2)->map(fn($p) => $p->persona->nombre_completo)->join(', '),
        ])->toArray();
    }

    public function seleccionarExpediente(string $id, string $label): void
    {
        $this->expediente_id         = $id;
        $this->expedienteSeleccionado = $id;
        $this->expedienteLabel       = $label;
        $this->buscarExpediente      = '';
        $this->resultadosExpediente  = [];
    }

    public function limpiarExpediente(): void
    {
        $this->expediente_id         = '';
        $this->expedienteSeleccionado = null;
        $this->expedienteLabel       = null;
    }

    public function subir(): void
    {
        $this->validate([
            'archivo'       => 'required|file|mimes:pdf,docx|max:51200',
            'tipo_documento' => 'required|in:demanda,sentencia,memorial,contrato,notificacion,otro',
        ]);

        $extension    = $this->archivo->getClientOriginalExtension();
        $nombreOriginal = pathinfo($this->archivo->getClientOriginalName(), PATHINFO_FILENAME);
        $nombreArchivo = $nombreOriginal . '_' . now()->format('Ymd_His') . '.' . $extension;

        $this->archivo->storeAs('documentos', $nombreArchivo, 'local');

        $documento = Documento::create([
            'expediente_id'    => $this->expediente_id ?: null,
            'usuario_id'       => auth()->id(),
            'nombre_archivo'   => $nombreArchivo,
            'formato'          => strtolower($extension) === 'pdf' ? 'pdf' : 'docx',
            'tipo_documento'   => $this->tipo_documento,
            'estado_extraccion' => 'pendiente',
        ]);

        ProcesarDocumento::dispatch($documento->id);

        $this->redirect(route('documentos.show', $documento->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.documentos.upload');
    }
}
