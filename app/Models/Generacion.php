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
        'usuario_id',
        'expediente_id',
        'tipo_documento',
        'subtipo',
        'plantilla_id',
        'formato_salida',
        'input_formulario',
        'estado',
        'cancelada',
        'contenido_actual',
        'llm_provider',
        'llm_model',
        'tokens_input',
        'tokens_output',
    ];

    protected $casts = [
        'input_formulario' => 'array',
        'cancelada'        => 'boolean',
        'tokens_input'     => 'integer',
        'tokens_output'    => 'integer',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function plantilla(): BelongsTo
    {
        return $this->belongsTo(Plantilla::class);
    }

    public function secciones(): HasMany
    {
        return $this->hasMany(GeneracionSeccion::class)->orderBy('orden');
    }
}
