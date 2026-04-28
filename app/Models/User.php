<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'nombre',
        'correo',
        'password',
        'rol',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'activo'            => 'boolean',
        ];
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->correo;
    }

    public function routeNotificationForMail(): string
    {
        return $this->correo;
    }

    public function isAdmin(): bool
    {
        return $this->rol === 'administrador';
    }

    public function isAbogado(): bool
    {
        return $this->rol === 'abogado';
    }

    public function isPasante(): bool
    {
        return $this->rol === 'pasante';
    }

    public function expedientes(): HasMany
    {
        return $this->hasMany(Expediente::class, 'abogado_id');
    }

    public function actuaciones(): HasMany
    {
        return $this->hasMany(Actuacion::class, 'usuario_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'usuario_id');
    }
}
