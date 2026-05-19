<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurisprudencia_chunks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('numero_auto', 50);
            $table->date('fecha_resolucion')->nullable();
            $table->string('materia', 50);
            $table->string('sala', 100)->nullable();
            $table->text('chunk_texto');
            $table->json('metadata')->default('{}');
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement('ALTER TABLE jurisprudencia_chunks ADD COLUMN embedding vector(1024)');

        DB::statement('CREATE INDEX idx_jurisprudencia_embedding ON jurisprudencia_chunks USING hnsw (embedding vector_cosine_ops)');
        DB::statement('CREATE INDEX idx_jurisprudencia_materia ON jurisprudencia_chunks (materia)');
        DB::statement('CREATE INDEX idx_jurisprudencia_auto ON jurisprudencia_chunks (numero_auto)');
    }

    public function down(): void
    {
        Schema::dropIfExists('jurisprudencia_chunks');
    }
};
