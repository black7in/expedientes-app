<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlantillasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('plantillas')->truncate();

        $plantillas = [
            $this->ejecutiva(),
            $this->coactiva(),
            $this->terceria(),
            $this->desalojo(),
            $this->ordinaria(),
        ];

        foreach ($plantillas as $p) {
            DB::table('plantillas')->insert([
                'id'             => Str::uuid(),
                'tipo_documento' => $p['tipo_documento'],
                'subtipo'        => $p['subtipo'],
                'nombre'         => $p['nombre'],
                'descripcion'    => $p['descripcion'],
                'secciones'      => json_encode($p['secciones'], JSON_UNESCAPED_UNICODE),
                'version'        => 'v1',
                'activa'         => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }

    // ── Secciones comunes reutilizables ───────────────────────────────────────

    private function seccionEncabezado(string $subtipo): array
    {
        return [
            'id'          => 'encabezado',
            'nombre'      => 'Encabezado',
            'orden'       => 1,
            'obligatoria' => true,
            'instrucciones' => 'Redactá el encabezado dirigido al Juez Público de Turno en lo Civil de la jurisdicción correspondiente. Incluí el nombre del tribunal o juzgado. Usá formato boliviano formal en mayúsculas para el tratamiento del juez.',
            'retrieval' => [
                'fuentes' => [
                    ['tipo' => 'doc_chunks', 'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => $subtipo, 'seccion' => 'encabezado'], 'top_k' => 3],
                ],
            ],
            'salida' => [
                'estructurado' => '{contenido}',
                'corrido'      => '{contenido}',
            ],
        ];
    }

    private function seccionTipoDemanda(string $subtipo, string $titulo): array
    {
        return [
            'id'          => 'tipo_demanda',
            'nombre'      => 'Tipo de Demanda',
            'orden'       => 2,
            'obligatoria' => true,
            'instrucciones' => "Redactá una sola línea que identifique el tipo de proceso. Debe decir exactamente: \"{$titulo}\"",
            'retrieval' => [
                'fuentes' => [
                    ['tipo' => 'doc_chunks', 'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => $subtipo, 'seccion' => 'tipo_demanda'], 'top_k' => 2],
                ],
            ],
            'salida' => [
                'estructurado' => '{contenido}',
                'corrido'      => '{contenido}',
            ],
        ];
    }

    private function seccionAnuncioOtrosi(): array
    {
        return [
            'id'          => 'anuncio_otrosi',
            'nombre'      => 'Anuncio de Otrosíes',
            'orden'       => 3,
            'obligatoria' => true,
            'instrucciones' => "No se necesita generar contenido.",
            'retrieval' => ['fuentes' => []],
            'salida' => [
                'estructurado' => 'OTROSI.-',
                'corrido'      => 'OTROSI.-',
            ],
        ];
    }

    private function seccionGenerales(string $subtipo): array
    {
        return [
            'id'          => 'generales',
            'nombre'      => 'Generales del Demandante',
            'orden'       => 4,
            'obligatoria' => true,
            'instrucciones' => 'Redactá las generales de ley del demandante. Incluí: nombre completo en MAYÚSCULAS, "mayor de edad, hábil por derecho", estado civil, profesión u ocupación, domicilio, número de Cédula de Identidad con extensión departamental. Cerrá con la fórmula: "ante su Autoridad con respeto expongo y pido:"',
            'retrieval' => [
                'fuentes' => [
                    ['tipo' => 'doc_chunks', 'filtros' => ['tipo_doc' => 'demanda', 'seccion' => 'generales'], 'top_k' => 3],
                ],
            ],
            'salida' => [
                'estructurado' => '{contenido}',
                'corrido'      => '{contenido}',
            ],
        ];
    }

    private function seccionCierre(): array
    {
        return [
            'id'          => 'cierre',
            'nombre'      => 'Cierre',
            'orden'       => 99,
            'obligatoria' => true,
            'instrucciones' => 'Redactá el cierre de la demanda con ciudad y fecha en letras (ej: "Santa Cruz de la Sierra, veintiuno de mayo de dos mil veintiséis"). Dejá dos líneas en blanco para las firmas del abogado y la parte demandante.',
            'retrieval' => ['fuentes' => []],
            'salida' => [
                'estructurado' => '{contenido}',
                'corrido'      => '{contenido}',
            ],
        ];
    }

    // ── Plantilla 1: Demanda Ejecutiva ────────────────────────────────────────

    private function ejecutiva(): array
    {
        return [
            'tipo_documento' => 'demanda',
            'subtipo'        => 'ejecutiva',
            'nombre'         => 'Demanda Ejecutiva',
            'descripcion'    => 'Demanda ejecutiva en proceso monitorio por incumplimiento de obligación con título ejecutivo (pagaré, cheque, letra de cambio, documento reconocido)',
            'secciones'      => [
                $this->seccionEncabezado('ejecutiva'),
                $this->seccionTipoDemanda('ejecutiva', 'En Proceso Monitorio presenta Demanda Ejecutiva.'),
                $this->seccionAnuncioOtrosi(),
                $this->seccionGenerales('ejecutiva'),
                [
                    'id'          => 'cuerpo_principal',
                    'nombre'      => 'Cuerpo Principal',
                    'orden'       => 5,
                    'obligatoria' => true,
                    'instrucciones' => 'Redactá el cuerpo principal de la demanda ejecutiva en formato corrido (estilo boliviano). Incluí en orden: (1) Identificación del título ejecutivo (escritura pública, documento privado reconocido, letra de cambio, pagaré, etc.) con sus datos: número, fecha, notario. (2) Hechos: monto adeudado, plazo, fecha de vencimiento, intereses pactados. (3) Estado de mora: que el plazo está vencido y la obligación es exigible. (4) Gestiones de cobro infructuosas. (5) Necesidad de iniciar la demanda ejecutiva. (6) Identificación completa del demandado: nombre, CI, estado civil, ocupación, domicilio. (7) Fundamento legal: citá los artículos del Código Procesal Civil relativos al proceso ejecutivo. (8) Petitorio: solicitud de admisión + sentencia probada + pago de capital, intereses, costas, daños, honorarios. Usá conectores típicos bolivianos: "De la documentación que me permito acompañar...", "se establece con meridiana claridad...", "me veo en la imperiosa necesidad de iniciar...", "amparando mi proceso ejecutivo en...".',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks',   'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'ejecutiva', 'seccion' => 'hechos'], 'top_k' => 3],
                            ['tipo' => 'leyes_chunks', 'filtros' => ['ley' => 'Código Procesal Civil', 'numero_articulo' => ['379', '380', '381', '382', '383']], 'top_k' => 5],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => '{contenido}',
                        'corrido'      => '{contenido}',
                    ],
                ],
                [
                    'id'          => 'otrosies',
                    'nombre'      => 'Otrosíes',
                    'orden'       => 6,
                    'obligatoria' => true,
                    'instrucciones' => 'Generá los otrosíes numerados para la demanda ejecutiva. Típicamente incluyen: OTROSI 1.- Anotación preventiva del bien dado en garantía (si aplica). OTROSI 2.- Notificación a ASFI para retención de fondos (si aplica). OTROSI 3.- Mandamiento de embargo. OTROSI 4.- Arancel de honorarios profesionales. OTROSI 5.- Domicilio procesal del abogado. OTROSI 6.- Citaciones y notificaciones por servidor público. Adaptá según el caso. Cada otrosí en un párrafo separado con el título en mayúsculas seguido de ".-".',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks', 'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'ejecutiva', 'seccion' => 'otrosi'], 'top_k' => 6],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => '{contenido}',
                        'corrido'      => '{contenido}',
                    ],
                ],
                $this->seccionCierre(),
            ],
        ];
    }

    // ── Plantilla 2: Demanda Coactiva ─────────────────────────────────────────

    private function coactiva(): array
    {
        return [
            'tipo_documento' => 'demanda',
            'subtipo'        => 'coactiva',
            'nombre'         => 'Demanda Coactiva Civil',
            'descripcion'    => 'Demanda coactiva civil por incumplimiento de contrato con cláusula de renuncia al proceso ejecutivo',
            'secciones'      => [
                $this->seccionEncabezado('coactiva'),
                $this->seccionTipoDemanda('coactiva', 'Demanda Coactiva.'),
                $this->seccionAnuncioOtrosi(),
                $this->seccionGenerales('coactiva'),
                [
                    'id'          => 'cuerpo_principal',
                    'nombre'      => 'Cuerpo Principal',
                    'orden'       => 5,
                    'obligatoria' => true,
                    'instrucciones' => 'Redactá el cuerpo principal de la demanda coactiva en formato corrido. Incluí: (1) Identificación del título coactivo (contrato con escritura pública, con cláusula de renuncia al proceso ejecutivo): número de escritura, fecha, notario, protocolo. (2) Datos completos del demandado. (3) Hechos: monto prestado, plazo acordado, garantía ofrecida (vehículo, inmueble u otros), intereses pactados. (4) Incumplimiento: cuotas específicas no pagadas, fecha de mora. (5) Gestiones de cobro infructuosas. (6) Mención expresa de la cláusula de renuncia al proceso ejecutivo contenida en el contrato. (7) Fundamento legal: Arts. 110 y 404 del Código Procesal Civil. (8) Petitorio: admisión de la demanda + sentencia probada + pago en el tercer día bajo apercibimiento + remate del bien dado en garantía. Usá tono formal boliviano.',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks',   'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'coactiva', 'seccion' => 'hechos'], 'top_k' => 3],
                            ['tipo' => 'leyes_chunks', 'filtros' => ['ley' => 'Código Procesal Civil', 'numero_articulo' => ['110', '404']], 'top_k' => 3],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => '{contenido}',
                        'corrido'      => '{contenido}',
                    ],
                ],
                [
                    'id'          => 'otrosies',
                    'nombre'      => 'Otrosíes',
                    'orden'       => 6,
                    'obligatoria' => true,
                    'instrucciones' => 'Generá los otrosíes de la demanda coactiva: OTROSI 1.- Generales del demandado y datos del título coactivo. OTROSI 2.- Anotación del bien dado en garantía y prohibición de enajenar. OTROSI 3.- Mandamiento de embargo del bien garantizado. OTROSI 4.- Arancel de honorarios profesionales. OTROSI 5.- Domicilio procesal del abogado. OTROSI 6.- Citaciones por servidor público. Adaptá según el caso.',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks', 'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'coactiva', 'seccion' => 'otrosi'], 'top_k' => 6],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => '{contenido}',
                        'corrido'      => '{contenido}',
                    ],
                ],
                $this->seccionCierre(),
            ],
        ];
    }

    // ── Plantilla 3: Demanda de Tercería ─────────────────────────────────────

    private function terceria(): array
    {
        return [
            'tipo_documento' => 'demanda',
            'subtipo'        => 'terceria',
            'nombre'         => 'Demanda de Tercería',
            'descripcion'    => 'Tercería de dominio preferente o excluyente dentro de proceso ejecutivo en curso',
            'secciones'      => [
                $this->seccionEncabezado('terceria'),
                $this->seccionTipoDemanda('terceria', 'Demanda de Tercería de Dominio.'),
                $this->seccionAnuncioOtrosi(),
                $this->seccionGenerales('terceria'),
                [
                    'id'          => 'cuerpo_principal',
                    'nombre'      => 'Cuerpo Principal',
                    'orden'       => 5,
                    'obligatoria' => true,
                    'instrucciones' => 'Redactá el cuerpo de la tercería. Estructura: (1) Mención del proceso ejecutivo en curso: "dentro el proceso ejecutivo seguido por [DEMANDANTE_ORIGINAL] contra [DEMANDADO_ORIGINAL], radicado ante este juzgado". (2) Si es TERCERÍA PREFERENTE: documenta el préstamo anterior con garantía sobre el mismo bien, argumenta prioridad temporal respecto a la ejecución actual. Si es TERCERÍA EXCLUYENTE: documenta el vínculo de matrimonio o copropiedad, argumenta que el 50% (u otro porcentaje) del bien pertenece al tercerista y no puede ser ejecutado. (3) Cómo se enteró del remate (notificación, publicación en periódico, etc.). (4) Datos del bien objeto de litigio: descripción, folio real, matrícula, ubicación. (5) Fundamento legal: PREFERENTE: Art. 53 del CPC. EXCLUYENTE: Art. 360 CPC + Art. 243 Código de Familia. (6) Petitorio: que se declare probada la tercería con las consecuencias legales correspondientes.',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks',   'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'terceria', 'seccion' => 'hechos'], 'top_k' => 3],
                            ['tipo' => 'leyes_chunks', 'filtros' => ['ley' => 'Código Procesal Civil', 'numero_articulo' => ['53', '360']], 'top_k' => 3],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => '{contenido}',
                        'corrido'      => '{contenido}',
                    ],
                ],
                [
                    'id'          => 'otrosies',
                    'nombre'      => 'Otrosíes',
                    'orden'       => 6,
                    'obligatoria' => true,
                    'instrucciones' => 'Generá los otrosíes de la tercería: OTROSI 1.- Acompañar prueba documental (documento base del derecho del tercerista: contrato de préstamo, folio real, certificado de matrimonio, etc.). OTROSI 2.- Suspensión del remate hasta resolver la tercería. OTROSI 3.- Arancel de honorarios profesionales. OTROSI 4.- Domicilio procesal del abogado. OTROSI 5.- Citaciones por servidor público.',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks', 'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'terceria', 'seccion' => 'otrosi'], 'top_k' => 5],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => '{contenido}',
                        'corrido'      => '{contenido}',
                    ],
                ],
                $this->seccionCierre(),
            ],
        ];
    }

    // ── Plantilla 4: Demanda de Desalojo ─────────────────────────────────────

    private function desalojo(): array
    {
        return [
            'tipo_documento' => 'demanda',
            'subtipo'        => 'desalojo',
            'nombre'         => 'Demanda de Desalojo',
            'descripcion'    => 'Demanda de desalojo de inmueble por vencimiento de contrato, falta de pago o incumplimiento del inquilino',
            'secciones'      => [
                $this->seccionEncabezado('desalojo'),
                $this->seccionTipoDemanda('desalojo', 'Demanda de Desalojo.'),
                $this->seccionAnuncioOtrosi(),
                $this->seccionGenerales('desalojo'),
                [
                    'id'          => 'cuerpo_principal',
                    'nombre'      => 'Cuerpo Principal',
                    'orden'       => 5,
                    'obligatoria' => true,
                    'instrucciones' => 'Redactá el cuerpo principal de la demanda de desalojo. Incluí: (1) Antecedentes: cómo el demandado llegó a ocupar el inmueble (alquiler, comodato, cuidador, anticipo, etc.) y las condiciones pactadas. (2) Hechos cronológicos: fechas relevantes, motivo de la demanda (no pago de alquileres, vencimiento de plazo, necesidad propia, incumplimiento de contrato, etc.). (3) Gestiones previas de desocupación: intimaciones, requerimientos extrajudiciales. (4) Datos precisos del inmueble: ubicación exacta, código catastral, matrícula computarizada, distrito, provincia, departamento. (5) Identificación del demandado: nombre completo, estado civil, ocupación, domicilio actual. (6) Fundamento legal: Arts. 623 y siguientes del CPC; Arts. 635 y 636 del CPC para el lanzamiento. (7) Petitorio: admisión de la demanda + sentencia que declare probada + lanzamiento bajo apercibimiento de ley. Tono formal boliviano.',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks',   'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'desalojo', 'seccion' => 'hechos'], 'top_k' => 3],
                            ['tipo' => 'leyes_chunks', 'filtros' => ['ley' => 'Código Procesal Civil', 'numero_articulo' => ['623', '635', '636']], 'top_k' => 3],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => '{contenido}',
                        'corrido'      => '{contenido}',
                    ],
                ],
                [
                    'id'          => 'otrosies',
                    'nombre'      => 'Otrosíes',
                    'orden'       => 6,
                    'obligatoria' => true,
                    'instrucciones' => 'Generá los otrosíes de la demanda de desalojo: OTROSI 1.- Prueba documental (título de propiedad, plano arquitectónico, pago de impuestos, contrato de alquiler o anticrético si corresponde). OTROSI 2.- Declaración jurada de necesidad propia (si aplica). OTROSI 3.- Arancel del Colegio de Abogados. OTROSI 4.- Croquis de referencia del inmueble para notificación. OTROSI 5.- Domicilio procesal del abogado. OTROSI 6.- Citaciones y notificaciones por servidor público de la Policía Judicial.',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks', 'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'desalojo', 'seccion' => 'otrosi'], 'top_k' => 6],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => '{contenido}',
                        'corrido'      => '{contenido}',
                    ],
                ],
                $this->seccionCierre(),
            ],
        ];
    }

    // ── Plantilla 5: Demanda Ordinaria ────────────────────────────────────────

    private function ordinaria(): array
    {
        return [
            'tipo_documento' => 'demanda',
            'subtipo'        => 'ordinaria',
            'nombre'         => 'Demanda Ordinaria Civil',
            'descripcion'    => 'Demandas ordinarias civiles: nulidad, usucapión, regularización de derecho propietario, cumplimiento de contrato, división y partición, etc.',
            'secciones'      => [
                $this->seccionEncabezado('ordinaria'),
                $this->seccionTipoDemanda('ordinaria', 'Demanda Ordinaria Civil.'),
                $this->seccionAnuncioOtrosi(),
                $this->seccionGenerales('ordinaria'),
                [
                    'id'          => 'personeria',
                    'nombre'      => 'Personería',
                    'orden'       => 5,
                    'obligatoria' => false,
                    'instrucciones' => 'Si el demandante actúa mediante mandato (poder notarial), redactá la sección de personería: número de escritura del poder, notario otorgante, fecha, protocolo y facultades conferidas. Si actúa por derecho propio, omití esta sección (sección opcional).',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks', 'filtros' => ['tipo_doc' => 'demanda', 'seccion' => 'personeria'], 'top_k' => 2],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => "PERSONERIA.-\n\n{contenido}",
                        'corrido'      => '{contenido}',
                    ],
                ],
                [
                    'id'          => 'hechos',
                    'nombre'      => 'Fundamento de Hecho',
                    'orden'       => 6,
                    'obligatoria' => true,
                    'instrucciones' => 'Narrá los hechos del caso en orden cronológico. Comenzá con "Señor juez, en fecha [...]" o "Señora juez...". Usá tercera persona o primera persona según sea más natural. Mencioná documentos probatorios cuando corresponda (fechas, notarios, folios reales, matrículas, etc.). Estructurá en párrafos coherentes y no saltes en el tiempo sin transición. Sé específico con fechas, montos y documentos.',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks', 'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'ordinaria', 'seccion' => 'hechos'], 'top_k' => 3],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => "FUNDAMENTO DE HECHO.-\n\n{contenido}",
                        'corrido'      => '{contenido}',
                    ],
                ],
                [
                    'id'          => 'fundamento_derecho',
                    'nombre'      => 'Fundamento de Derecho',
                    'orden'       => 7,
                    'obligatoria' => true,
                    'instrucciones' => 'Citá los artículos del Código Civil y/o Código Procesal Civil boliviano aplicables al caso. Para cada artículo: mencioná el número y nombre, citá el texto pertinente de los chunks entregados, explicá brevemente su aplicación al caso. Solo usá artículos entregados en las referencias legales. NO inventes numeraciones ni textos de artículos.',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'leyes_chunks', 'filtros' => ['materia' => ['civil', 'procesal_civil']], 'top_k' => 8],
                            ['tipo' => 'doc_chunks',   'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'ordinaria', 'seccion' => 'fundamento_derecho'], 'top_k' => 2],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => "FUNDAMENTO DE DERECHO.-\n\n{contenido}",
                        'corrido'      => '{contenido}',
                    ],
                ],
                [
                    'id'          => 'petitorio',
                    'nombre'      => 'Petición',
                    'orden'       => 8,
                    'obligatoria' => true,
                    'instrucciones' => 'Redactá la petición concreta al juez. Comenzá con "Con todo lo expuesto anteriormente es que solicito a su autoridad declare probada la demanda con costas y costos, en consecuencia [PRETENSIÓN ESPECÍFICA]." Adaptá la pretensión según el tipo de demanda: Nulidad → declarar nulidad del instrumento + devolución; Usucapión → otorgar propiedad sobre el inmueble + notificar a Derechos Reales + cancelar gravámenes; Regularización → declarar propietario + inscripción en DDRR. Usá fórmulas formales bolivianas.',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks', 'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'ordinaria', 'seccion' => 'petitorio'], 'top_k' => 3],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => "PETICION.-\n\n{contenido}",
                        'corrido'      => '{contenido}',
                    ],
                ],
                [
                    'id'          => 'otrosies',
                    'nombre'      => 'Otrosíes',
                    'orden'       => 9,
                    'obligatoria' => true,
                    'instrucciones' => 'Generá los otrosíes para la demanda ordinaria. Típicamente incluyen: OTROSI 1.- Generales completas del demandado (nombre, CI, estado civil, domicilio). OTROSI 2.- Prueba documental acompañada (lista los documentos probatorios mencionados en los hechos). OTROSI 3.- Prueba testifical (si hay testigos, indicá que se reserva el derecho). OTROSI 4.- Inspección judicial (si aplica). OTROSI 5.- Prueba pericial (si aplica). OTROSI 6.- Notificación por edictos (si el demandado tiene domicilio desconocido). OTROSI 7.- Arancel de honorarios del Colegio de Abogados. OTROSI 8.- Domicilio procesal del abogado. OTROSI 9.- Citaciones y notificaciones por servidor público. Omití los otrosíes que no apliquen al caso.',
                    'retrieval' => [
                        'fuentes' => [
                            ['tipo' => 'doc_chunks', 'filtros' => ['tipo_doc' => 'demanda', 'subtipo' => 'ordinaria', 'seccion' => 'otrosi'], 'top_k' => 8],
                        ],
                    ],
                    'salida' => [
                        'estructurado' => '{contenido}',
                        'corrido'      => '{contenido}',
                    ],
                ],
                $this->seccionCierre(),
            ],
        ];
    }
}
