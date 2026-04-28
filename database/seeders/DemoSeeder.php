<?php

namespace Database\Seeders;

use App\Models\Actuacion;
use App\Models\Expediente;
use App\Models\Juzgado;
use App\Models\Parte;
use App\Models\Persona;
use App\Models\TipoProceso;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Usuarios adicionales ─────────────────────────────────────────────
        $abogado1 = User::firstOrCreate(
            ['correo' => 'carlos.rosales@rosalesasociados.bo'],
            [
                'nombre'   => 'Dr. Carlos Rosales Mendoza',
                'password' => Hash::make('password'),
                'rol'      => 'abogado',
                'activo'   => true,
            ]
        );

        $abogado2 = User::firstOrCreate(
            ['correo' => 'ana.quiroga@rosalesasociados.bo'],
            [
                'nombre'   => 'Dra. Ana Belén Quiroga Villena',
                'password' => Hash::make('password'),
                'rol'      => 'abogado',
                'activo'   => true,
            ]
        );

        $pasante = User::firstOrCreate(
            ['correo' => 'pedro.mamani@rosalesasociados.bo'],
            [
                'nombre'   => 'Pedro Mamani Flores',
                'password' => Hash::make('password'),
                'rol'      => 'pasante',
                'activo'   => true,
            ]
        );

        // Abogado de pruebas existente
        $abogadoPruebas = User::where('correo', 'abogado@gmail.com')->first();

        // ─── Personas naturales ───────────────────────────────────────────────
        $personas = [];

        $personasData = [
            // Clientes del estudio (demandantes)
            [
                'nombre_completo' => 'Marco Antonio Villarroel Sánchez',
                'ci_nit'          => '5234567 CB',
                'tipo_persona'    => 'natural',
                'telefono'        => '72345678',
                'correo'          => 'marco.villarroel@gmail.com',
                'direccion'       => 'Av. Blanco Galindo Km 3, Cochabamba',
            ],
            [
                'nombre_completo' => 'Sofía Esperanza Gutiérrez Rodas',
                'ci_nit'          => '7812345 CB',
                'tipo_persona'    => 'natural',
                'telefono'        => '71234567',
                'correo'          => 'sofia.gutierrez@hotmail.com',
                'direccion'       => 'Calle Sucre Nº 456, Zona Central, Cochabamba',
            ],
            [
                'nombre_completo' => 'Juan Pablo Flores Arce',
                'ci_nit'          => '8765432 CB',
                'tipo_persona'    => 'natural',
                'telefono'        => '73456789',
                'correo'          => 'jpflores@gmail.com',
                'direccion'       => 'Av. América Nº 1234, Cochabamba',
            ],
            [
                'nombre_completo' => 'Diego Alejandro Rojas Cabrera',
                'ci_nit'          => '9012345 CB',
                'tipo_persona'    => 'natural',
                'telefono'        => '78901234',
                'correo'          => 'diego.rojas@gmail.com',
                'direccion'       => 'Av. Heroínas Nº 678, Cochabamba',
            ],
            [
                'nombre_completo' => 'Fernanda Alejandra Cruz Molina',
                'ci_nit'          => '5678901 CB',
                'tipo_persona'    => 'natural',
                'telefono'        => '70987654',
                'correo'          => 'fernanda.cruz@gmail.com',
                'direccion'       => 'Calle Jordán Nº 321, Cochabamba',
            ],
            [
                'nombre_completo' => 'Lucía del Carmen Salinas Quispe',
                'ci_nit'          => '2345678 CB',
                'tipo_persona'    => 'natural',
                'telefono'        => '76543210',
                'correo'          => 'lucia.salinas@gmail.com',
                'direccion'       => 'Av. Tadeo Haenke Nº 890, Cochabamba',
            ],
            // Contrapartes (demandados)
            [
                'nombre_completo' => 'Roberto Carlos Méndez Paz',
                'ci_nit'          => '3456789 CB',
                'tipo_persona'    => 'natural',
                'telefono'        => '74567890',
                'correo'          => null,
                'direccion'       => 'Calle Colombia Nº 234, Cochabamba',
            ],
            [
                'nombre_completo' => 'Elena Cristina Torres Vásquez',
                'ci_nit'          => '6123456 CB',
                'tipo_persona'    => 'natural',
                'telefono'        => '77890123',
                'correo'          => null,
                'direccion'       => 'Calle Oquendo Nº 567, Cochabamba',
            ],
            [
                'nombre_completo' => 'Patricia Inés Mamani Condori',
                'ci_nit'          => '4321876 CB',
                'tipo_persona'    => 'natural',
                'telefono'        => '79012345',
                'correo'          => null,
                'direccion'       => 'Av. Santa Cruz Nº 1122, Cochabamba',
            ],
            [
                'nombre_completo' => 'Hernán Gustavo Peña Vargas',
                'ci_nit'          => '7654321 CB',
                'tipo_persona'    => 'natural',
                'telefono'        => '71122334',
                'correo'          => null,
                'direccion'       => 'Calle Bolívar Nº 789, Cochabamba',
            ],
        ];

        foreach ($personasData as $data) {
            $personas[] = Persona::firstOrCreate(['ci_nit' => $data['ci_nit']], $data);
        }

        // ─── Personas jurídicas ───────────────────────────────────────────────
        $personasJuridicasData = [
            [
                'nombre_completo' => 'Inversiones del Valle Ltda.',
                'ci_nit'          => '3045678901',
                'tipo_persona'    => 'juridica',
                'telefono'        => '44567890',
                'correo'          => 'legal@inversionesdelvalle.bo',
                'direccion'       => 'Av. Ballivián Nº 1500, Torre Empresarial, Piso 3, Cochabamba',
            ],
            [
                'nombre_completo' => 'Constructora Boliviana del Centro S.R.L.',
                'ci_nit'          => '1023456789',
                'tipo_persona'    => 'juridica',
                'telefono'        => '44123456',
                'correo'          => 'administracion@constructoraboliviana.bo',
                'direccion'       => 'Av. Villazón Nº 456, Cochabamba',
            ],
            [
                'nombre_completo' => 'Importaciones Andinas S.A.',
                'ci_nit'          => '2034567890',
                'tipo_persona'    => 'juridica',
                'telefono'        => '44234567',
                'correo'          => 'contacto@importacionesandinas.bo',
                'direccion'       => 'Calle Baptista Nº 890, Zona Comercial, Cochabamba',
            ],
            [
                'nombre_completo' => 'Banco FIE S.A.',
                'ci_nit'          => '4056789012',
                'tipo_persona'    => 'juridica',
                'telefono'        => '44345678',
                'correo'          => 'asesorialegal@bancoFIE.bo',
                'direccion'       => 'Av. Heroínas Nº 100, Cochabamba',
            ],
            [
                'nombre_completo' => 'Cooperativa Agrícola del Chapare R.L.',
                'ci_nit'          => '5067890123',
                'tipo_persona'    => 'juridica',
                'telefono'        => '44456789',
                'correo'          => 'legal@coopchapare.bo',
                'direccion'       => 'Av. Oquendo Nº 345, Cochabamba',
            ],
        ];

        $personasJuridicas = [];
        foreach ($personasJuridicasData as $data) {
            $personasJuridicas[] = Persona::firstOrCreate(['ci_nit' => $data['ci_nit']], $data);
        }

        // Alias semánticos
        [$inversionesValle, $constructoraCBC, $importacionesAndinas, $bancoFIE, $coopChapare] = $personasJuridicas;
        [$villarroel, $gutierrez, $floresArce, $rojasCA, $cruzMolina, $salinasQ,
         $mendezPaz, $torresVasquez, $mamaCondr, $penaVargas] = $personas;

        // ─── Juzgados (referencia) ────────────────────────────────────────────
        $jCivil1   = Juzgado::where('nombre', 'like', '%Civil y Comercial Nº 1%')->first();
        $jCivil2   = Juzgado::where('nombre', 'like', '%Civil y Comercial Nº 2%')->first();
        $jCivil3   = Juzgado::where('nombre', 'like', '%Civil y Comercial Nº 3%')->first();
        $jCivil4   = Juzgado::where('nombre', 'like', '%Civil y Comercial Nº 4%')->first();
        $jCivil5   = Juzgado::where('nombre', 'like', '%Civil y Comercial Nº 5%')->first();
        $jLaboral1 = Juzgado::where('nombre', 'like', '%Laboral y Social Nº 1%')->first();
        $jSalaCivil1 = Juzgado::where('nombre', 'like', '%Sala Civil Primera%')->first();

        // ─── Tipos de proceso (referencia) ───────────────────────────────────
        $tOrdinario    = TipoProceso::where('nombre', 'Proceso Ordinario')->first();
        $tExtraordinario = TipoProceso::where('nombre', 'Proceso Extraordinario')->first();
        $tMonitorio    = TipoProceso::where('nombre', 'Proceso Monitorio')->first();
        $tEjecucion    = TipoProceso::where('nombre', 'Proceso de Ejecución')->first();

        // ─── Expedientes ──────────────────────────────────────────────────────
        // 1. Resolución de contrato + daños — Ordinario — ACTIVO
        $exp1 = Expediente::create([
            'numero_expediente' => '439/2024',
            'juzgado_id'        => $jCivil2->id,
            'tipo_proceso_id'   => $tOrdinario->id,
            'abogado_id'        => $abogado1->id,
            'estado'            => 'activo',
            'fecha_inicio'      => '2024-03-10',
        ]);
        Parte::create(['expediente_id' => $exp1->id, 'persona_id' => $inversionesValle->id, 'rol_procesal' => 'demandante', 'es_cliente' => true]);
        Parte::create(['expediente_id' => $exp1->id, 'persona_id' => $constructoraCBC->id,  'rol_procesal' => 'demandado',  'es_cliente' => false]);
        $this->actuaciones($exp1->id, $abogado1->id, [
            ['escrito',      '2024-03-10', 'Presentación de la demanda de resolución de contrato de construcción por incumplimiento de plazos y especificaciones técnicas, con pretensión de daños y perjuicios por Bs. 450.000.'],
            ['resolucion',   '2024-03-18', 'Auto admisorio de la demanda. El Juez admite a trámite la demanda y ordena la citación a la parte demandada Constructora Boliviana del Centro S.R.L. con término de 30 días para contestar.'],
            ['notificacion', '2024-04-05', 'Diligencia de citación efectuada al representante legal de la demandada en domicilio de Av. Villazón Nº 456, Cochabamba. Cédula dejada con empleado. Notificación válida según Art. 78 Ley 439.'],
            ['escrito',      '2024-04-22', 'Contestación a la demanda. La parte demandada niega los hechos alegados, argumenta cumplimiento parcial justificado por causas de fuerza mayor (lluvias atípicas) y solicita reconvención por pago de adicionales.'],
            ['audiencia',    '2024-05-20', 'Audiencia preliminar celebrada. Se intentó conciliación sin acuerdo. Se fijaron los hechos a probar: (1) incumplimiento imputable, (2) cuantía de daños, (3) fuerza mayor alegada. Período probatorio: 50 días.'],
            ['otro',         '2024-07-08', 'Informe pericial presentado por Ing. Jorge Alvarado Suárez (perito designado). Conclusión: retraso de 4 meses atribuible a deficiencias organizativas del contratista. Daños estimados en Bs. 380.000.'],
            ['audiencia',    '2024-09-05', 'Audiencia complementaria. Producción de prueba testimonial (3 testigos por cada parte). Alegatos in voce. Autos para sentencia.'],
        ]);

        // 2. Ejecución hipotecaria — Ejecución — ACTIVO
        $exp2 = Expediente::create([
            'numero_expediente' => '112/2024',
            'juzgado_id'        => $jCivil1->id,
            'tipo_proceso_id'   => $tEjecucion->id,
            'abogado_id'        => $abogado2->id,
            'estado'            => 'activo',
            'fecha_inicio'      => '2024-01-15',
        ]);
        Parte::create(['expediente_id' => $exp2->id, 'persona_id' => $bancoFIE->id,    'rol_procesal' => 'demandante', 'es_cliente' => false]);
        Parte::create(['expediente_id' => $exp2->id, 'persona_id' => $villarroel->id,  'rol_procesal' => 'demandado',  'es_cliente' => true]);
        $this->actuaciones($exp2->id, $abogado2->id, [
            ['escrito',    '2024-01-15', 'Demanda ejecutiva presentada por Banco FIE S.A. con título hipotecario constituido sobre inmueble ubicado en Av. Blanco Galindo Km 3 (Testimonio de Escritura Pública Nº 234/2021 de Notaría Nº 8). Capital adeudado: $us. 45.000 más intereses.'],
            ['resolucion', '2024-01-22', 'Auto de intimación de pago. Se intima al ejecutado Marco Antonio Villarroel Sánchez a pagar en el término de 3 días hábiles la suma de $us. 45.000 más intereses corrientes. Bajo apercibimiento de embargo.'],
            ['escrito',    '2024-02-02', 'El ejecutado opone excepción de pago parcial documentado (Art. 401 Ley 439), acompañando recibos por $us. 12.000. Solicita liquidación actualizada. Se corre traslado al ejecutante.'],
            ['resolucion', '2024-02-20', 'Auto rechazando la excepción por insuficiencia documental. Se ordena proseguir la ejecución. Mandamiento de embargo sobre el bien hipotecado.'],
            ['otro',       '2024-03-15', 'Embargo anotado en Derechos Reales, matrícula 3.01.2.02.0012345. Perito avaluador Arq. Patricia Soliz designada. Plazo para avalúo: 20 días hábiles.'],
            ['otro',       '2024-04-10', 'Informe de avalúo presentado: inmueble valorado en $us. 68.000. Aprobado por el juzgado. Se fija audiencia de remate para la fecha que corresponda.'],
        ]);

        // 3. Cobro monitorio deuda personal — CONCLUIDO
        $exp3 = Expediente::create([
            'numero_expediente' => '78/2024',
            'juzgado_id'        => $jCivil3->id,
            'tipo_proceso_id'   => $tMonitorio->id,
            'abogado_id'        => $abogado1->id,
            'estado'            => 'concluido',
            'fecha_inicio'      => '2024-02-05',
        ]);
        Parte::create(['expediente_id' => $exp3->id, 'persona_id' => $gutierrez->id,  'rol_procesal' => 'demandante', 'es_cliente' => true]);
        Parte::create(['expediente_id' => $exp3->id, 'persona_id' => $mendezPaz->id,  'rol_procesal' => 'demandado',  'es_cliente' => false]);
        $this->actuaciones($exp3->id, $abogado1->id, [
            ['escrito',    '2024-02-05', 'Solicitud monitoria presentada por Sofía Gutiérrez Rodas. Acompaña pagaré por Bs. 35.000 con firma del deudor Roberto Méndez Paz, vencido el 15/01/2024. Solicita mandamiento de pago conforme Art. 394 Ley 439.'],
            ['resolucion', '2024-02-12', 'Mandamiento de pago emitido. Se intima al deudor Roberto Carlos Méndez Paz a pagar Bs. 35.000 más intereses legales en el término de 10 días hábiles, bajo apercibimiento de ejecución inmediata.'],
            ['notificacion','2024-02-15', 'Citación personal efectuada al deudor en Calle Colombia Nº 234. Firmó el recibo de notificación. Plazo vence el 29 de febrero de 2024.'],
            ['otro',       '2024-02-28', 'El deudor Roberto Méndez Paz realiza pago total mediante depósito judicial de Bs. 36.200 (capital + intereses + costas). Se decreta conclusión del proceso con archivo de obrados. Concluido favorablemente.'],
        ]);

        // 4. Nulidad de contrato de compraventa — EN APELACIÓN
        $exp4 = Expediente::create([
            'numero_expediente' => '892/2023',
            'juzgado_id'        => $jSalaCivil1->id,
            'tipo_proceso_id'   => $tOrdinario->id,
            'abogado_id'        => $abogado1->id,
            'estado'            => 'en_apelacion',
            'fecha_inicio'      => '2023-11-10',
        ]);
        Parte::create(['expediente_id' => $exp4->id, 'persona_id' => $floresArce->id,     'rol_procesal' => 'demandante', 'es_cliente' => true]);
        Parte::create(['expediente_id' => $exp4->id, 'persona_id' => $torresVasquez->id,  'rol_procesal' => 'demandado',  'es_cliente' => false]);
        $this->actuaciones($exp4->id, $abogado1->id, [
            ['escrito',    '2023-11-10', 'Demanda de nulidad de contrato de compraventa de lote de terreno (1.200 m²) ubicado en zona Sacaba. Fundamentos: vicio en el consentimiento y falta de capacidad del vendedor al momento de suscribir la minuta (Art. 549 Código Civil).'],
            ['resolucion', '2023-11-20', 'Auto admisorio. Se admite la demanda y se dispone la citación de la demandada Elena Cristina Torres Vásquez con término de 30 días corridos para contestar.'],
            ['escrito',    '2023-12-22', 'Contestación a la demanda. La demandada niega los hechos y afirma que la compraventa es válida, adjuntando certificado médico de salud del vendedor y escritura pública debidamente protocolizada.'],
            ['audiencia',  '2024-01-18', 'Audiencia preliminar. No hubo acuerdo conciliatorio. Hechos a probar fijados: (1) capacidad legal del vendedor, (2) existencia de vicios del consentimiento. Se abre período probatorio de 50 días.'],
            ['otro',       '2024-02-20', 'Informe médico forense emitido por el IDIF: el vendedor presentaba deterioro cognitivo moderado al momento de la firma. Prueba pericial calígrafica admitida y producida.'],
            ['resolucion', '2024-03-25', 'Sentencia Nº 45/2024. Se declara PROBADA la demanda. Se anula el contrato de compraventa por incapacidad del vendedor. Costas a la demandada. La demandada notificada el 28/03/2024.'],
            ['recurso',    '2024-04-08', 'Recurso de apelación interpuesto por Elena Torres Vásquez contra Sentencia Nº 45/2024. Se fundamenta en errónea valoración de la prueba pericial médica. Expediente remitido al Tribunal Departamental.'],
        ]);

        // 5. Desalojo por vencimiento de contrato — Extraordinario — ACTIVO
        $exp5 = Expediente::create([
            'numero_expediente' => '234/2024',
            'juzgado_id'        => $jCivil4->id,
            'tipo_proceso_id'   => $tExtraordinario->id,
            'abogado_id'        => $abogado2->id,
            'estado'            => 'activo',
            'fecha_inicio'      => '2024-04-05',
        ]);
        Parte::create(['expediente_id' => $exp5->id, 'persona_id' => $rojasCA->id,    'rol_procesal' => 'demandante', 'es_cliente' => true]);
        Parte::create(['expediente_id' => $exp5->id, 'persona_id' => $mamaCondr->id,  'rol_procesal' => 'demandado',  'es_cliente' => false]);
        $this->actuaciones($exp5->id, $abogado2->id, [
            ['escrito',    '2024-04-05', 'Demanda de desalojo por vencimiento de contrato de arrendamiento. El contrato con vigencia de 2 años venció el 28/02/2024 y la arrendataria Patricia Mamani Condori se niega a desocupar el inmueble ubicado en Av. Santa Cruz Nº 1122. Se reclama desalojo y daños por ocupación indebida.'],
            ['resolucion', '2024-04-15', 'Auto admisorio. Se admite la demanda de desalojo bajo proceso extraordinario (Art. 388 Ley 439). Se ordena citación de la demandada con término de 15 días hábiles para contestar.'],
            ['notificacion','2024-04-25', 'Citación personal realizada a Patricia Inés Mamani Condori en domicilio del inmueble objeto de litigio. Aceptó la cédula de notificación. Plazo para contestar vence el 16 de mayo de 2024.'],
            ['escrito',    '2024-05-14', 'Contestación a la demanda. La demandada alega tener contrato verbal prorrogado y ofrece pago de cánones pendientes. No adjunta documentación que acredite la prórroga.'],
            ['audiencia',  '2024-06-10', 'Audiencia única celebrada. Se intentó conciliación; la parte actora rechazó la oferta de la demandada. Producción de prueba: contrato original y recibos de alquiler. Alegatos. Autos para sentencia en 10 días hábiles.'],
        ]);

        // 6. Divorcio y liquidación de bienes — Ordinario — ACTIVO (sin número)
        $exp6 = Expediente::create([
            'numero_expediente' => null,
            'juzgado_id'        => $jCivil5->id,
            'tipo_proceso_id'   => $tOrdinario->id,
            'abogado_id'        => $abogado2->id,
            'estado'            => 'activo',
            'fecha_inicio'      => '2025-01-10',
        ]);
        Parte::create(['expediente_id' => $exp6->id, 'persona_id' => $cruzMolina->id,  'rol_procesal' => 'demandante', 'es_cliente' => true]);
        Parte::create(['expediente_id' => $exp6->id, 'persona_id' => $penaVargas->id,  'rol_procesal' => 'demandado',  'es_cliente' => false]);
        $this->actuaciones($exp6->id, $abogado2->id, [
            ['escrito',    '2025-01-10', 'Demanda de divorcio unilateral conforme Art. 205 Código de las Familias. Causales: ruptura del proyecto de vida en común. Se solicita además liquidación de la sociedad conyugal (dos inmuebles, un vehículo y cuentas bancarias conjuntas) y guarda de hija menor.'],
            ['resolucion', '2025-01-22', 'Auto admisorio. Se admite la demanda. Citación al demandado Hernán Peña Vargas con término de 30 días corridos. Se fija pensión provisional de asistencia familiar para la hija menor mientras dure el proceso.'],
            ['escrito',    '2025-03-05', 'Contestación a la demanda y reconvención. El demandado acepta el divorcio pero impugna la distribución propuesta de bienes. Solicita en reconvención que se le adjudique el vehículo y el 60% de los ahorros bancarios.'],
        ]);

        // 7. Ejecución de pagaré — Ejecución — SUSPENDIDO
        $exp7 = Expediente::create([
            'numero_expediente' => '67/2025',
            'juzgado_id'        => $jCivil1->id,
            'tipo_proceso_id'   => $tEjecucion->id,
            'abogado_id'        => $abogadoPruebas?->id ?? $abogado1->id,
            'estado'            => 'suspendido',
            'fecha_inicio'      => '2025-02-05',
        ]);
        Parte::create(['expediente_id' => $exp7->id, 'persona_id' => $importacionesAndinas->id, 'rol_procesal' => 'demandante', 'es_cliente' => false]);
        Parte::create(['expediente_id' => $exp7->id, 'persona_id' => $salinasQ->id,              'rol_procesal' => 'demandado',  'es_cliente' => true]);
        $this->actuaciones($exp7->id, $abogadoPruebas?->id ?? $abogado1->id, [
            ['escrito',    '2025-02-05', 'Demanda ejecutiva interpuesta por Importaciones Andinas S.A. Título: pagaré por Bs. 82.500 emitido por Lucía del Carmen Salinas Quispe, vencido el 31/12/2024. Se adjunta pagaré original con protesto notarial.'],
            ['resolucion', '2025-02-14', 'Auto de intimación de pago. Se intima a la ejecutada Lucía Salinas Quispe a pagar Bs. 82.500 más intereses del 6% anual en 3 días hábiles bajo apercibimiento de embargo de bienes.'],
            ['escrito',    '2025-02-25', 'Incidente de nulidad de notificación planteado por la ejecutada. Alega que la intimación no fue practicada personalmente conforme Art. 75 Ley 439. Se corre traslado al ejecutante por 3 días.'],
            ['resolucion', '2025-03-12', 'Auto de suspensión del proceso. El Juez admite el incidente y suspende el proceso hasta resolver la nulidad de notificación. Se dispone nueva citación conforme a ley.'],
        ]);

        // 8. Incumplimiento de obra civil — Ordinario — ACTIVO (sin número)
        $exp8 = Expediente::create([
            'numero_expediente' => null,
            'juzgado_id'        => $jCivil2->id,
            'tipo_proceso_id'   => $tOrdinario->id,
            'abogado_id'        => $abogado1->id,
            'estado'            => 'activo',
            'fecha_inicio'      => '2025-03-01',
        ]);
        Parte::create(['expediente_id' => $exp8->id, 'persona_id' => $coopChapare->id,      'rol_procesal' => 'demandante', 'es_cliente' => true]);
        Parte::create(['expediente_id' => $exp8->id, 'persona_id' => $constructoraCBC->id,  'rol_procesal' => 'demandado',  'es_cliente' => false]);
        $this->actuaciones($exp8->id, $abogado1->id, [
            ['escrito',    '2025-03-01', 'Demanda ordinaria por incumplimiento de contrato de construcción de almacén agrícola (1.800 m²) en Municipio de Villa Tunari. La constructora abandonó obra con un 40% de avance tras recibir el 70% del precio pactado (Bs. 950.000). Se reclama devolución de pagos en exceso y daños emergentes.'],
            ['resolucion', '2025-03-14', 'Auto admisorio de la demanda. Se admite y se ordena la citación de Constructora Boliviana del Centro S.R.L. mediante su representante legal. Término de 30 días corridos para contestar.'],
        ]);

        // 9. Proceso Monitorio cobro de honorarios — ACTIVO
        $exp9 = Expediente::create([
            'numero_expediente' => '156/2025',
            'juzgado_id'        => $jCivil3->id,
            'tipo_proceso_id'   => $tMonitorio->id,
            'abogado_id'        => $abogado2->id,
            'estado'            => 'activo',
            'fecha_inicio'      => '2025-04-01',
        ]);
        Parte::create(['expediente_id' => $exp9->id, 'persona_id' => $inversionesValle->id, 'rol_procesal' => 'demandante', 'es_cliente' => true]);
        Parte::create(['expediente_id' => $exp9->id, 'persona_id' => $penaVargas->id,       'rol_procesal' => 'demandado',  'es_cliente' => false]);
        $this->actuaciones($exp9->id, $abogado2->id, [
            ['escrito',    '2025-04-01', 'Solicitud monitoria para cobro de facturas impagas por servicios de consultoría empresarial prestados durante el año 2024. Monto total: Bs. 48.000. Se acompañan 6 facturas originales y contrato de servicios suscrito entre partes.'],
            ['resolucion', '2025-04-08', 'Mandamiento de pago emitido. Se intima al deudor Hernán Gustavo Peña Vargas a pagar Bs. 48.000 en el término de 10 días hábiles. En caso de incumplimiento se procederá a la ejecución inmediata.'],
            ['notificacion','2025-04-14', 'Citación personal practicada al deudor Hernán Gustavo Peña Vargas en su domicilio, Calle Bolívar Nº 789, Cochabamba. Firmó recibo. Plazo vence el 28 de abril de 2025.'],
        ]);

        // 10. Demanda laboral por beneficios sociales — Ordinario — ACTIVO
        $exp10 = Expediente::create([
            'numero_expediente' => '301/2025',
            'juzgado_id'        => $jLaboral1->id,
            'tipo_proceso_id'   => $tOrdinario->id,
            'abogado_id'        => $abogado2->id,
            'estado'            => 'activo',
            'fecha_inicio'      => '2025-03-20',
        ]);
        Parte::create(['expediente_id' => $exp10->id, 'persona_id' => $villarroel->id,       'rol_procesal' => 'demandante', 'es_cliente' => true]);
        Parte::create(['expediente_id' => $exp10->id, 'persona_id' => $importacionesAndinas->id, 'rol_procesal' => 'demandado', 'es_cliente' => false]);
        $this->actuaciones($exp10->id, $abogado2->id, [
            ['escrito',    '2025-03-20', 'Demanda por cobro de beneficios sociales y reintegro de haberes. El actor Marco Villarroel trabajó para Importaciones Andinas S.A. del 01/03/2019 al 28/02/2025 (6 años). Se reclama: desahucio (Bs. 34.800), indemnización (Bs. 52.200), vacaciones no gozadas (Bs. 8.700) y aguinaldo proporcional. Total: Bs. 95.700.'],
            ['resolucion', '2025-03-28', 'Auto de admisión. Se admite la demanda laboral conforme CPT. Se ordena citación a Importaciones Andinas S.A. con término de 10 días hábiles para contestar. Se fija audiencia de conciliación.'],
            ['notificacion','2025-04-05', 'Notificación a representante legal de Importaciones Andinas S.A. en domicilio legal. Aceptó la cédula. Término para contestar: 19 de abril de 2025.'],
            ['audiencia',  '2025-04-20', 'Audiencia de conciliación. La empresa demandada ofreció Bs. 60.000 como pago total. El actor rechazó la oferta por insuficiente. Se declara fracasada la conciliación. Continuará con proceso ordinario laboral.'],
        ]);

        $this->command->info('✓ DemoSeeder ejecutado: 10 expedientes, 20 personas, 40+ actuaciones.');
    }

    /**
     * Helper para insertar actuaciones en bloque.
     * @param array<array{string, string, string}> $items [tipo_actuacion, fecha, descripcion]
     */
    private function actuaciones(string $expedienteId, string $usuarioId, array $items): void
    {
        foreach ($items as [$tipo, $fecha, $descripcion]) {
            Actuacion::create([
                'expediente_id'  => $expedienteId,
                'usuario_id'     => $usuarioId,
                'tipo_actuacion' => $tipo,
                'fecha'          => $fecha,
                'descripcion'    => $descripcion,
            ]);
        }
    }
}
