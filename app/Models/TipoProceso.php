<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoProceso extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tipos_proceso';

    protected $fillable = [
        'nombre',
        'area_derecho',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function etapas(): HasMany
    {
        return $this->hasMany(EtapaTipoProceso::class)->orderBy('orden');
    }

    public function expedientes(): HasMany
    {
        return $this->hasMany(Expediente::class);
    }
}
