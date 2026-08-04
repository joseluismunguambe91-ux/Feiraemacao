<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeria_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feira_id')->constrained('feiras')->restrictOnDelete();
            $table->enum('tipo', ['foto', 'video']);
            $table->string('categoria', 80)->nullable();
            $table->string('titulo', 150)->nullable();
            $table->string('path_ou_url');
            $table->smallInteger('ordem')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['feira_id', 'categoria', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galeria_itens');
    }
};
