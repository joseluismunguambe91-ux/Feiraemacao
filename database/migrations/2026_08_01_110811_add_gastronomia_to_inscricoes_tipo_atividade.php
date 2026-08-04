<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "gastronomia" passa a ser uma opção de tipo_atividade tal como
     * "danca"/"teatro" — quem se inscreve escolhe se quer apresentar uma
     * atividade agendada ou expor um produto gastronómico numa banca; a
     * aprovação (InscricaoAprovacaoService) é que ramifica o resultado
     * (Atividade+Programação vs. Expositor+Stand) consoante este valor.
     */
    public function up(): void
    {
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->enum('tipo_atividade', [
                'teatro', 'danca', 'musica', 'poesia', 'ciencias', 'artesanato', 'pintura', 'jogos', 'gastronomia', 'outro',
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->enum('tipo_atividade', [
                'teatro', 'danca', 'musica', 'poesia', 'ciencias', 'artesanato', 'pintura', 'jogos', 'outro',
            ])->change();
        });
    }
};
