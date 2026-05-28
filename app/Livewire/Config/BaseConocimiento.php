<?php

namespace App\Livewire\Config;

use App\Models\Plantilla;
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
    public $archivoPdf    = null;
    public string $nombreLey  = '';
    public string $materiaLey = 'civil';
    public bool   $indexandoLey = false;
    public ?string $mensajeLey  = null;
    public bool   $exitoLey    = false;

    // ── Jurisprudencia ────────────────────────────────────────────────────────
    public string  $numeroAuto    = '';
    public string  $textoAuto     = '';
    public string  $materiaAuto   = 'civil';
    public ?string $fechaAuto     = null;
    public ?string $salaAuto      = null;
    public bool    $indexandoAuto = false;
    public ?string $mensajeAuto   = null;
    public bool    $exitoAuto     = false;

    // ── Stats ─────────────────────────────────────────────────────────────────
    public array $stats  = [];
    public array $leyes  = [];
    public array $autos  = [];
    public array $moldes = [];

    // ── Plantillas ────────────────────────────────────────────────────────────
    public string  $plantillaVista            = 'lista';
    public ?string $editandoPlantillaId       = null;
    public string  $plantillaBusqueda         = '';
    public string  $plantillaTipoFiltro       = '';
    public ?string $confirmarEliminarId       = null;
    public ?string $mensajePlantilla          = null;
    public bool    $exitoPlantilla            = false;

    public string $plantillaNombre        = '';
    public string $plantillaTipoDocumento = 'demanda';
    public string $plantillaSubtipo       = '';
    public string $plantillaDescripcion   = '';
    public string $plantillaVersion       = 'v1';
    public bool   $plantillaActiva        = true;
    public string $plantillaSecciones     = '[]';

    public function mount(): void
    {
        $this->cargarStats();
    }

    public function cargarStats(): void
    {
        try {
            $data          = (new GeneracionService())->getRagStats();
            $this->stats   = $data['stats']  ?? [];
            $this->leyes   = $data['leyes']  ?? [];
            $this->autos   = $data['autos']  ?? [];
            $this->moldes  = $data['moldes'] ?? [];
        } catch (\Throwable) {
            $this->stats = [];
        }
    }

    // ── Leyes ─────────────────────────────────────────────────────────────────

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

    // ── Jurisprudencia ────────────────────────────────────────────────────────

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

    // ── Plantillas ────────────────────────────────────────────────────────────

    public function nuevaPlantilla(): void
    {
        $this->editandoPlantillaId   = null;
        $this->plantillaNombre       = '';
        $this->plantillaTipoDocumento = 'demanda';
        $this->plantillaSubtipo      = '';
        $this->plantillaDescripcion  = '';
        $this->plantillaVersion      = 'v1';
        $this->plantillaActiva       = true;
        $this->plantillaSecciones    = '[]';
        $this->mensajePlantilla      = null;
        $this->resetErrorBag();
        $this->plantillaVista = 'formulario';
    }

    public function editarPlantilla(string $id): void
    {
        $p = Plantilla::findOrFail($id);

        $this->editandoPlantillaId   = $id;
        $this->plantillaNombre       = $p->nombre;
        $this->plantillaTipoDocumento = $p->tipo_documento;
        $this->plantillaSubtipo      = $p->subtipo;
        $this->plantillaDescripcion  = $p->descripcion ?? '';
        $this->plantillaVersion      = $p->version;
        $this->plantillaActiva       = $p->activa;
        $this->plantillaSecciones    = json_encode($p->secciones, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->mensajePlantilla      = null;
        $this->resetErrorBag();
        $this->plantillaVista = 'formulario';
    }

    public function cancelarFormularioPlantilla(): void
    {
        $this->plantillaVista      = 'lista';
        $this->editandoPlantillaId = null;
        $this->mensajePlantilla    = null;
        $this->resetErrorBag();
    }

    public function guardarPlantilla(): void
    {
        $this->validate([
            'plantillaNombre'        => 'required|string|max:100',
            'plantillaTipoDocumento' => 'required|string|max:50',
            'plantillaSubtipo'       => 'required|string|max:50',
            'plantillaDescripcion'   => 'nullable|string',
            'plantillaVersion'       => 'required|string|max:20',
            'plantillaActiva'        => 'boolean',
            'plantillaSecciones'     => 'required|string',
        ], [], [
            'plantillaNombre'        => 'nombre',
            'plantillaTipoDocumento' => 'tipo de documento',
            'plantillaSubtipo'       => 'subtipo',
            'plantillaVersion'       => 'versión',
            'plantillaSecciones'     => 'secciones',
        ]);

        $decoded = json_decode($this->plantillaSecciones, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            $this->addError('plantillaSecciones', 'Las secciones deben ser un JSON válido en formato array.');
            return;
        }

        $existe = Plantilla::where('tipo_documento', $this->plantillaTipoDocumento)
            ->where('subtipo', $this->plantillaSubtipo)
            ->where('version', $this->plantillaVersion)
            ->when($this->editandoPlantillaId, fn ($q) => $q->where('id', '!=', $this->editandoPlantillaId))
            ->exists();

        if ($existe) {
            $this->addError('plantillaSubtipo', 'Ya existe una plantilla con ese tipo, subtipo y versión.');
            return;
        }

        $datos = [
            'nombre'         => $this->plantillaNombre,
            'tipo_documento' => $this->plantillaTipoDocumento,
            'subtipo'        => $this->plantillaSubtipo,
            'descripcion'    => $this->plantillaDescripcion,
            'version'        => $this->plantillaVersion,
            'activa'         => $this->plantillaActiva,
            'secciones'      => $decoded,
        ];

        if ($this->editandoPlantillaId) {
            Plantilla::findOrFail($this->editandoPlantillaId)->update($datos);
            $this->mensajePlantilla = 'Plantilla actualizada correctamente.';
        } else {
            Plantilla::create($datos);
            $this->mensajePlantilla = 'Plantilla creada correctamente.';
        }

        $this->exitoPlantilla      = true;
        $this->editandoPlantillaId = null;
        $this->plantillaVista      = 'lista';
    }

    public function togglePlantillaActiva(string $id): void
    {
        $p = Plantilla::findOrFail($id);
        $p->update(['activa' => ! $p->activa]);
    }

    public function confirmarEliminar(string $id): void
    {
        $this->confirmarEliminarId = $id;
    }

    public function cancelarEliminar(): void
    {
        $this->confirmarEliminarId = null;
    }

    public function eliminarPlantilla(string $id): void
    {
        $p = Plantilla::findOrFail($id);

        if ($p->generaciones()->count() > 0) {
            $this->mensajePlantilla = 'No se puede eliminar: la plantilla tiene generaciones asociadas.';
            $this->exitoPlantilla   = false;
            $this->confirmarEliminarId = null;
            return;
        }

        $p->delete();
        $this->mensajePlantilla    = 'Plantilla eliminada correctamente.';
        $this->exitoPlantilla      = true;
        $this->confirmarEliminarId = null;
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $plantillas = Plantilla::query()
            ->when($this->plantillaBusqueda, fn ($q) => $q->where(fn ($i) =>
                $i->where('nombre', 'ilike', '%' . $this->plantillaBusqueda . '%')
                  ->orWhere('subtipo', 'ilike', '%' . $this->plantillaBusqueda . '%')
            ))
            ->when($this->plantillaTipoFiltro, fn ($q) => $q->where('tipo_documento', $this->plantillaTipoFiltro))
            ->orderBy('tipo_documento')
            ->orderBy('subtipo')
            ->get();

        return view('livewire.config.base-conocimiento', compact('plantillas'));
    }
}
