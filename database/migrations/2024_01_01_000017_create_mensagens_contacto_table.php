<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensagens_contacto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feira_id')->nullable()->constrained('feiras')->nullOnDelete();
            $table->string('nome', 150);
            $table->string('email', 190);
            $table->string('assunto', 150)->nullable();
            $table->text('mensagem');
            $table->boolean('lida')->default(false);
            $table->timestamps();

            $table->index('feira_id');
            $table->index('lida');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensagens_contacto');
    }
};
