<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\FeiraController;
use App\Http\Controllers\Admin\FeiraEstadoController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Área exclusiva do Administrador (Etapa 4/5) — utilizadores, papéis, dados
// institucionais da feira, auditoria, configurações.
Route::middleware(['auth', 'role:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('feiras', FeiraController::class)->except(['show']);
        Route::post('feiras/{feira}/reverter-estado', [FeiraEstadoController::class, 'reverter'])
            ->name('feiras.reverter-estado');

        Route::resource('utilizadores', UserController::class)
            ->except(['show'])
            ->parameters(['utilizadores' => 'utilizador']);

        Route::get('auditoria', [AuditLogController::class, 'index'])->name('auditoria.index');
    });
