<?php

namespace Database\Seeders;

use App\Models\TipoProceso;
use Illuminate\Database\Seeder;

class TiposProcesoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'nombre'      => 'Proceso Ordinario',
                'area_derecho' => 'civil',
                'activo'      => true,
                'etapas'      => [
                    ['orden' => 1, 'nombre' => 'Demanda',                    'tipo' => 'escrito',    'plazo_dias' => null, 'tipo_computo' => null,    'es_critica' => true,  'descripcion' => 'Presentación de la demanda ante el juzgado'],
                    ['orden' => 2, 'nombre' => 'Admisión y citación',        'tipo' => 'resolucion', 'plazo_dias' => 5,    'tipo_computo' => 'habil', 'es_critica' => true,  'descripcion' => 'El juez admite la demanda y ordena citación al demandado'],
                    ['orden' => 3, 'nombre' => 'Contestación',               'tipo' => 'escrito',    'plazo_dias' => 30,   'tipo_computo' => 'corrido','es_critica' => true,  'descripcion' => 'El demandado contesta la demanda (Art. 125 Ley 439)'],
                    ['orden' => 4, 'nombre' => 'Reconvención',               'tipo' => 'escrito',    'plazo_dias' => 30,   'tipo_computo' => 'corrido','es_critica' => false, 'descripcion' => 'Demanda reconvencional del demandado (opcional)'],
                    ['orden' => 5, 'nombre' => 'Audiencia preliminar',       'tipo' => 'audiencia',  'plazo_dias' => 20,   'tipo_computo' => 'corrido','es_critica' => true,  'descripcion' => 'Conciliación, excepciones, fijación de hechos a probar (Art. 366)'],
                    ['orden' => 6, 'nombre' => 'Período probatorio',         'tipo' => 'otro',       'plazo_dias' => 50,   'tipo_computo' => 'corrido','es_critica' => false, 'descripcion' => 'Producción de prueba ofrecida (Art. 375)'],
                    ['orden' => 7, 'nombre' => 'Audiencia complementaria',   'tipo' => 'audiencia',  'plazo_dias' => 20,   'tipo_computo' => 'corrido','es_critica' => true,  'descripcion' => 'Recepción de prueba y alegatos (Art. 367)'],
                    ['orden' => 8, 'nombre' => 'Sentencia',                  'tipo' => 'resolucion', 'plazo_dias' => 30,   'tipo_computo' => 'corrido','es_critica' => true,  'descripcion' => 'Pronunciamiento de sentencia (Art. 213)'],
                    ['orden' => 9, 'nombre' => 'Apelación',                  'tipo' => 'recurso',    'plazo_dias' => 10,   'tipo_computo' => 'habil',  'es_critica' => false, 'descripcion' => 'Recurso de apelación ante Tribunal Departamental (Art. 256)'],
                    ['orden' => 10,'nombre' => 'Ejecución de sentencia',     'tipo' => 'ejecucion',  'plazo_dias' => null, 'tipo_computo' => null,    'es_critica' => false, 'descripcion' => 'Cumplimiento forzado del fallo ejecutoriado'],
                ],
            ],
            [
                'nombre'      => 'Proceso Extraordinario',
                'area_derecho' => 'civil',
                'activo'      => true,
                'etapas'      => [
                    ['orden' => 1, 'nombre' => 'Demanda',                'tipo' => 'escrito',    'plazo_dias' => null, 'tipo_computo' => null,    'es_critica' => true,  'descripcion' => 'Presentación de la demanda'],
                    ['orden' => 2, 'nombre' => 'Admisión y citación',    'tipo' => 'resolucion', 'plazo_dias' => 5,    'tipo_computo' => 'habil', 'es_critica' => true,  'descripcion' => 'Admisión de la demanda y citación'],
                    ['orden' => 3, 'nombre' => 'Contestación',           'tipo' => 'escrito',    'plazo_dias' => 15,   'tipo_computo' => 'habil', 'es_critica' => true,  'descripcion' => 'Contestación del demandado (Art. 388 Ley 439)'],
                    ['orden' => 4, 'nombre' => 'Audiencia única',        'tipo' => 'audiencia',  'plazo_dias' => 10,   'tipo_computo' => 'habil', 'es_critica' => true,  'descripcion' => 'Audiencia única: conciliación, prueba y alegatos (Art. 389)'],
                    ['orden' => 5, 'nombre' => 'Sentencia',              'tipo' => 'resolucion', 'plazo_dias' => 10,   'tipo_computo' => 'habil', 'es_critica' => true,  'descripcion' => 'Pronunciamiento de sentencia en audiencia o diferida'],
                    ['orden' => 6, 'nombre' => 'Apelación',              'tipo' => 'recurso',    'plazo_dias' => 5,    'tipo_computo' => 'habil', 'es_critica' => false, 'descripcion' => 'Recurso de apelación (Art. 256)'],
                    ['orden' => 7, 'nombre' => 'Ejecución de sentencia', 'tipo' => 'ejecucion',  'plazo_dias' => null, 'tipo_computo' => null,    'es_critica' => false, 'descripcion' => 'Cumplimiento forzado del fallo'],
                ],
            ],
            [
                'nombre'      => 'Proceso Monitorio',
                'area_derecho' => 'civil',
                'activo'      => true,
                'etapas'      => [
                    ['orden' => 1, 'nombre' => 'Solicitud monitoria',        'tipo' => 'escrito',    'plazo_dias' => null, 'tipo_computo' => null,    'es_critica' => true,  'descripcion' => 'Solicitud de pago con documento que acredite la deuda (Art. 394)'],
                    ['orden' => 2, 'nombre' => 'Mandamiento de pago',        'tipo' => 'resolucion', 'plazo_dias' => 3,    'tipo_computo' => 'habil', 'es_critica' => true,  'descripcion' => 'El juez emite mandamiento intimando el pago en 10 días'],
                    ['orden' => 3, 'nombre' => 'Oposición del deudor',       'tipo' => 'escrito',    'plazo_dias' => 10,   'tipo_computo' => 'habil', 'es_critica' => true,  'descripcion' => 'El deudor puede oponerse al mandamiento (Art. 396)'],
                    ['orden' => 4, 'nombre' => 'Resolución de oposición',    'tipo' => 'resolucion', 'plazo_dias' => 10,   'tipo_computo' => 'habil', 'es_critica' => true,  'descripcion' => 'El juez resuelve la oposición o convierte en ordinario'],
                    ['orden' => 5, 'nombre' => 'Ejecución',                  'tipo' => 'ejecucion',  'plazo_dias' => null, 'tipo_computo' => null,    'es_critica' => false, 'descripcion' => 'Si no hay oposición o se rechaza, se ejecuta la deuda'],
                ],
            ],
            [
                'nombre'      => 'Proceso de Ejecución',
                'area_derecho' => 'civil',
                'activo'      => true,
                'etapas'      => [
                    ['orden' => 1, 'nombre' => 'Demanda ejecutiva',          'tipo' => 'escrito',    'plazo_dias' => null, 'tipo_computo' => null,    'es_critica' => true,  'descripcion' => 'Presentación de título ejecutivo (Art. 397 Ley 439)'],
                    ['orden' => 2, 'nombre' => 'Auto de intimación',         'tipo' => 'resolucion', 'plazo_dias' => 3,    'tipo_computo' => 'habil', 'es_critica' => true,  'descripcion' => 'El juez intima al deudor a pagar en 3 días'],
                    ['orden' => 3, 'nombre' => 'Excepciones del ejecutado',  'tipo' => 'escrito',    'plazo_dias' => 5,    'tipo_computo' => 'habil', 'es_critica' => false, 'descripcion' => 'El deudor puede oponer excepciones limitadas (Art. 401)'],
                    ['orden' => 4, 'nombre' => 'Embargo y avalúo',           'tipo' => 'otro',       'plazo_dias' => 10,   'tipo_computo' => 'habil', 'es_critica' => true,  'descripcion' => 'Embargo de bienes y designación de perito para avalúo'],
                    ['orden' => 5, 'nombre' => 'Remate',                     'tipo' => 'audiencia',  'plazo_dias' => 20,   'tipo_computo' => 'corrido','es_critica' => true,  'descripcion' => 'Subasta pública de los bienes embargados'],
                    ['orden' => 6, 'nombre' => 'Liquidación y pago',         'tipo' => 'ejecucion',  'plazo_dias' => 5,    'tipo_computo' => 'habil', 'es_critica' => false, 'descripcion' => 'Liquidación definitiva y pago al acreedor'],
                ],
            ],
        ];

        foreach ($tipos as $data) {
            $etapas = $data['etapas'];
            unset($data['etapas']);

            $tipo = TipoProceso::create($data);

            foreach ($etapas as $etapa) {
                $tipo->etapas()->create($etapa);
            }
        }
    }
}
