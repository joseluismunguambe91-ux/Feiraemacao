<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feira_id')->constrained('feiras')->restrictOnDelete();
            $table->foreignId('inscricao_id')->nullable()->unique()->constrained('inscricoes')->nullOnDelete();
            $table->enum('tipo', [
                'teatro', 'danca', 'musica', 'poesia', 'ciencias', 'artesanato', 'pintura', 'jogos', 'outro',
            ]);
            $table->string('titulo', 150);
            $table->text('descricao')->nullable();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->smallInteger('participantes_previstos')->unsigned()->nullable();
            $table->string('foto_path')->nullable();
            $table->enum('estado', ['planeada', 'confirmada', 'cancelada'])->default('planeada');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['feira_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atividades');
    }
};
