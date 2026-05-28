<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memoriales_feedback', function (Blueprint $table) {
            $table->id();
            $table->string('source_doc_id')->nullable();
            $table->foreign('source_doc_id')
                ->references('doc_id')
                ->on('memoriales_moldes')
                ->nullOnDelete();
            $table->smallInteger('rating')->nullable();
            $table->boolean('aceptado')->nullable();
            $table->text('comentario')->nullable();
            $table->timestampTz('fecha')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memoriales_feedback');
    }
};
