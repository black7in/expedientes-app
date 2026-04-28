<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parte extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'expediente_id',
        'persona_id',
        'rol_procesal',
        'es_cliente',
    ];

    protected $casts = [
        'es_cliente' => 'boolean',
    ];

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
