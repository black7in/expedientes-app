<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('doc_chunks');
    }

    public function down(): void
    {
        // No se restaura — la indexación por chunks fue reemplazada por memoriales_moldes
    }
};
