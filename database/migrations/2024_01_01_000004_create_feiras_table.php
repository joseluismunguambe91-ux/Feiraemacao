<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * estado_ativo é uma coluna gerada (STORED) que só assume valor quando
     * estado ∈ {publicada, em_curso}; o índice UNIQUE sobre ela impede, ao
     * nível do motor de base de dados, que duas edições fiquem ativas ao
     * mesmo tempo (RN02 — ver docs/03-modelagem-base-dados.md, decisão 1.5).
     */
    public function up(): void
    {
        Schema::create('feiras', function (Blueprint $table) {
            $table->id();
            $table->string('tema', 150);
            $table->text('descricao')->nullable();
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->time('hora_abertura');
            $table->time('hora_encerramento');
            $table->string('local', 200);
            $table->string('banner_path')->nullable();
            $table->string('logotipo_path')->nullable();
            $table->string('regulamento_path')->nullable();
            $table->enum('estado', ['rascunho', 'publicada', 'em_curso', 'encerrada', 'arquivada'])
                ->default('rascunho');
            $table->string('estado_ativo', 10)
                ->storedAs("CASE WHEN estado IN ('publicada','em_curso') THEN 'ATIVA' ELSE NULL END")
                ->nullable()
                ->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feiras');
    }
};
