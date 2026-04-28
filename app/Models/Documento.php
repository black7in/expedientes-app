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
    ];

    protected $casts = [
        'texto_extraido' => 'array',
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
}
