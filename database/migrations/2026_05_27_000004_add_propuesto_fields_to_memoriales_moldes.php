<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memoriales_moldes', function (Blueprint $table) {
            $table->string('tipo_memorial_propuesto', 80)->nullable()->after('tipo_memorial');
            $table->string('tipo_accion_propuesto', 80)->nullable()->after('tipo_accion');
        });
    }

    public function down(): void
    {
        Schema::table('memoriales_moldes', function (Blueprint $table) {
            $table->dropColumn(['tipo_memorial_propuesto', 'tipo_accion_propuesto']);
        });
    }
};
