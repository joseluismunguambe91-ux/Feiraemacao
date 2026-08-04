<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professor\AlunoRequest;
use App\Models\Aluno;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * RF04 (Etapa 1): o professor regista os seus próprios alunos — plantel
 * reutilizado entre edições da feira (não tem feira_id) e usado pela
 * inscrição (Professor\InscricaoController) para escolher quem participa
 * em vez de escrever a turma à mão.
 */
class AlunoController extends Controller
{
    public function index(Request $request): View
    {
        $alunos = Aluno::where('professor_id', $request->user()->id)
            ->orderBy('turma')->orderBy('nome')
            ->paginate(20);

        return view('professor.alunos.index', compact('alunos'));
    }

    public function create(): View
    {
        return view('professor.alunos.form', [
            'aluno' => new Aluno(),
            'utilizadoresAluno' => $this->utilizadoresAlunoDisponiveis(),
        ]);
    }

    public function store(AlunoRequest $request): RedirectResponse
    {
        Aluno::create([...$request->validated(), 'professor_id' => $request->user()->id]);

        return redirect()->route('professor.alunos.index')->with('sucesso', 'Aluno registado.');
    }

    public function edit(Aluno $aluno): View
    {
        abort_unless($aluno->professor_id === auth()->id(), 403);

        return view('professor.alunos.form', [
            'aluno' => $aluno,
            'utilizadoresAluno' => $this->utilizadoresAlunoDisponiveis($aluno),
        ]);
    }

    public function update(AlunoRequest $request, Aluno $aluno): RedirectResponse
    {
        abort_unless($aluno->professor_id === auth()->id(), 403);

        $aluno->update($request->validated());

        return redirect()->route('professor.alunos.index')->with('sucesso', 'Aluno atualizado.');
    }

    public function destroy(Aluno $aluno): RedirectResponse
    {
        abort_unless($aluno->professor_id === auth()->id(), 403);

        $aluno->delete();

        return redirect()->route('professor.alunos.index')->with('sucesso', 'Aluno removido.');
    }

    private function utilizadoresAlunoDisponiveis(?Aluno $aluno = null)
    {
        return User::whereHas('roles', fn ($q) => $q->where('slug', 'aluno'))
            ->whereDoesntHave('alunoLigado', fn ($q) => $aluno ? $q->where('id', '!=', $aluno->id) : $q)
            ->orderBy('name')
            ->get();
    }
}
