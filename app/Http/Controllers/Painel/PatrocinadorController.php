<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Painel\PatrocinadorRequest;
use App\Models\Patrocinador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatrocinadorController extends Controller
{
    public function index(Request $request): View
    {
        $feira = $request->attributes->get('feiraAtual');
        $patrocinadores = $feira ? $feira->patrocinadores()->orderBy('ordem')->paginate(15) : null;

        return view('painel.patrocinadores.index', compact('patrocinadores', 'feira'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->attributes->get('feiraAtual')) {
            return redirect()->route('painel.dashboard')->with('erro', 'Seleciona ou cria uma edição da feira primeiro.');
        }

        return view('painel.patrocinadores.form', ['patrocinador' => new Patrocinador()]);
    }

    public function store(PatrocinadorRequest $request): RedirectResponse
    {
        $feira = $request->attributes->get('feiraAtual');
        $this->assegurarFeiraEditavel($feira);

        $dados = $request->safe()->only(['nome', 'url_site', 'nivel', 'ordem']);
        $dados['feira_id'] = $feira->id;
        $dados['logotipo_path'] = $request->file('logotipo')->store('patrocinadores', 'public');

        Patrocinador::create($dados);

        return redirect()->route('painel.patrocinadores.index')->with('sucesso', 'Patrocinador criado.');
    }

    public function edit(Patrocinador $patrocinador): View
    {
        return view('painel.patrocinadores.form', compact('patrocinador'));
    }

    public function update(PatrocinadorRequest $request, Patrocinador $patrocinador): RedirectResponse
    {
        $this->assegurarFeiraEditavel($patrocinador->feira);

        $dados = $request->safe()->only(['nome', 'url_site', 'nivel', 'ordem']);

        if ($request->hasFile('logotipo')) {
            $dados['logotipo_path'] = $request->file('logotipo')->store('patrocinadores', 'public');
        }

        $patrocinador->update($dados);

        return redirect()->route('painel.patrocinadores.index')->with('sucesso', 'Patrocinador atualizado.');
    }

    public function destroy(Patrocinador $patrocinador): RedirectResponse
    {
        $this->assegurarFeiraEditavel($patrocinador->feira);

        $patrocinador->delete();

        return redirect()->route('painel.patrocinadores.index')->with('sucesso', 'Patrocinador eliminado.');
    }
}
