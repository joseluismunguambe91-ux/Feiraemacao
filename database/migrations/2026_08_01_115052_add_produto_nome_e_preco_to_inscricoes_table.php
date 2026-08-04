<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Só preenchido quando tipo_atividade = 'gastronomia' — quem se
     * inscreve já indica o prato e o preço; a Comissão confirma/ajusta ao
     * aprovar, e é isso que vira o GastronomiaItem criado nesse momento.
     */
    public function up(): void
    {
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->string('produto_nome', 120)->nullable()->after('descricao');
            $table->decimal('produto_preco', 8, 2)->nullable()->after('produto_nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->dropColumn(['produto_nome', 'produto_preco']);
        });
    }
};
