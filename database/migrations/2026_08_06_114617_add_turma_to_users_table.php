<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permite ao Administrador registar a turma diretamente na conta de
     * Aluno (Utilizadores), sem depender de um Professor criar antes um
     * registo em "Os meus alunos" — pedido pós-Etapa 10 para simplificar o
     * fluxo de inscrição. Só é usado quando o próprio Aluno se inscreve sem
     * ter um registo de Aluno ligado (User::alunoLigado continua a ter
     * prioridade quando existe, ver Professor\InscricaoController).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('turma', 50)->nullable()->after('telefone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('turma');
        });
    }
};
