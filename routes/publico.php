<?php

use App\Http\Controllers\PublicoController;
use Illuminate\Support\Facades\Route;

// RF28/RF29 (Etapa 1): página pública sem login, sempre a refletir a edição
// publicada/em_curso (RN10) — nunca uma edição em rascunho.
Route::name('publico.')->group(function () {
    Route::get('/', [PublicoController::class, 'inicio'])->name('inicio');
    Route::get('sobre', [PublicoController::class, 'sobre'])->name('sobre');
    Route::get('programacao', [PublicoController::class, 'programacao'])->name('programacao');
    Route::get('atividades', [PublicoController::class, 'atividades'])->name('atividades');
    Route::get('gastronomia', [PublicoController::class, 'gastronomia'])->name('gastronomia');
    Route::get('expositores', [PublicoController::class, 'expositores'])->name('expositores');
    Route::get('mapa', [PublicoController::class, 'mapa'])->name('mapa');
    Route::get('stand/{qrToken}', [PublicoController::class, 'stand'])->name('stand');
    Route::get('galeria', [PublicoController::class, 'galeria'])->name('galeria');
    Route::get('patrocinadores', [PublicoController::class, 'patrocinadores'])->name('patrocinadores');
    Route::get('pesquisa', [PublicoController::class, 'pesquisa'])->name('pesquisa');
    Route::get('contacto', [PublicoController::class, 'contacto'])->name('contacto');
    Route::post('contacto', [PublicoController::class, 'contactoStore'])
        ->middleware('throttle:5,1')
        ->name('contacto.store');
});
