<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricao_aluno', function (Blueprint $table) {
            $table->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();
            $table->primary(['inscricao_id', 'aluno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricao_aluno');
    }
};
