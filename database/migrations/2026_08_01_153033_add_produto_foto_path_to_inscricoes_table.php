<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Foto do próprio prato (opcional), distinta das fotos de apoio gerais
     * já cobertas por inscricao_fotos — esta é a que vira o foto_path do
     * GastronomiaItem criado na aprovação, para já aparecer na página
     * pública e dar aos visitantes uma ideia do que vão encontrar.
     */
    public function up(): void
    {
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->string('produto_foto_path')->nullable()->after('produto_preco');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->dropColumn('produto_foto_path');
        });
    }
};
