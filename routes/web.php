<?php

use App\Http\Controllers\NotificacaoController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/publico.php';
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/painel.php';
require __DIR__.'/professor.php';

// Partilhada por qualquer papel autenticado (Administrador, Comissão,
// Professor) — o sino de notificações vive em ambos os layouts.
Route::middleware('auth')
    ->post('notificacoes/{id}/marcar-lida', [NotificacaoController::class, 'marcarLida'])
    ->name('notificacoes.marcar-lida');
