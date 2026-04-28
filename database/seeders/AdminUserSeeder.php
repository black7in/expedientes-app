<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['correo' => 'admin@gmail.com'],
            [
                'nombre'   => 'Administrador',
                'password' => Hash::make('password'),
                'rol'      => 'administrador',
                'activo'   => true,
            ]
        );

        User::firstOrCreate(
            ['correo' => 'abogado@gmail.com'],
            [
                'nombre'   => 'Abogado Pruebas',
                'password' => Hash::make('password'),
                'rol'      => 'abogado',
                'activo'   => true,
            ]
        );
    }
}
