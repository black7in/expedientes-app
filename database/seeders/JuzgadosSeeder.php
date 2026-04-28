<?php

namespace Database\Seeders;

use App\Models\Juzgado;
use Illuminate\Database\Seeder;

class JuzgadosSeeder extends Seeder
{
    public function run(): void
    {
        $juzgados = [
            // Cochabamba — Civil y Comercial
            ['nombre' => 'Juzgado Público Civil y Comercial Nº 1',  'ciudad' => 'Cochabamba'],
            ['nombre' => 'Juzgado Público Civil y Comercial Nº 2',  'ciudad' => 'Cochabamba'],
            ['nombre' => 'Juzgado Público Civil y Comercial Nº 3',  'ciudad' => 'Cochabamba'],
            ['nombre' => 'Juzgado Público Civil y Comercial Nº 4',  'ciudad' => 'Cochabamba'],
            ['nombre' => 'Juzgado Público Civil y Comercial Nº 5',  'ciudad' => 'Cochabamba'],
            // Cochabamba — Familia
            ['nombre' => 'Juzgado Público de Familia Nº 1',         'ciudad' => 'Cochabamba'],
            ['nombre' => 'Juzgado Público de Familia Nº 2',         'ciudad' => 'Cochabamba'],
            // Cochabamba — Trabajo
            ['nombre' => 'Juzgado Laboral y Social Nº 1',           'ciudad' => 'Cochabamba'],
            ['nombre' => 'Juzgado Laboral y Social Nº 2',           'ciudad' => 'Cochabamba'],
            // Tribunal
            ['nombre' => 'Tribunal Departamental de Justicia de Cochabamba', 'ciudad' => 'Cochabamba'],
            ['nombre' => 'Sala Civil Primera del Tribunal Departamental',    'ciudad' => 'Cochabamba'],
            ['nombre' => 'Sala Civil Segunda del Tribunal Departamental',    'ciudad' => 'Cochabamba'],
        ];

        foreach ($juzgados as $data) {
            Juzgado::firstOrCreate(
                ['nombre' => $data['nombre']],
                ['ciudad' => $data['ciudad'], 'activo' => true]
            );
        }
    }
}
