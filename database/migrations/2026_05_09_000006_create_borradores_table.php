<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borradores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('generacion_id')->constrained('generaciones')->cascadeOnDelete();
            $table->foreignUuid('expediente_id')->nullable()->constrained('expedientes')->nullOnDelete();
            $table->foreignUuid('editado_por')->constrained('users');
            $table->string('tipo_documento', 50);
            $table->text('contenido_html');
            $table->text('contenido_texto')->nullable();
            $table->enum('estado', ['borrador', 'aprobado', 'exportado'])->default('borrador');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borradores');
    }
};
