<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoExportado extends Model
{
    use HasUuids;

    protected $table = 'documentos_exportados';

    public $timestamps = false;

    protected $fillable = [
        'borrador_id',
        'expediente_id',
        'exportado_por',
        'formato',
        'ruta_archivo',
        'nombre_archivo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function borrador(): BelongsTo
    {
        return $this->belongsTo(Borrador::class, 'borrador_id');
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function exportadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'exportado_por');
    }
}
