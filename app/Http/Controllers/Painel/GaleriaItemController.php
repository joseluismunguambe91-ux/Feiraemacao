<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Painel\GaleriaItemRequest;
use App\Models\GaleriaItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GaleriaItemController extends Controller
{
    public function index(Request $request): View
    {
        $feira = $request->attributes->get('feiraAtual');
        $itens = $feira ? $feira->galeriaItens()->orderBy('categoria')->orderBy('ordem')->paginate(15) : null;

        return view('painel.galeria.index', compact('itens', 'feira'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->attributes->get('feiraAtual')) {
            return redirect()->route('painel.dashboard')->with('erro', 'Seleciona ou cria uma edição da feira primeiro.');
        }

        return view('painel.galeria.form', ['item' => new GaleriaItem()]);
    }

    public function store(GaleriaItemRequest $request): RedirectResponse
    {
        $feira = $request->attributes->get('feiraAtual');
        $this->assegurarFeiraEditavel($feira);

        $dados = $request->safe()->only(['tipo', 'categoria', 'titulo', 'ordem']);
        $dados['feira_id'] = $feira->id;
        $dados['path_ou_url'] = $dados['tipo'] === 'foto'
            ? $request->file('foto')->store('galeria', 'public')
            : $request->validated('url_video');

        GaleriaItem::create($dados);

        return redirect()->route('painel.galeria.index')->with('sucesso', 'Item adicionado à galeria.');
    }

    public function edit(GaleriaItem $item): View
    {
        return view('painel.galeria.form', compact('item'));
    }

    public function update(GaleriaItemRequest $request, GaleriaItem $item): RedirectResponse
    {
        $this->assegurarFeiraEditavel($item->feira);

        $dados = $request->safe()->only(['tipo', 'categoria', 'titulo', 'ordem']);

        if ($dados['tipo'] === 'foto' && $request->hasFile('foto')) {
            $dados['path_ou_url'] = $request->file('foto')->store('galeria', 'public');
        } elseif ($dados['tipo'] === 'video') {
            $dados['path_ou_url'] = $request->validated('url_video');
        }

        $item->update($dados);

        return redirect()->route('painel.galeria.index')->with('sucesso', 'Item atualizado.');
    }

    public function destroy(GaleriaItem $item): RedirectResponse
    {
        $this->assegurarFeiraEditavel($item->feira);

        $item->delete();

        return redirect()->route('painel.galeria.index')->with('sucesso', 'Item eliminado.');
    }
}
