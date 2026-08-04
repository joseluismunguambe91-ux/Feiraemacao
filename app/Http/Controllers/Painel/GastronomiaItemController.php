<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Painel\GastronomiaItemRequest;
use App\Models\GastronomiaItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GastronomiaItemController extends Controller
{
    public function index(Request $request): View
    {
        $feira = $request->attributes->get('feiraAtual');
        $itens = $feira ? $feira->gastronomiaItens()->with('inscricao.alunos')->orderBy('nome')->paginate(15) : null;

        return view('painel.gastronomia.index', compact('itens', 'feira'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->attributes->get('feiraAtual')) {
            return redirect()->route('painel.dashboard')->with('erro', 'Seleciona ou cria uma edição da feira primeiro.');
        }

        return view('painel.gastronomia.form', ['item' => new GastronomiaItem()]);
    }

    public function store(GastronomiaItemRequest $request): RedirectResponse
    {
        $feira = $request->attributes->get('feiraAtual');
        $this->assegurarFeiraEditavel($feira);

        $dados = $request->validated();
        $dados['feira_id'] = $feira->id;
        $dados['disponivel'] = $request->boolean('disponivel', true);
        $dados['foto_path'] = $request->file('foto')?->store('gastronomia', 'public');

        GastronomiaItem::create($dados);

        return redirect()->route('painel.gastronomia.index')->with('sucesso', 'Item de gastronomia criado.');
    }

    public function edit(GastronomiaItem $item): View
    {
        return view('painel.gastronomia.form', compact('item'));
    }

    public function update(GastronomiaItemRequest $request, GastronomiaItem $item): RedirectResponse
    {
        $this->assegurarFeiraEditavel($item->feira);

        $dados = $request->validated();
        $dados['disponivel'] = $request->boolean('disponivel', true);

        if ($request->hasFile('foto')) {
            $dados['foto_path'] = $request->file('foto')->store('gastronomia', 'public');
        }

        $item->update($dados);

        return redirect()->route('painel.gastronomia.index')->with('sucesso', 'Item atualizado.');
    }

    public function destroy(GastronomiaItem $item): RedirectResponse
    {
        $this->assegurarFeiraEditavel($item->feira);

        $item->delete();

        return redirect()->route('painel.gastronomia.index')->with('sucesso', 'Item eliminado.');
    }
}
