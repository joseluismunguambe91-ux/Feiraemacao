<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastronomia_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feira_id')->constrained('feiras')->restrictOnDelete();
            $table->string('nome', 120);
            $table->string('categoria', 80)->nullable();
            $table->text('descricao')->nullable();
            $table->decimal('preco', 8, 2);
            $table->string('foto_path')->nullable();
            $table->text('ingredientes')->nullable();
            $table->boolean('disponivel')->default(true);
            $table->integer('quantidade_disponivel')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['feira_id', 'categoria']);
        });

        // RN — preço nunca pode ser negativo (secção 3.11 do dicionário de dados).
        // Ver nota na migration de inscricoes: sintaxe MySQL, saltada no SQLite dos testes.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE gastronomia_itens ADD CONSTRAINT chk_gastronomia_preco CHECK (preco >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gastronomia_itens');
    }
};
