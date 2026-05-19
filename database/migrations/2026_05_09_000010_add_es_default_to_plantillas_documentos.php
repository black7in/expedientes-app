<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plantillas_documentos', function (Blueprint $table) {
            $table->boolean('es_default')->default(false)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('plantillas_documentos', function (Blueprint $table) {
            $table->dropColumn('es_default');
        });
    }
};
