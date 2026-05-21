<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneracionSeccion extends Model
{
    use HasUuids;

    protected $table = 'generacion_secciones';

    protected $fillable = [
        'generacion_id',
        'seccion_id',
        'orden',
        'contenido_generado',
        'contenido_editado',
        'chunks_usados',
        'prompt_usado',
        'tokens_input',
        'tokens_output',
        'regenerada_count',
    ];

    protected $casts = [
        'chunks_usados'    => 'array',
        'tokens_input'     => 'integer',
        'tokens_output'    => 'integer',
        'regenerada_count' => 'integer',
    ];

    public function generacion(): BelongsTo
    {
        return $this->belongsTo(Generacion::class);
    }
}
