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
#[Title('Editar expediente')]
class Edit extends Component
{
    public Expediente $expediente;

    public string $numero_expediente = '';
    public string $juzgado_id        = '';
    public string $tipo_proceso_id   = '';
    public string $fecha_inicio      = '';
    public string $estado            = 'activo';

    public array  $partes             = [];
    public string $buscarPersona      = '';
    public array  $resultadosPersona  = [];
    public bool   $mostrarResultados  = false;

    public function mount(Expediente $expediente): void
    {
        $this->expediente = $expediente->load('partes.persona');

        $this->numero_expediente = $expediente->numero_expediente ?? '';
        $this->juzgado_id        = $expediente->juzgado_id;
        $this->tipo_proceso_id   = $expediente->tipo_proceso_id;
        $this->fecha_inicio      = $expediente->fecha_inicio->format('Y-m-d');
        $this->estado            = $expediente->estado;

        $this->partes = $expediente->partes->map(fn ($p) => [
            'persona_id'      => $p->persona_id,
            'nombre_completo' => $p->persona->nombre_completo,
            'ci_nit'          => $p->persona->ci_nit,
            'rol_procesal'    => $p->rol_procesal,
            'es_cliente'      => $p->es_cliente,
        ])->values()->toArray();
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

        foreach ($this->partes as $parte) {
            if ($parte['persona_id'] === $personaId) {
                $this->mostrarResultados = false;
                $this->buscarPersona = '';
                return;
            }
        }

        $this->partes[] = [
            'persona_id'      => $persona->id,
            'nombre_completo' => $persona->nombre_completo,
            'ci_nit'          => $persona->ci_nit,
            'rol_procesal'    => 'demandante',
            'es_cliente'      => false,
        ];

        $this->buscarPersona     = '';
        $this->resultadosPersona = [];
        $this->mostrarResultados = false;
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

        $this->expediente->update([
            'numero_expediente' => $this->numero_expediente ?: null,
            'juzgado_id'        => $this->juzgado_id,
            'tipo_proceso_id'   => $this->tipo_proceso_id,
            'estado'            => $this->estado,
            'fecha_inicio'      => $this->fecha_inicio,
        ]);

        // Replace partes
        $this->expediente->partes()->delete();
        foreach ($this->partes as $parte) {
            $this->expediente->partes()->create([
                'persona_id'   => $parte['persona_id'],
                'rol_procesal' => $parte['rol_procesal'],
                'es_cliente'   => $parte['es_cliente'],
            ]);
        }

        session()->flash('success', 'Expediente actualizado correctamente.');
        $this->redirect(route('expedientes.show', $this->expediente), navigate: true);
    }

    public function render()
    {
        return view('livewire.expedientes.edit', [
            'juzgados'     => Juzgado::where('activo', true)->orderBy('nombre')->get(),
            'tiposProceso' => TipoProceso::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }
}
