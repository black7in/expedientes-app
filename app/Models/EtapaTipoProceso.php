<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtapaTipoProceso extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'etapas_tipo_proceso';

    protected $fillable = [
        'tipo_proceso_id',
        'orden',
        'nombre',
        'tipo',
        'plazo_dias',
        'tipo_computo',
        'es_critica',
        'descripcion',
    ];

    protected $casts = [
        'es_critica' => 'boolean',
        'orden'      => 'integer',
        'plazo_dias' => 'integer',
    ];

    public function tipoProceso(): BelongsTo
    {
        return $this->belongsTo(TipoProceso::class);
    }
}
