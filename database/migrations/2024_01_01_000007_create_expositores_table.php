<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expositores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feira_id')->constrained('feiras')->restrictOnDelete();
            $table->foreignId('professor_id')->constrained('users')->restrictOnDelete();
            $table->string('turma', 50);
            $table->string('categoria', 80)->nullable();
            $table->text('descricao')->nullable();
            $table->foreignId('stand_id')->nullable()->unique()->constrained('stands')->nullOnDelete();
            $table->enum('estado', ['pendente', 'ativo', 'inativo'])->default('pendente');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['feira_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expositores');
    }
};
