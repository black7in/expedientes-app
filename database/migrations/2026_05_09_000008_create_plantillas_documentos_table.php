<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantillas_documentos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nombre', 100);
            $table->string('tipo_documento', 50);
            $table->string('materia', 50)->default('civil');
            $table->text('contenido');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantillas_documentos');
    }
};
