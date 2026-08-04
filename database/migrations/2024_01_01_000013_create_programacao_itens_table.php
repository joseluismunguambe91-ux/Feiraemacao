<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programacao_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feira_id')->constrained('feiras')->restrictOnDelete();
            $table->foreignId('atividade_id')->constrained('atividades')->cascadeOnDelete();
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->string('local', 150);
            $table->string('palco', 80)->nullable();
            $table->timestamps();

            $table->index(['feira_id', 'data', 'palco']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programacao_itens');
    }
};
