<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('expediente_id')->constrained('expedientes')->onDelete('cascade');
            $table->foreignUuid('persona_id')->constrained('personas');
            $table->enum('rol_procesal', ['demandante', 'demandado']);
            $table->boolean('es_cliente')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partes');
    }
};
