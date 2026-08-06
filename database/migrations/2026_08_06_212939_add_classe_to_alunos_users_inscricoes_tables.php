<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "classe" (ex.: 9ª, 10ª) é distinta de "turma" (ex.: C, A) — o
     * relatório de participantes pediu as duas separadas, não só o
     * combinado livre que já existia (ex.: "9C"). Mesmo padrão em três
     * sítios, para acompanhar onde "turma" já vivia.
     */
    public function up(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->string('classe', 20)->nullable()->after('nome');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('classe', 20)->nullable()->after('turma');
        });

        Schema::table('inscricoes', function (Blueprint $table) {
            $table->string('classe', 20)->nullable()->after('turma');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->dropColumn('classe');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('classe');
        });

        Schema::table('inscricoes', function (Blueprint $table) {
            $table->dropColumn('classe');
        });
    }
};
