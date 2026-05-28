<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_propuestas', function (Blueprint $table) {
            $table->id();
            $table->string('campo', 30);           // tipo_memorial | tipo_accion
            $table->string('valor_propuesto', 80);
            $table->integer('frecuencia')->default(1);
            $table->string('estado', 20)->default('pendiente'); // pendiente | aprobada | rechazada
            $table->timestampTz('fecha_creacion')->useCurrent();
            $table->timestampTz('fecha_decision')->nullable();
            $table->string('decidido_por', 100)->nullable();
            $table->unique(['campo', 'valor_propuesto']);
        });

        DB::statement('ALTER TABLE catalogo_propuestas ADD COLUMN doc_ids TEXT[] NOT NULL DEFAULT \'{}\'');
        DB::statement('CREATE INDEX idx_propuestas_estado ON catalogo_propuestas (estado, frecuencia DESC)');
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_propuestas');
    }
};
