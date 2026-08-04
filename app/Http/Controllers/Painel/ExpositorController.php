<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Painel\ExpositorRequest;
use App\Models\Expositor;
use App\Models\Feira;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpositorController extends Controller
{
    public function index(Request $request): View
    {
        $feira = $request->attributes->get('feiraAtual');
        $expositores = $feira
            ? $feira->expositores()->with(['professor', 'stand'])->orderBy('turma')->paginate(15)
            : null;

        return view('painel.expositores.index', compact('expositores', 'feira'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $feira = $request->attributes->get('feiraAtual');
        if (! $feira) {
            return redirect()->route('painel.dashboard')->with('erro', 'Seleciona ou cria uma edição da feira primeiro.');
        }

        return view('painel.expositores.form', [
            'expositor' => new Expositor(),
            'professores' => $this->professores(),
            'stands' => $this->standsDisponiveis($feira),
        ]);
    }

    public function store(ExpositorRequest $request): RedirectResponse
    {
        $feira = $request->attributes->get('feiraAtual');
        $this->assegurarFeiraEditavel($feira);

        $dados = $request->validated();
        $dados['feira_id'] = $feira->id;
        $dados['estado'] = $dados['estado'] ?? 'pendente';

        $expositor = Expositor::create($dados);
        $this->guardarFotos($request, $expositor);

        return redirect()->route('painel.expositores.index')->with('sucesso', 'Expositor criado.');
    }

    public function edit(Expositor $expositor): View
    {
        $expositor->load('fotos');

        return view('painel.expositores.form', [
            'expositor' => $expositor,
            'professores' => $this->professores(),
            'stands' => $this->standsDisponiveis($expositor->feira, $expositor->stand_id),
        ]);
    }

    public function update(ExpositorRequest $request, Expositor $expositor): RedirectResponse
    {
        $this->assegurarFeiraEditavel($expositor->feira);

        $expositor->update($request->validated());
        $this->guardarFotos($request, $expositor);

        return redirect()->route('painel.expositores.index')->with('sucesso', 'Expositor atualizado.');
    }

    public function destroy(Expositor $expositor): RedirectResponse
    {
        $this->assegurarFeiraEditavel($expositor->feira);

        $expositor->delete();

        return redirect()->route('painel.expositores.index')->with('sucesso', 'Expositor eliminado.');
    }

    private function guardarFotos(Request $request, Expositor $expositor): void
    {
        foreach ($request->hasFile('fotos') ? $request->file('fotos') : [] as $foto) {
            $expositor->fotos()->create(['path' => $foto->store('expositores', 'public')]);
        }
    }

    private function professores(): Collection
    {
        return Role::where('slug', 'professor')->first()?->users()->orderBy('name')->get() ?? new Collection();
    }

    private function standsDisponiveis(?Feira $feira, ?int $manterId = null): Collection
    {
        if (! $feira) {
            return new Collection();
        }

        return $feira->stands()
            ->where(function ($query) use ($manterId) {
                $query->whereDoesntHave('expositor');
                if ($manterId) {
                    $query->orWhere('id', $manterId);
                }
            })
            ->orderBy('numero')
            ->get();
    }
}
