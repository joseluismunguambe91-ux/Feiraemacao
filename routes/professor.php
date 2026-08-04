<?php

use App\Http\Controllers\Professor\AlunoController;
use App\Http\Controllers\Professor\InscricaoController;
use Illuminate\Support\Facades\Route;

// Partilhada por Professor e Aluno: ambos submetem e acompanham as suas
// próprias inscrições da mesma forma (decisão revista após a Etapa 10 —
// Etapa 1 previa originalmente só o Professor, ver docs/10-documentacao.md).
Route::middleware(['auth', 'role:professor,aluno'])
    ->prefix('professor')
    ->name('professor.')
    ->group(function () {
        Route::resource('inscricoes', InscricaoController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->parameters(['inscricoes' => 'inscricao']);

        // Só o Professor gere o seu próprio plantel de Alunos (RF04) — o
        // Aluno nunca regista colegas, só é escolhido a partir daqui.
        Route::middleware('role:professor')->group(function () {
            Route::resource('alunos', AlunoController::class)
                ->except(['show'])
                ->parameters(['alunos' => 'aluno']);
        });
    });
