<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrocinadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feira_id')->constrained('feiras')->restrictOnDelete();
            $table->string('nome', 120);
            $table->string('logotipo_path');
            $table->string('url_site')->nullable();
            $table->string('nivel', 40)->nullable();
            $table->smallInteger('ordem')->unsigned()->default(0);
            $table->timestamps();

            $table->index('feira_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrocinadores');
    }
};
