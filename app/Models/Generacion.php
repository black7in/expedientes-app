<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Generacion extends Model
{
    use HasUuids;

    protected $table = 'generaciones';

    protected $fillable = [
        'expediente_id',
        'creado_por',
        'tipo_documento',
        'contexto_usado',
        'chunks_usados',
        'prompt_enviado',
        'borrador_generado',
        'modelo_usado',
        'tokens_usados',
        'tiempo_ms',
    ];

    protected $casts = [
        'contexto_usado' => 'array',
        'chunks_usados'  => 'array',
    ];

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function borradores(): HasMany
    {
        return $this->hasMany(Borrador::class, 'generacion_id');
    }
}
