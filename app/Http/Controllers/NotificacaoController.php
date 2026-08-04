<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificacaoController extends Controller
{
    public function marcarLida(Request $request, string $id): RedirectResponse
    {
        $notificacao = $request->user()->notifications()->findOrFail($id);
        $notificacao->markAsRead();

        return redirect($notificacao->data['url'] ?? url()->previous());
    }
}
