<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mesmo padrão de atividades.inscricao_id: rastreia que um Expositor
     * nasceu da aprovação de uma inscrição de gastronomia, em vez de ter
     * sido criado diretamente pela Comissão no Painel (nullable — os
     * expositores criados diretamente continuam sem inscrição de origem).
     */
    public function up(): void
    {
        Schema::table('expositores', function (Blueprint $table) {
            $table->foreignId('inscricao_id')->nullable()->unique()->after('feira_id')
                ->constrained('inscricoes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expositores', function (Blueprint $table) {
            $table->dropConstrainedForeignId('inscricao_id');
        });
    }
};
