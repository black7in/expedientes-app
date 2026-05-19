<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_exportados', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('borrador_id')->nullable()->constrained('borradores')->nullOnDelete();
            $table->foreignUuid('expediente_id')->nullable()->constrained('expedientes')->nullOnDelete();
            $table->foreignUuid('exportado_por')->constrained('users');
            $table->enum('formato', ['pdf', 'docx']);
            $table->text('ruta_archivo');
            $table->string('nombre_archivo', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_exportados');
    }
};
