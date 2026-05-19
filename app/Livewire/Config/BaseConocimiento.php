<?php

namespace App\Livewire\Config;

use App\Models\Documento;
use App\Models\PlantillaDocumento;
use App\Services\GeneracionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Base de Conocimiento')]
class BaseConocimiento extends Component
{
    use WithFileUploads;

    public string $tab = 'leyes';

    // ── Leyes ─────────────────────────────────────────────────────────────────
    public $archivoPdf   = null;
    public string $nombreLey  = '';
    public string $materiaLey = 'civil';
    public bool   $indexandoLey = false;
    public ?string $mensajeLey  = null;
    public bool   $exitoLey    = false;

    // ── Jurisprudencia ────────────────────────────────────────────────────────
    public string  $numeroAuto   = '';
    public string  $textoAuto    = '';
    public string  $materiaAuto  = 'civil';
    public ?string $fechaAuto    = null;
    public ?string $salaAuto     = null;
    public bool    $indexandoAuto = false;
    public ?string $mensajeAuto   = null;
    public bool    $exitoAuto     = false;

    // ── Stats ─────────────────────────────────────────────────────────────────
    public array $stats = [];
    public array $leyes = [];
    public array $autos = [];

    // ── Plantillas ─────────────────────────────────────────────────────────────
    public array   $plantillas         = [];
    public ?string $editandoId         = null;   // null = nueva, uuid = editar
    public string  $plantillaNombre    = '';
    public string  $plantillaTipo      = 'demanda';
    public string  $plantillaMateria   = 'civil';
    public string  $plantillaContenido = '';
    public bool    $plantillaEsDefault = false;
    public bool    $guardandoPlantilla = false;
    public ?string $mensajePlantilla   = null;
    public bool    $exitoPlantilla     = false;

    public function mount(): void
    {
        $this->cargarStats();
        $this->cargarPlantillas();
    }

    public function cargarStats(): void
    {
        try {
            $data        = (new GeneracionService())->getRagStats();
            $this->stats = $data['stats'] ?? [];
            $this->leyes = $data['leyes'] ?? [];
            $this->autos = $data['autos'] ?? [];
        } catch (\Throwable) {
            $this->stats = [];
        }
    }

    public function cargarPlantillas(): void
    {
        $this->plantillas = PlantillaDocumento::where('activo', true)
            ->selectRaw('id, nombre, tipo_documento, materia, activo, es_default, created_at, LENGTH(contenido) AS contenido_len')
            ->orderBy('tipo_documento')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($p) => [
                'id'             => (string) $p->id,
                'nombre'         => $p->nombre,
                'tipo_documento' => $p->tipo_documento,
                'materia'        => $p->materia,
                'activo'         => (bool) $p->activo,
                'es_default'     => (bool) $p->es_default,
                'contenido_len'  => (int) $p->contenido_len,
            ])
            ->toArray();
    }

    public function editarPlantilla(string $id): void
    {
        $p = PlantillaDocumento::find($id);

        if (! $p) {
            $this->mensajePlantilla = 'Plantilla no encontrada.';
            $this->exitoPlantilla   = false;
            return;
        }

        $this->editandoId         = $id;
        $this->plantillaNombre    = $p->nombre;
        $this->plantillaTipo      = $p->tipo_documento;
        $this->plantillaMateria   = $p->materia;
        $this->plantillaContenido = $p->contenido;
        $this->plantillaEsDefault = $p->es_default;
        $this->mensajePlantilla   = null;
    }

    public function nuevaPlantilla(): void
    {
        $this->editandoId         = null;
        $this->plantillaNombre    = '';
        $this->plantillaTipo      = 'demanda';
        $this->plantillaMateria   = 'civil';
        $this->plantillaContenido = '';
        $this->plantillaEsDefault = false;
        $this->mensajePlantilla   = null;
    }

    public function guardarPlantilla(): void
    {
        $this->validate([
            'plantillaNombre'    => 'required|min:3|max:100',
            'plantillaTipo'      => 'required|in:demanda,memorial,contestacion,apelacion,nulidad,contrato',
            'plantillaMateria'   => 'required|in:civil,comercial,familiar,laboral,penal',
            'plantillaContenido' => 'required|min:50',
        ], [], [
            'plantillaNombre'    => 'nombre',
            'plantillaTipo'      => 'tipo de documento',
            'plantillaMateria'   => 'materia',
            'plantillaContenido' => 'contenido',
        ]);

        $this->guardandoPlantilla = true;
        $this->mensajePlantilla   = null;

        try {
            if ($this->editandoId) {
                PlantillaDocumento::where('id', $this->editandoId)->update([
                    'nombre'         => $this->plantillaNombre,
                    'tipo_documento' => $this->plantillaTipo,
                    'materia'        => $this->plantillaMateria,
                    'contenido'      => $this->plantillaContenido,
                    'es_default'     => $this->plantillaEsDefault,
                ]);
                $this->mensajePlantilla = 'Plantilla actualizada correctamente.';
            } else {
                PlantillaDocumento::create([
                    'nombre'         => $this->plantillaNombre,
                    'tipo_documento' => $this->plantillaTipo,
                    'materia'        => $this->plantillaMateria,
                    'contenido'      => $this->plantillaContenido,
                    'es_default'     => $this->plantillaEsDefault,
                    'activo'         => true,
                    'creado_por'     => auth()->id(),
                ]);
                $this->mensajePlantilla = 'Plantilla creada correctamente.';
            }

            $this->exitoPlantilla = true;
            $this->cargarPlantillas();
            $this->nuevaPlantilla();
        } catch (\Throwable $e) {
            $this->mensajePlantilla = $e->getMessage();
            $this->exitoPlantilla   = false;
        } finally {
            $this->guardandoPlantilla = false;
        }
    }

    public function setDefault(string $id): void
    {
        $p = PlantillaDocumento::find($id);
        if (! $p) return;

        PlantillaDocumento::where('materia', $p->materia)
            ->where('tipo_documento', $p->tipo_documento)
            ->update(['es_default' => false]);

        PlantillaDocumento::where('id', $id)->update(['es_default' => true]);

        $this->cargarPlantillas();
    }

    public function eliminarPlantilla(string $id): void
    {
        PlantillaDocumento::where('id', $id)->update(['activo' => false]);
        $this->cargarPlantillas();

        if ($this->editandoId === $id) {
            $this->nuevaPlantilla();
        }
    }

    public function indexarLey(): void
    {
        $this->validate([
            'archivoPdf' => 'required|file|mimes:pdf|max:51200',
            'nombreLey'  => 'required|min:3',
            'materiaLey' => 'required|in:civil,comercial,familiar,laboral,penal',
        ]);

        $this->indexandoLey = true;
        $this->mensajeLey   = null;

        try {
            $resultado = (new GeneracionService())->indexarLey(
                $this->archivoPdf,
                $this->nombreLey,
                $this->materiaLey,
            );

            $chunks = $resultado['chunks'] ?? 0;
            $this->mensajeLey = "Ley indexada — {$chunks} artículos procesados.";
            $this->exitoLey   = true;
            $this->reset('archivoPdf', 'nombreLey');
            $this->cargarStats();
        } catch (\Throwable $e) {
            $this->mensajeLey = $e->getMessage();
            $this->exitoLey   = false;
        } finally {
            $this->indexandoLey = false;
        }
    }

    public function indexarJurisprudencia(): void
    {
        $this->validate([
            'numeroAuto'  => 'required|min:3',
            'textoAuto'   => 'required|min:50',
            'materiaAuto' => 'required|in:civil,comercial,familiar,laboral,penal',
        ]);

        $this->indexandoAuto = true;
        $this->mensajeAuto   = null;

        try {
            $resultado = (new GeneracionService())->indexarJurisprudencia([
                'numero_auto' => $this->numeroAuto,
                'texto'       => $this->textoAuto,
                'materia'     => $this->materiaAuto,
                'fecha'       => $this->fechaAuto ?: null,
                'sala'        => $this->salaAuto ?: null,
            ]);

            $chunks = $resultado['chunks'] ?? 0;
            $this->mensajeAuto = "Auto Supremo indexado — {$chunks} fragmentos generados.";
            $this->exitoAuto   = true;
            $this->reset('numeroAuto', 'textoAuto', 'fechaAuto', 'salaAuto');
            $this->cargarStats();
        } catch (\Throwable $e) {
            $this->mensajeAuto = $e->getMessage();
            $this->exitoAuto   = false;
        } finally {
            $this->indexandoAuto = false;
        }
    }

    public function render()
    {
        $documentosIndexados = Documento::where('indexado', true)
            ->with('expediente')
            ->latest('indexado_at')
            ->get();

        // Agrupar plantillas por tipo_documento para la vista
        $plantillasPorTipo = collect($this->plantillas)->groupBy('tipo_documento')->toArray();

        return view('livewire.config.base-conocimiento', compact('documentosIndexados', 'plantillasPorTipo'));
    }
}
