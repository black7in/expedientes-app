<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Eliminar FK antes de tocar columnas referenciadas
        DB::statement('ALTER TABLE memoriales_feedback DROP CONSTRAINT IF EXISTS memoriales_feedback_source_doc_id_foreign');

        // 2. Renombrar tabla principal
        Schema::rename('memoriales_moldes', 'plantillas_memoriales');

        // 3. Renombrar columna doc_id → id y cambiar tipo a UUID
        DB::statement('ALTER TABLE plantillas_memoriales RENAME COLUMN doc_id TO id');
        DB::statement('ALTER TABLE plantillas_memoriales ALTER COLUMN id TYPE uuid USING gen_random_uuid()');
        DB::statement('ALTER TABLE plantillas_memoriales ALTER COLUMN id SET DEFAULT gen_random_uuid()');

        // 4. Renombrar índices
        DB::statement('ALTER INDEX IF EXISTS idx_moldes_filtros   RENAME TO idx_plantillas_filtros');
        DB::statement('ALTER INDEX IF EXISTS idx_moldes_calidad   RENAME TO idx_plantillas_calidad');
        DB::statement('ALTER INDEX IF EXISTS idx_moldes_embedding RENAME TO idx_plantillas_embedding');

        // 5. Renombrar tabla de feedback y ajustar FK
        Schema::rename('memoriales_feedback', 'plantillas_feedback');
        DB::statement('ALTER TABLE plantillas_feedback RENAME COLUMN source_doc_id TO plantilla_id');
        DB::statement('ALTER TABLE plantillas_feedback ALTER COLUMN plantilla_id TYPE uuid USING NULL::uuid');
        DB::statement('ALTER TABLE plantillas_feedback ADD CONSTRAINT plantillas_feedback_plantilla_id_foreign
            FOREIGN KEY (plantilla_id) REFERENCES plantillas_memoriales(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE plantillas_feedback DROP CONSTRAINT IF EXISTS plantillas_feedback_plantilla_id_foreign');

        DB::statement('ALTER TABLE plantillas_feedback ALTER COLUMN plantilla_id TYPE varchar(255) USING NULL::varchar');
        DB::statement('ALTER TABLE plantillas_feedback RENAME COLUMN plantilla_id TO source_doc_id');
        Schema::rename('plantillas_feedback', 'memoriales_feedback');

        DB::statement('ALTER INDEX IF EXISTS idx_plantillas_filtros   RENAME TO idx_moldes_filtros');
        DB::statement('ALTER INDEX IF EXISTS idx_plantillas_calidad   RENAME TO idx_moldes_calidad');
        DB::statement('ALTER INDEX IF EXISTS idx_plantillas_embedding RENAME TO idx_moldes_embedding');

        DB::statement('ALTER TABLE plantillas_memoriales ALTER COLUMN id DROP DEFAULT');
        DB::statement('ALTER TABLE plantillas_memoriales ALTER COLUMN id TYPE varchar(255) USING id::text');
        DB::statement('ALTER TABLE plantillas_memoriales RENAME COLUMN id TO doc_id');
        Schema::rename('plantillas_memoriales', 'memoriales_moldes');

        DB::statement('ALTER TABLE memoriales_feedback ADD CONSTRAINT memoriales_feedback_source_doc_id_foreign
            FOREIGN KEY (source_doc_id) REFERENCES memoriales_moldes(doc_id) ON DELETE SET NULL');
    }
};
