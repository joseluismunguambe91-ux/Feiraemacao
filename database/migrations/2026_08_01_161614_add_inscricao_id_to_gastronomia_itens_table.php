<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mesmo padrão de atividades.inscricao_id/expositores.inscricao_id:
     * rastreia que este item nasceu da aprovação de uma inscrição de
     * gastronomia — permite chegar de um prato até à turma/aluno que o
     * propôs (via inscricao->alunos), sem repetir esses dados aqui.
     */
    public function up(): void
    {
        Schema::table('gastronomia_itens', function (Blueprint $table) {
            $table->foreignId('inscricao_id')->nullable()->unique()->after('feira_id')
                ->constrained('inscricoes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gastronomia_itens', function (Blueprint $table) {
            $table->dropConstrainedForeignId('inscricao_id');
        });
    }
};
