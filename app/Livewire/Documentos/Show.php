<?php

namespace App\Livewire\Documentos;

use App\Jobs\GenerarResumenExpedienteJob;
use App\Models\Documento;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Documento')]
class Show extends Component
{
    public Documento $documento;

    private const TIPOS = ['PER', 'CI', 'NIT', 'TEL', 'DIR'];

    public const ROLES_PER = [
        'DEMANDANTE', 'DEMANDADO', 'ABOGADO', 'JUEZ', 'TESTIGO', 'NOTARIO',
    ];

    private const COLORES = [
        'PER' => '#dbeafe',
        'CI'  => '#dcfce7',
        'NIT' => '#fef9c3',
        'TEL' => '#fce7f3',
        'DIR' => '#f3e8ff',
    ];

    public function mount(Documento $documento): void
    {
        $this->documento = $documento->load(['expediente', 'usuario']);
    }

    public function refrescar(): void
    {
        $this->documento = Documento::with(['expediente', 'usuario'])->find($this->documento->id);
    }

    // PB-9 — texto original con entidades resaltadas con <mark>
    #[Computed]
    public function textoResaltado(): string
    {
        $texto     = ($this->documento->texto_extraido ?? [])['texto_completo'] ?? '';
        $entidades = $this->documento->entidades ?? [];

        if (empty($entidades) || empty($texto)) {
            return nl2br(e($texto));
        }

        usort($entidades, fn($a, $b) => (int) $a['inicio'] - (int) $b['inicio']);

        $resultado = '';
        $cursor    = 0;
        $longitud  = mb_strlen($texto, 'UTF-8');

        foreach ($entidades as $ent) {
            $inicio = (int) $ent['inicio'];
            $fin    = (int) $ent['fin'];

            if ($inicio < $cursor || $fin > $longitud || $fin <= $inicio) {
                continue;
            }

            $resultado .= nl2br(e(mb_substr($texto, $cursor, $inicio - $cursor, 'UTF-8')));

            $color       = self::COLORES[$ent['tipo']] ?? '#f3f4f6';
            $placeholder = e($ent['placeholder'] ?? $ent['tipo']);
            $textoEnt    = e(mb_substr($texto, $inicio, $fin - $inicio, 'UTF-8'));

            $resultado .= "<mark style=\"background:{$color};border-radius:3px;padding:0 2px\" title=\"{$placeholder}\">{$textoEnt}</mark>";

            $cursor = $fin;
        }

        $resultado .= nl2br(e(mb_substr($texto, $cursor, null, 'UTF-8')));

        return $resultado;
    }

    // PB-12 — confirmar entidad como PII real
    public function confirmarEntidad(string $entidadId): void
    {
        $this->_patchEntidad($entidadId, ['confirmado' => true]);
    }

    // PB-12 — cambiar tipo de entidad
    public function cambiarTipo(string $entidadId, string $tipo): void
    {
        if (!in_array($tipo, self::TIPOS, true)) return;
        $this->_patchEntidad($entidadId, ['tipo' => $tipo, 'confirmado' => true]);
    }

    // Asignar rol a una entidad PER
    public function asignarRol(string $entidadId, string $rol): void
    {
        $rolesValidos = array_merge(self::ROLES_PER, ['OTRO']);
        if (!in_array($rol, $rolesValidos, true)) return;
        $this->_patchEntidad($entidadId, ['rol' => $rol]);
    }

    // PB-12 — eliminar entidad (falso positivo)
    public function eliminarEntidad(string $entidadId): void
    {
        $this->_patchEntidad($entidadId, ['eliminar' => true]);
    }

    // Confirmar anonimización completa del documento
    public function confirmarAnonimizacion(): void
    {
        $url = rtrim(config('services.ai.url'), '/') . "/api/documentos/{$this->documento->id}/confirmar";

        try {
            $response = Http::timeout(10)->post($url);

            if ($response->successful() && $this->documento->expediente_id) {
                GenerarResumenExpedienteJob::dispatch($this->documento->expediente_id);
            }
        } catch (\Exception) {
            // El estado se verá en el próximo refrescar
        }

        $this->refrescar();
    }

    private function _patchEntidad(string $entidadId, array $body): void
    {
        $url = rtrim(config('services.ai.url'), '/') . "/api/documentos/{$this->documento->id}/entidades/{$entidadId}";

        try {
            Http::timeout(10)->patch($url, $body);
        } catch (\Exception) {
            // Fallo silencioso — el estado se verá actualizado en el refrescar
        }

        $this->refrescar();
    }

    public function render()
    {
        return view('livewire.documentos.show', [
            'tiposDisponibles' => self::TIPOS,
            'rolesPer'         => self::ROLES_PER,
            'colores'          => self::COLORES,
        ]);
    }
}
