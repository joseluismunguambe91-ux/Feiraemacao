<?php

use App\Http\Controllers\Painel\AtividadeController;
use App\Http\Controllers\Painel\DashboardController;
use App\Http\Controllers\Painel\ExpositorController;
use App\Http\Controllers\Painel\FeiraContextoController;
use App\Http\Controllers\Painel\FeiraEstadoController;
use App\Http\Controllers\Painel\GaleriaItemController;
use App\Http\Controllers\Painel\GastronomiaItemController;
use App\Http\Controllers\Painel\InscricaoAprovacaoController;
use App\Http\Controllers\Painel\InscricaoController;
use App\Http\Controllers\Painel\MensagemContactoController;
use App\Http\Controllers\Painel\PatrocinadorController;
use App\Http\Controllers\Painel\ProgramacaoController;
use App\Http\Controllers\Painel\RelatorioController;
use App\Http\Controllers\Painel\StandController;
use Illuminate\Support\Facades\Route;

// Área partilhada Administrador + Comissão (Etapa 5, secção 0 — evita
// duplicar controllers/views entre /admin e /organizador para os mesmos
// recursos).
Route::middleware(['auth', 'role:administrador,comissao', 'feira.contexto'])
    ->prefix('painel')
    ->name('painel.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('trocar-feira', [FeiraContextoController::class, 'index'])->name('trocar-feira');
        Route::post('trocar-feira', [FeiraContextoController::class, 'store'])->name('trocar-feira.store');

        Route::post('feiras/{feira}/avancar-estado', [FeiraEstadoController::class, 'avancar'])
            ->name('feiras.avancar-estado');

        Route::resource('expositores', ExpositorController::class)
            ->except(['show'])
            ->parameters(['expositores' => 'expositor']);

        Route::resource('stands', StandController::class)
            ->except(['show'])
            ->parameters(['stands' => 'stand']);
        Route::get('stands/{stand}/qr', [StandController::class, 'qr'])->name('stands.qr');

        Route::resource('atividades', AtividadeController::class)
            ->except(['show'])
            ->parameters(['atividades' => 'atividade']);

        Route::resource('gastronomia', GastronomiaItemController::class)
            ->except(['show'])
            ->parameters(['gastronomia' => 'item']);

        Route::get('inscricoes', [InscricaoController::class, 'index'])->name('inscricoes.index');
        Route::get('inscricoes/{inscricao}', [InscricaoController::class, 'show'])->name('inscricoes.show');
        Route::post('inscricoes/{inscricao}/aprovar', [InscricaoAprovacaoController::class, 'aprovar'])->name('inscricoes.aprovar');
        Route::post('inscricoes/{inscricao}/rejeitar', [InscricaoAprovacaoController::class, 'rejeitar'])->name('inscricoes.rejeitar');

        Route::get('programacao', [ProgramacaoController::class, 'index'])->name('programacao.index');
        Route::post('programacao/verificar-conflito', [ProgramacaoController::class, 'verificarConflito'])->name('programacao.verificar-conflito');
        Route::get('programacao/agendar/{atividade}', [ProgramacaoController::class, 'create'])->name('programacao.create');
        Route::post('programacao/agendar/{atividade}', [ProgramacaoController::class, 'store'])->name('programacao.store');
        Route::get('programacao/{item}/editar', [ProgramacaoController::class, 'edit'])->name('programacao.edit');
        Route::put('programacao/{item}', [ProgramacaoController::class, 'update'])->name('programacao.update');

        Route::resource('galeria', GaleriaItemController::class)
            ->except(['show'])
            ->parameters(['galeria' => 'item']);

        Route::resource('patrocinadores', PatrocinadorController::class)
            ->except(['show'])
            ->parameters(['patrocinadores' => 'patrocinador']);

        Route::get('mensagens-contacto', [MensagemContactoController::class, 'index'])->name('mensagens-contacto.index');
        Route::post('mensagens-contacto/{mensagem}/marcar-lida', [MensagemContactoController::class, 'marcarLida'])
            ->name('mensagens-contacto.marcar-lida');

        Route::get('relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
        Route::post('relatorios', [RelatorioController::class, 'store'])->name('relatorios.store');
        Route::get('relatorios/{relatorio}/download', [RelatorioController::class, 'download'])->name('relatorios.download');
    });
