<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('relatorios_gerados', function (Blueprint $table) {
            $table->enum('tipo', ['participantes', 'atividades', 'expositores', 'gastronomia', 'programacao', 'visitantes'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('relatorios_gerados', function (Blueprint $table) {
            $table->enum('tipo', ['participantes', 'atividades', 'expositores', 'gastronomia', 'programacao'])->change();
        });
    }
};
