<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantillaDocumento extends Model
{
    use HasUuids;

    protected $table = 'plantillas_documentos';

    protected $fillable = [
        'creado_por',
        'nombre',
        'tipo_documento',
        'materia',
        'contenido',
        'activo',
        'es_default',
    ];

    protected $casts = [
        'activo'     => 'boolean',
        'es_default' => 'boolean',
    ];

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_documento', $tipo);
    }
}
