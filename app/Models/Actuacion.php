<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Actuacion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'expediente_id',
        'usuario_id',
        'descripcion',
        'fecha',
        'tipo_actuacion',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::updating(function () {
            throw new \LogicException('Las actuaciones son inmutables y no pueden modificarse.');
        });

        static::deleting(function () {
            throw new \LogicException('Las actuaciones son inmutables y no pueden eliminarse.');
        });
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
