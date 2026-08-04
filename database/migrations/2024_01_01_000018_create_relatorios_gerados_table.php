<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relatorios_gerados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feira_id')->constrained('feiras')->restrictOnDelete();
            $table->enum('tipo', ['participantes', 'atividades', 'expositores', 'gastronomia', 'programacao']);
            $table->enum('formato', ['pdf', 'excel']);
            $table->json('filtros')->nullable();
            $table->string('path_ficheiro')->nullable();
            $table->foreignId('gerado_por')->constrained('users')->restrictOnDelete();
            $table->enum('estado', ['processando', 'concluido', 'falhou'])->default('processando');
            $table->timestamps();

            $table->index(['feira_id', 'tipo']);
            $table->index('gerado_por');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relatorios_gerados');
    }
};
