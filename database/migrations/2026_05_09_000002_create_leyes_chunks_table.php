<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leyes_chunks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ley', 100);
            $table->string('numero_articulo', 20);
            $table->text('titulo_articulo')->nullable();
            $table->string('materia', 50);
            $table->text('chunk_texto');
            $table->json('metadata')->default('{}');
            $table->timestamp('created_at')->useCurrent();
        });

        // Columna vector (1024 dimensiones — multilingual-e5-large)
        DB::statement('ALTER TABLE leyes_chunks ADD COLUMN embedding vector(1024)');

        // HNSW: funciona con tabla vacía y no requiere tuning de lists
        DB::statement('CREATE INDEX idx_leyes_embedding ON leyes_chunks USING hnsw (embedding vector_cosine_ops)');
        DB::statement('CREATE INDEX idx_leyes_materia ON leyes_chunks (materia)');
        DB::statement('CREATE INDEX idx_leyes_ley ON leyes_chunks (ley)');
        DB::statement('CREATE INDEX idx_leyes_articulo ON leyes_chunks (numero_articulo)');
    }

    public function down(): void
    {
        Schema::dropIfExists('leyes_chunks');
    }
};
