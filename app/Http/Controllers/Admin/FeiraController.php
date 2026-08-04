<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeiraRequest;
use App\Models\Feira;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FeiraController extends Controller
{
    public function index(): View
    {
        $feiras = Feira::orderByDesc('data_inicio')->paginate(15);

        return view('admin.feiras.index', compact('feiras'));
    }

    public function create(): View
    {
        return view('admin.feiras.form', ['feira' => new Feira()]);
    }

    public function store(FeiraRequest $request): RedirectResponse
    {
        $dados = $request->validated();
        $dados['banner_path'] = $request->file('banner')?->store('feiras', 'public');
        $dados['logotipo_path'] = $request->file('logotipo')?->store('feiras', 'public');
        $dados['regulamento_path'] = $request->file('regulamento')?->store('feiras', 'public');

        Feira::create($dados);

        return redirect()->route('admin.feiras.index')->with('sucesso', 'Edição criada com sucesso.');
    }

    public function edit(Feira $feira): View|RedirectResponse
    {
        if (Gate::denies('feira-editavel', $feira)) {
            return redirect()->route('admin.feiras.index')
                ->with('erro', 'Esta edição está arquivada — reverte o estado primeiro para poderes editá-la.');
        }

        return view('admin.feiras.form', compact('feira'));
    }

    public function update(FeiraRequest $request, Feira $feira): RedirectResponse
    {
        if (Gate::denies('feira-editavel', $feira)) {
            return redirect()->route('admin.feiras.index')
                ->with('erro', 'Esta edição está arquivada — reverte o estado primeiro para poderes editá-la.');
        }

        $dados = $request->validated();

        if ($request->hasFile('banner')) {
            $dados['banner_path'] = $request->file('banner')->store('feiras', 'public');
        }
        if ($request->hasFile('logotipo')) {
            $dados['logotipo_path'] = $request->file('logotipo')->store('feiras', 'public');
        }
        if ($request->hasFile('regulamento')) {
            $dados['regulamento_path'] = $request->file('regulamento')->store('feiras', 'public');
        }

        $feira->update($dados);

        return redirect()->route('admin.feiras.index')->with('sucesso', 'Edição atualizada com sucesso.');
    }

    public function destroy(Feira $feira): RedirectResponse
    {
        $feira->delete();

        return redirect()->route('admin.feiras.index')->with('sucesso', 'Edição eliminada.');
    }
}
