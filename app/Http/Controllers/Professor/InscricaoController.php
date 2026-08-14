<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professor\InscricaoRequest;
use App\Models\Aluno;
use App\Models\Feira;
use App\Models\Inscricao;
use App\Models\User;
use App\Notifications\NovaInscricaoSubmetida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

/**
 * RC01 (Etapa 4): um utilizador só edita a própria inscrição enquanto
 * `pendente`. Partilhado por Professor e Aluno (decisão revista após a
 * Etapa 10 — ver docs/10-documentacao.md); `professor_id` guarda sempre
 * quem submeteu, independentemente do papel.
 *
 * Quando a inscrição é em nome de Aluno(s) (RF04, revisto após a Etapa 10 e
 * simplificado mais duas vezes depois — ver docs): o Professor continua a
 * poder escolher do seu próprio plantel (Professor\AlunoController), mas o
 * Aluno nunca precisa de nada disso — mesmo uma conta criada agora mesmo em
 * `/registar`, sem turma nem professor nenhum, já consegue submeter. A
 * turma fica em branco nesse caso (`derivarClasseETurma()`), a Comissão
 * decide sem ela. `User::alunoLigado()` continua a ter prioridade quando
 * existe (liga a um registo de Aluno concreto, útil para o relatório de
 * participantes); na ausência dele, cai-se para `User::turma`.
 */
class InscricaoController extends Controller
{
    private const CAMPOS_BOOLEANOS = [
        'necessita_palco', 'necessita_eletricidade', 'necessita_projetor', 'necessita_som',
    ];

    public function index(Request $request): View
    {
        $inscricoes = Inscricao::where('professor_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('professor.inscricoes.index', compact('inscricoes'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $feira = Feira::ativa()->first();

        if (! $feira) {
            return redirect()->route('professor.inscricoes.index')
                ->with('erro', 'Não há nenhuma edição da feira aberta a inscrições de momento.');
        }

        return view('professor.inscricoes.form', [
            'inscricao' => new Inscricao(),
            'feira' => $feira,
            'alunosDoProfessor' => $this->alunosDoProfessor($request),
        ]);
    }

    public function store(InscricaoRequest $request): RedirectResponse
    {
        $feira = Feira::ativa()->first();

        if (! $feira) {
            return redirect()->route('professor.inscricoes.index')
                ->with('erro', 'Não há nenhuma edição da feira aberta a inscrições de momento.');
        }

        $dados = $request->validated();
        $alunoIds = $this->resolverAlunoIds($request);
        unset($dados['alunos'], $dados['produto_foto']);

        $dados['feira_id'] = $feira->id;
        $dados['professor_id'] = $request->user()->id;
        $dados['estado'] = 'pendente';
        foreach (self::CAMPOS_BOOLEANOS as $campo) {
            $dados[$campo] = $request->boolean($campo);
        }
        $dados['produto_foto_path'] = $this->guardarFotoDoProduto($request);
        if ($dados['tipo_participante'] === 'aluno') {
            [$dados['classe'], $dados['turma']] = $this->derivarClasseETurma($request, $alunoIds);
        }

        $inscricao = Inscricao::create($dados);
        if ($alunoIds) {
            $inscricao->alunos()->sync($alunoIds);
        }
        $this->guardarFotos($request, $inscricao);

        $comissao = User::whereHas('roles', fn ($q) => $q->whereIn('slug', ['comissao', 'administrador']))->get();
        Notification::send($comissao, new NovaInscricaoSubmetida($inscricao));

        return redirect()->route('professor.inscricoes.index')
            ->with('sucesso', 'Inscrição submetida com sucesso. Não te esqueças de entregar um livro na secretaria para a biblioteca da escola — é condição para a aprovação. Aguarda a avaliação da Comissão Organizadora.');
    }

    public function edit(Inscricao $inscricao): View|RedirectResponse
    {
        abort_unless($inscricao->professor_id === auth()->id(), 403);

        if ($inscricao->estado !== 'pendente') {
            return redirect()->route('professor.inscricoes.index')
                ->with('erro', 'Só é possível editar uma inscrição enquanto estiver pendente.');
        }

        $inscricao->load(['fotos', 'alunos']);

        return view('professor.inscricoes.form', [
            'inscricao' => $inscricao,
            'feira' => $inscricao->feira,
            'alunosDoProfessor' => $this->alunosDoProfessor(request()),
        ]);
    }

    public function update(InscricaoRequest $request, Inscricao $inscricao): RedirectResponse
    {
        abort_unless($inscricao->professor_id === auth()->id(), 403);

        if ($inscricao->estado !== 'pendente') {
            return redirect()->route('professor.inscricoes.index')
                ->with('erro', 'Só é possível editar uma inscrição enquanto estiver pendente.');
        }

        $dados = $request->validated();
        $alunoIds = $this->resolverAlunoIds($request);
        unset($dados['alunos'], $dados['produto_foto']);

        foreach (self::CAMPOS_BOOLEANOS as $campo) {
            $dados[$campo] = $request->boolean($campo);
        }
        if ($novaFoto = $this->guardarFotoDoProduto($request)) {
            $dados['produto_foto_path'] = $novaFoto;
        }
        if ($dados['tipo_participante'] === 'aluno') {
            [$dados['classe'], $dados['turma']] = $this->derivarClasseETurma($request, $alunoIds);
        }

        $inscricao->update($dados);
        $inscricao->alunos()->sync($alunoIds);
        $this->guardarFotos($request, $inscricao);

        return redirect()->route('professor.inscricoes.index')->with('sucesso', 'Inscrição atualizada.');
    }

    private function alunosDoProfessor(Request $request): \Illuminate\Support\Collection
    {
        return $request->user()->hasRole('professor')
            ? Aluno::where('professor_id', $request->user()->id)->orderBy('turma')->orderBy('nome')->get()
            : collect();
    }

    /**
     * Professor escolhe do seu plantel (campo "alunos" do formulário); Aluno
     * nunca escolhe — a sua própria inscrição liga-se sempre ao seu registo
     * via alunoLigado(), resolvido aqui em vez de confiar em input do utilizador.
     */
    private function resolverAlunoIds(Request $request): array
    {
        if ($request->user()->hasRole('aluno')) {
            $aluno = $request->user()->alunoLigado;

            return $aluno ? [$aluno->id] : [];
        }

        return array_map('intval', $request->input('alunos', []));
    }

    /**
     * @return array{0: ?string, 1: ?string} [classe, turma]
     */
    private function derivarClasseETurma(Request $request, array $alunoIds): array
    {
        if ($alunoIds) {
            $aluno = Aluno::find($alunoIds[0]);

            return [$aluno?->classe, $aluno?->turma];
        }

        if ($request->user()->hasRole('aluno')) {
            return [$request->user()->classe, $request->user()->turma];
        }

        return [null, null];
    }

    private function guardarFotoDoProduto(Request $request): ?string
    {
        return $request->file('produto_foto')?->store('gastronomia', 'public');
    }

    private function guardarFotos(Request $request, Inscricao $inscricao): void
    {
        foreach ($request->hasFile('fotos') ? $request->file('fotos') : [] as $foto) {
            $inscricao->fotos()->create(['path' => $foto->store('inscricoes', 'public')]);
        }
    }
}
