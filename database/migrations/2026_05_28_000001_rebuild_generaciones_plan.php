<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP TABLE IF EXISTS generacion_secciones CASCADE');
        DB::statement('DROP TABLE IF EXISTS generaciones CASCADE');

        DB::statement(<<<SQL
            CREATE TABLE generaciones (
                id               UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                usuario_id       UUID NOT NULL REFERENCES users(id),
                expediente_id    UUID REFERENCES expedientes(id) ON DELETE SET NULL,

                narracion              TEXT NOT NULL,
                incluir_jurisprudencia BOOLEAN NOT NULL DEFAULT FALSE,

                estado           TEXT NOT NULL DEFAULT 'analizando'
                                 CHECK (estado IN ('analizando','recuperando','generando','completado','error')),

                documento_html   TEXT,
                molde_usado_id   TEXT,

                advertencias     JSONB NOT NULL DEFAULT '[]',
                validaciones     JSONB NOT NULL DEFAULT '[]',

                error_msg        TEXT,
                calificacion     SMALLINT CHECK (calificacion BETWEEN 1 AND 5),

                llm_model        TEXT,
                tokens_input     INT DEFAULT 0,
                tokens_output    INT DEFAULT 0,

                created_at       TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                updated_at       TIMESTAMPTZ NOT NULL DEFAULT NOW()
            )
        SQL);

        DB::statement('CREATE INDEX idx_generaciones_usuario ON generaciones (usuario_id)');
        DB::statement('CREATE INDEX idx_generaciones_estado  ON generaciones (estado)');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS generaciones CASCADE');
    }
};
