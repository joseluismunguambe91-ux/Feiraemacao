<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expositor_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expositor_id')->constrained('expositores')->cascadeOnDelete();
            $table->string('path');
            $table->smallInteger('ordem')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expositor_fotos');
    }
};
