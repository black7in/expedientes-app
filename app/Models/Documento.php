<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'expediente_id',
        'usuario_id',
        'nombre_archivo',
        'formato',
        'tipo_documento',
        'estado_extraccion',
        'texto_extraido',
        'entidades',
        'texto_anonimizado',
    ];

    protected $casts = [
        'texto_extraido' => 'array',
        'entidades'      => 'array',
    ];

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function isPendiente(): bool
    {
        return $this->estado_extraccion === 'pendiente';
    }

    public function isProcesado(): bool
    {
        return $this->estado_extraccion === 'procesado';
    }

    public function isPendienteRevision(): bool
    {
        return $this->estado_extraccion === 'pendiente_revision';
    }

    public function isConfirmado(): bool
    {
        return $this->estado_extraccion === 'confirmado';
    }

    /** Texto extraído y NER listo — pendiente revisión o ya confirmado */
    public function isRevisable(): bool
    {
        return in_array($this->estado_extraccion, ['pendiente_revision', 'confirmado'], true);
    }

    /** @deprecated usar isRevisable() o isConfirmado() */
    public function isAnonimizado(): bool
    {
        return $this->estado_extraccion === 'anonimizado';
    }

    public function getEntidadesConfirmadas(): array
    {
        return array_values(array_filter(
            $this->entidades ?? [],
            fn($e) => $e['confirmado'] ?? false
        ));
    }
}
