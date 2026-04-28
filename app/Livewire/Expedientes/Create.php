<?php

namespace App\Livewire\Expedientes;

use App\Models\Expediente;
use App\Models\Juzgado;
use App\Models\Persona;
use App\Models\TipoProceso;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Nuevo expediente')]
class Create extends Component
{
    // Datos del expediente
    public string $numero_expediente = '';
    public string $juzgado_id        = '';
    public string $tipo_proceso_id   = '';
    public string $fecha_inicio      = '';
    public string $estado            = 'activo';

    // Partes procesales (PB-14)
    public array $partes = [];

    // Búsqueda de persona para agregar parte
    public string $buscarPersona    = '';
    public array  $resultadosPersona = [];
    public bool   $mostrarResultados = false;

    // Modal nueva persona
    public bool   $showModalPersona  = false;
    public string $mpNombre          = '';
    public string $mpCiNit           = '';
    public string $mpTipo            = 'natural';
    public string $mpTelefono        = '';
    public string $mpCorreo          = '';

    public function mount(): void
    {
        $this->fecha_inicio = now()->format('Y-m-d');
        $this->partes = [];
    }

    public function updatedBuscarPersona(): void
    {
        if (strlen($this->buscarPersona) < 2) {
            $this->resultadosPersona = [];
            $this->mostrarResultados = false;
            return;
        }

        $this->resultadosPersona = Persona::where('nombre_completo', 'ilike', '%' . $this->buscarPersona . '%')
            ->orWhere('ci_nit', 'ilike', '%' . $this->buscarPersona . '%')
            ->limit(6)
            ->get(['id', 'nombre_completo', 'ci_nit', 'tipo_persona'])
            ->toArray();

        $this->mostrarResultados = true;
    }

    public function seleccionarPersona(string $personaId): void
    {
        $persona = Persona::find($personaId);
        if (! $persona) return;

        // Evitar duplicados
        foreach ($this->partes as $parte) {
            if ($parte['persona_id'] === $personaId) {
                $this->mostrarResultados = false;
                $this->buscarPersona = '';
                return;
            }
        }

        $this->partes[] = [
            'persona_id'     => $persona->id,
            'nombre_completo' => $persona->nombre_completo,
            'ci_nit'         => $persona->ci_nit,
            'rol_procesal'   => 'demandante',
            'es_cliente'     => false,
        ];

        $this->buscarPersona    = '';
        $this->resultadosPersona = [];
        $this->mostrarResultados = false;
    }

    public function abrirModalPersona(): void
    {
        $this->showModalPersona = true;
        $this->mpNombre   = $this->buscarPersona; // pre-rellena con lo que buscó
        $this->mpCiNit    = '';
        $this->mpTipo     = 'natural';
        $this->mpTelefono = '';
        $this->mpCorreo   = '';
        $this->mostrarResultados = false;
        $this->resetErrorBag();
    }

    public function cerrarModalPersona(): void
    {
        $this->showModalPersona = false;
        $this->resetErrorBag();
    }

    public function crearYAgregarPersona(): void
    {
        $this->validate([
            'mpNombre' => 'required|min:3|max:255',
            'mpCiNit'  => 'required|max:50',
            'mpTipo'   => 'required|in:natural,juridica',
            'mpCorreo' => 'nullable|email|max:255',
        ], [], [
            'mpNombre' => 'nombre completo',
            'mpCiNit'  => 'CI / NIT',
            'mpTipo'   => 'tipo de persona',
            'mpCorreo' => 'correo',
        ]);

        $persona = Persona::create([
            'nombre_completo' => $this->mpNombre,
            'ci_nit'          => $this->mpCiNit,
            'tipo_persona'    => $this->mpTipo,
            'telefono'        => $this->mpTelefono ?: null,
            'correo'          => $this->mpCorreo ?: null,
        ]);

        $this->partes[] = [
            'persona_id'      => $persona->id,
            'nombre_completo' => $persona->nombre_completo,
            'ci_nit'          => $persona->ci_nit,
            'rol_procesal'    => 'demandante',
            'es_cliente'      => false,
        ];

        $this->showModalPersona = false;
        $this->buscarPersona    = '';
    }

    public function removerParte(int $index): void
    {
        array_splice($this->partes, $index, 1);
    }

    public function marcarCliente(int $index): void
    {
        foreach ($this->partes as $i => $parte) {
            $this->partes[$i]['es_cliente'] = ($i === $index);
        }
    }

    public function guardar(): void
    {
        $this->validate([
            'juzgado_id'      => 'required|exists:juzgados,id',
            'tipo_proceso_id' => 'required|exists:tipos_proceso,id',
            'fecha_inicio'    => 'required|date',
            'estado'          => 'required|in:activo,suspendido,en_apelacion,archivado,concluido',
            'partes'          => 'array|min:1',
            'partes.*.persona_id'   => 'required|exists:personas,id',
            'partes.*.rol_procesal' => 'required|in:demandante,demandado',
        ], [
            'partes.min'            => 'Debe agregar al menos una parte procesal.',
            'partes.*.rol_procesal' => 'Seleccioná el rol de cada parte.',
        ]);

        $expediente = Expediente::create([
            'numero_expediente' => $this->numero_expediente ?: null,
            'juzgado_id'        => $this->juzgado_id,
            'tipo_proceso_id'   => $this->tipo_proceso_id,
            'abogado_id'        => auth()->id(),
            'estado'            => $this->estado,
            'fecha_inicio'      => $this->fecha_inicio,
        ]);

        foreach ($this->partes as $parte) {
            $expediente->partes()->create([
                'persona_id'   => $parte['persona_id'],
                'rol_procesal' => $parte['rol_procesal'],
                'es_cliente'   => $parte['es_cliente'],
            ]);
        }

        session()->flash('success', 'Expediente creado correctamente.');
        $this->redirect(route('expedientes.show', $expediente), navigate: true);
    }

    public function render()
    {
        return view('livewire.expedientes.create', [
            'juzgados'     => Juzgado::where('activo', true)->orderBy('nombre')->get(),
            'tiposProceso' => TipoProceso::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }
}
