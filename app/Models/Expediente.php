<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expediente extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'numero_expediente',
        'juzgado_id',
        'tipo_proceso_id',
        'abogado_id',
        'estado',
        'fecha_inicio',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
    ];

    public function juzgado(): BelongsTo
    {
        return $this->belongsTo(Juzgado::class);
    }

    public function tipoProceso(): BelongsTo
    {
        return $this->belongsTo(TipoProceso::class);
    }

    public function abogado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }

    public function partes(): HasMany
    {
        return $this->hasMany(Parte::class);
    }

    public function actuaciones(): HasMany
    {
        return $this->hasMany(Actuacion::class)->orderBy('fecha');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    public function cliente(): ?Parte
    {
        return $this->partes()->where('es_cliente', true)->with('persona')->first();
    }
}
