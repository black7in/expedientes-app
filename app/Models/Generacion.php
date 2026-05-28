<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Generacion extends Model
{
    use HasUuids;

    protected $table = 'generaciones';

    protected $fillable = [
        'usuario_id',
        'expediente_id',
        'narracion',
        'incluir_jurisprudencia',
        'estado',
        'documento_html',
        'molde_usado_id',
        'advertencias',
        'validaciones',
        'fuentes',
        'error_msg',
        'calificacion',
        'llm_model',
        'tokens_input',
        'tokens_output',
    ];

    protected $casts = [
        'incluir_jurisprudencia' => 'boolean',
        'advertencias'           => 'array',
        'validaciones'           => 'array',
        'fuentes'                => 'array',
        'tokens_input'           => 'integer',
        'tokens_output'          => 'integer',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }
}
