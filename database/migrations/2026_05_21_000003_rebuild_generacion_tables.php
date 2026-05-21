<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Eliminar tablas del esquema anterior ────────────────────────────
        DB::statement('DROP TABLE IF EXISTS borradores CASCADE');
        DB::statement('DROP TABLE IF EXISTS documentos_exportados CASCADE');
        DB::statement('DROP TABLE IF EXISTS plantillas_documentos CASCADE');
        DB::statement('DROP TABLE IF EXISTS generaciones CASCADE');

        // ── 2. Plantillas estructurales (DB-driven, JSONB) ─────────────────────
        DB::statement(<<<SQL
            CREATE TABLE plantillas (
                id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                tipo_documento  TEXT NOT NULL,
                subtipo         TEXT NOT NULL,
                nombre          TEXT NOT NULL,
                descripcion     TEXT,
                secciones       JSONB NOT NULL,
                version         TEXT NOT NULL DEFAULT 'v1',
                activa          BOOLEAN NOT NULL DEFAULT TRUE,
                created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),

                CONSTRAINT uq_plantilla UNIQUE (tipo_documento, subtipo, version)
            )
        SQL);

        // ── 3. Generaciones (nuevo esquema) ───────────────────────────────────
        DB::statement(<<<SQL
            CREATE TABLE generaciones (
                id               UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                usuario_id       UUID NOT NULL REFERENCES users(id),
                expediente_id    UUID REFERENCES expedientes(id) ON DELETE SET NULL,

                tipo_documento   TEXT NOT NULL,
                subtipo          TEXT NOT NULL,
                plantilla_id     UUID REFERENCES plantillas(id),
                formato_salida   TEXT NOT NULL CHECK (formato_salida IN ('estructurado', 'corrido')),

                input_formulario JSONB NOT NULL,

                estado           TEXT NOT NULL DEFAULT 'generando'
                                 CHECK (estado IN ('generando', 'completado', 'error', 'cancelada', 'editado')),
                cancelada        BOOLEAN NOT NULL DEFAULT FALSE,

                contenido_actual TEXT,

                llm_provider     TEXT,
                llm_model        TEXT,
                tokens_input     INT DEFAULT 0,
                tokens_output    INT DEFAULT 0,

                created_at       TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                updated_at       TIMESTAMPTZ NOT NULL DEFAULT NOW()
            )
        SQL);

        DB::statement('CREATE INDEX idx_generaciones_usuario    ON generaciones (usuario_id)');
        DB::statement('CREATE INDEX idx_generaciones_expediente ON generaciones (expediente_id)');
        DB::statement('CREATE INDEX idx_generaciones_estado     ON generaciones (estado)');

        // ── 4. Trazabilidad por sección ────────────────────────────────────────
        DB::statement(<<<SQL
            CREATE TABLE generacion_secciones (
                id                 UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                generacion_id      UUID NOT NULL REFERENCES generaciones(id) ON DELETE CASCADE,

                seccion_id         TEXT NOT NULL,
                orden              INT NOT NULL,

                contenido_generado TEXT NOT NULL,
                contenido_editado  TEXT,

                chunks_usados      JSONB NOT NULL DEFAULT '{"chunks": []}',
                prompt_usado       TEXT,

                tokens_input       INT DEFAULT 0,
                tokens_output      INT DEFAULT 0,
                regenerada_count   INT DEFAULT 0,

                created_at         TIMESTAMPTZ NOT NULL DEFAULT NOW(),
                updated_at         TIMESTAMPTZ NOT NULL DEFAULT NOW()
            )
        SQL);

        DB::statement('CREATE INDEX idx_gen_secciones_generacion ON generacion_secciones (generacion_id)');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS generacion_secciones CASCADE');
        DB::statement('DROP TABLE IF EXISTS generaciones CASCADE');
        DB::statement('DROP TABLE IF EXISTS plantillas CASCADE');
    }
};
