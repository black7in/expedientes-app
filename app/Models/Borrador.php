<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrador extends Model
{
    use HasUuids;

    protected $table = 'borradores';

    protected $fillable = [
        'generacion_id',
        'expediente_id',
        'editado_por',
        'tipo_documento',
        'contenido_html',
        'contenido_texto',
        'estado',
    ];

    public function generacion(): BelongsTo
    {
        return $this->belongsTo(Generacion::class, 'generacion_id');
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function editadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editado_por');
    }

    public function exportaciones(): HasMany
    {
        return $this->hasMany(DocumentoExportado::class, 'borrador_id');
    }
}
