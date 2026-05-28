<?php

namespace App\Livewire\Jurisprudencia;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Jurisprudencia TSJ')]
class Consulta extends Component
{
    public string  $pregunta           = '';
    public string  $respuesta          = '';
    public string  $confianza          = '';
    public array   $fuentes            = [];
    public array   $resolucionesCitadas = [];
    public ?string $errorMsg           = null;

    public function recibirResultado(array $data): void
    {
        $this->pregunta            = $data['pregunta']             ?? $this->pregunta;
        $this->respuesta           = $data['respuesta']            ?? '';
        $this->confianza           = $data['confianza']            ?? 'baja';
        $this->fuentes             = $data['fuentes']              ?? [];
        $this->resolucionesCitadas = $data['resoluciones_citadas'] ?? [];
        $this->errorMsg            = null;
    }

    public function recibirError(string $mensaje): void
    {
        $this->errorMsg = $mensaje;
    }

    public function nuevaConsulta(): void
    {
        $this->reset(['pregunta', 'respuesta', 'confianza', 'fuentes', 'resolucionesCitadas', 'errorMsg']);
    }

    public function render()
    {
        return view('livewire.jurisprudencia.consulta');
    }
}
