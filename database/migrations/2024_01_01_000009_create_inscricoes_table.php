<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feira_id')->constrained('feiras')->restrictOnDelete();
            $table->foreignId('professor_id')->constrained('users')->restrictOnDelete();
            $table->enum('tipo_participante', ['professor', 'aluno']);
            $table->string('turma', 50)->nullable();
            $table->string('telefone', 30);
            $table->string('email', 190);
            $table->enum('tipo_atividade', [
                'teatro', 'danca', 'musica', 'poesia', 'ciencias', 'artesanato', 'pintura', 'jogos', 'outro',
            ]);
            $table->text('descricao')->nullable();
            $table->smallInteger('numero_participantes')->unsigned();
            $table->boolean('necessita_palco')->default(false);
            $table->boolean('necessita_eletricidade')->default(false);
            $table->boolean('necessita_projetor')->default(false);
            $table->boolean('necessita_som')->default(false);
            $table->smallInteger('numero_mesas')->unsigned()->default(0);
            $table->smallInteger('numero_cadeiras')->unsigned()->default(0);
            $table->time('horario_pretendido')->nullable();
            $table->smallInteger('duracao_minutos')->unsigned()->nullable();
            $table->text('observacoes')->nullable();
            $table->enum('estado', ['pendente', 'aprovada', 'rejeitada'])->default('pendente');
            $table->text('comentario_avaliacao')->nullable();
            $table->foreignId('avaliado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('avaliado_em')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['feira_id', 'estado']);
            $table->index('professor_id');
        });

        // RN — número de participantes tem de ser pelo menos 1 (secção 3.12 do dicionário de dados).
        // ALTER TABLE ... ADD CONSTRAINT é sintaxe MySQL — o SQLite usado nos
        // testes (Etapa 9) não a suporta; a regra já é validada em
        // Professor\InscricaoRequest, isto é só a camada extra de defesa em
        // produção (MySQL).
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE inscricoes ADD CONSTRAINT chk_inscricoes_participantes CHECK (numero_participantes >= 1)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricoes');
    }
};
