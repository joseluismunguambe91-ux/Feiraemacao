<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Painel\StandRequest;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StandController extends Controller
{
    public function index(Request $request): View
    {
        $feira = $request->attributes->get('feiraAtual');
        $stands = $feira
            ? $feira->stands()->with(['responsavel', 'expositor'])->orderBy('numero')->paginate(15)
            : null;

        return view('painel.stands.index', compact('stands', 'feira'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->attributes->get('feiraAtual')) {
            return redirect()->route('painel.dashboard')->with('erro', 'Seleciona ou cria uma edição da feira primeiro.');
        }

        return view('painel.stands.form', [
            'stand' => new Stand(),
            'responsaveis' => User::orderBy('name')->get(),
        ]);
    }

    public function store(StandRequest $request): RedirectResponse
    {
        $feira = $request->attributes->get('feiraAtual');
        $this->assegurarFeiraEditavel($feira);

        $dados = $request->validated();
        $dados['feira_id'] = $feira->id;
        $dados['estado'] = $dados['estado'] ?? 'disponivel';
        $dados['qr_token'] = Str::random(12);

        Stand::create($dados);

        return redirect()->route('painel.stands.index')->with('sucesso', 'Stand criado.');
    }

    public function edit(Stand $stand): View
    {
        return view('painel.stands.form', [
            'stand' => $stand,
            'responsaveis' => User::orderBy('name')->get(),
        ]);
    }

    public function update(StandRequest $request, Stand $stand): RedirectResponse
    {
        $this->assegurarFeiraEditavel($stand->feira);

        $stand->update($request->validated());

        return redirect()->route('painel.stands.index')->with('sucesso', 'Stand atualizado.');
    }

    public function destroy(Stand $stand): RedirectResponse
    {
        $this->assegurarFeiraEditavel($stand->feira);

        $stand->delete();

        return redirect()->route('painel.stands.index')->with('sucesso', 'Stand eliminado.');
    }

    public function qr(Stand $stand): View
    {
        return view('painel.stands.qr', [
            'stand' => $stand,
            'urlPublica' => url('/stand/'.$stand->qr_token),
        ]);
    }
}
