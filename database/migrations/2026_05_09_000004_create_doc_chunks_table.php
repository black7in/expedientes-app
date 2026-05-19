<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc_chunks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignUuid('documento_id')->nullable()->constrained('documentos')->cascadeOnDelete();
            $table->foreignUuid('expediente_id')->nullable()->constrained('expedientes')->nullOnDelete();
            $table->string('tipo_doc', 50)->nullable();
            $table->text('chunk_texto');
            $table->integer('chunk_index');
            $table->json('metadata')->default('{}');
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement('ALTER TABLE doc_chunks ADD COLUMN embedding vector(1024)');

        DB::statement('CREATE INDEX idx_doc_chunks_embedding ON doc_chunks USING hnsw (embedding vector_cosine_ops)');
        DB::statement('CREATE INDEX idx_doc_chunks_tipo ON doc_chunks (tipo_doc)');
        DB::statement('CREATE INDEX idx_doc_chunks_expediente ON doc_chunks (expediente_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('doc_chunks');
    }
};
