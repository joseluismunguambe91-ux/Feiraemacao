<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Uma linha por sessão/dia (não por página vista) — conta "visitantes",
     * não "pedidos". `feira_id` fica nulo quando não há edição ativa no
     * momento da visita (a página pública continua a responder mesmo sem
     * feira — RN10, Etapa 3).
     */
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feira_id')->nullable()->constrained('feiras')->nullOnDelete();
            $table->string('sessao_id', 80);
            $table->date('data');
            $table->timestamps();

            $table->unique(['sessao_id', 'data']);
            $table->index(['feira_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
