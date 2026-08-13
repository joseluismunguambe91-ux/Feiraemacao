<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredAlunoController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Registo público, só para Alunos (RF03 continua a valer para os
    // restantes papéis — só o Administrador os cria, em Utilizadores).
    Route::get('registar', [RegisteredAlunoController::class, 'create'])->name('registar');
    Route::post('registar', [RegisteredAlunoController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('registar.store');

    Route::get('esqueci-senha', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('esqueci-senha', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:3,60')
        ->name('password.email');

    Route::get('redefinir-senha/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('redefinir-senha', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
