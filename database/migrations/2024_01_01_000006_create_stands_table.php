<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feira_id')->constrained('feiras')->restrictOnDelete();
            $table->string('numero', 20);
            $table->string('localizacao', 150)->nullable();
            $table->smallInteger('capacidade')->unsigned()->nullable();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('categoria', 80)->nullable();
            $table->enum('estado', ['disponivel', 'reservado', 'ocupado', 'inativo'])->default('disponivel');
            $table->string('qr_token', 12)->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['feira_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stands');
    }
};
