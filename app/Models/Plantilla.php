<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plantilla extends Model
{
    use HasUuids;

    protected $table = 'plantillas';

    protected $fillable = [
        'tipo_documento',
        'subtipo',
        'nombre',
        'descripcion',
        'secciones',
        'version',
        'activa',
    ];

    protected $casts = [
        'secciones' => 'array',
        'activa'    => 'boolean',
    ];

    public function generaciones(): HasMany
    {
        return $this->hasMany(Generacion::class);
    }
}
