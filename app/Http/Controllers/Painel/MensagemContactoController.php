<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\MensagemContacto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MensagemContactoController extends Controller
{
    public function index(Request $request): View
    {
        $feira = $request->attributes->get('feiraAtual');
        $mensagens = $feira
            ? MensagemContacto::where('feira_id', $feira->id)->orderByDesc('created_at')->paginate(15)
            : null;

        return view('painel.mensagens-contacto.index', compact('mensagens', 'feira'));
    }

    public function marcarLida(MensagemContacto $mensagem): RedirectResponse
    {
        $mensagem->update(['lida' => true]);

        return back()->with('sucesso', 'Mensagem marcada como lida.');
    }
}
