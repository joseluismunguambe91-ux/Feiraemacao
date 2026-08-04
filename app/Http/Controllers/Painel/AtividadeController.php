<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Painel\AtividadeRequest;
use App\Models\Atividade;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AtividadeController extends Controller
{
    public function index(Request $request): View
    {
        $feira = $request->attributes->get('feiraAtual');
        $atividades = $feira ? $feira->atividades()->orderBy('titulo')->paginate(15) : null;

        return view('painel.atividades.index', compact('atividades', 'feira'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->attributes->get('feiraAtual')) {
            return redirect()->route('painel.dashboard')->with('erro', 'Seleciona ou cria uma edição da feira primeiro.');
        }

        return view('painel.atividades.form', [
            'atividade' => new Atividade(),
            'responsaveis' => User::orderBy('name')->get(),
        ]);
    }

    public function store(AtividadeRequest $request): RedirectResponse
    {
        $feira = $request->attributes->get('feiraAtual');
        $this->assegurarFeiraEditavel($feira);

        $dados = $request->validated();
        $dados['feira_id'] = $feira->id;
        $dados['estado'] = $dados['estado'] ?? 'planeada';
        $dados['foto_path'] = $request->file('foto')?->store('atividades', 'public');

        Atividade::create($dados);

        return redirect()->route('painel.atividades.index')->with('sucesso', 'Atividade criada.');
    }

    public function edit(Atividade $atividade): View
    {
        return view('painel.atividades.form', [
            'atividade' => $atividade,
            'responsaveis' => User::orderBy('name')->get(),
        ]);
    }

    public function update(AtividadeRequest $request, Atividade $atividade): RedirectResponse
    {
        $this->assegurarFeiraEditavel($atividade->feira);

        $dados = $request->validated();

        if ($request->hasFile('foto')) {
            $dados['foto_path'] = $request->file('foto')->store('atividades', 'public');
        }

        $atividade->update($dados);

        return redirect()->route('painel.atividades.index')->with('sucesso', 'Atividade atualizada.');
    }

    public function destroy(Atividade $atividade): RedirectResponse
    {
        $this->assegurarFeiraEditavel($atividade->feira);

        $atividade->delete();

        return redirect()->route('painel.atividades.index')->with('sucesso', 'Atividade eliminada.');
    }
}
