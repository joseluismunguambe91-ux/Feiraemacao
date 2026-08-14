<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Alunos que se registam sozinhos (pedido pós-Etapa 10) podem não ter
     * turma nenhuma associada — sem isto, aprovar uma inscrição de
     * gastronomia desse aluno rebentava ao criar o Expositor (coluna
     * NOT NULL desde a Etapa 3, quando só o Administrador criava contas
     * com turma sempre definida).
     */
    public function up(): void
    {
        Schema::table('expositores', function (Blueprint $table) {
            $table->string('turma', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expositores', function (Blueprint $table) {
            $table->string('turma', 50)->nullable(false)->change();
        });
    }
};
