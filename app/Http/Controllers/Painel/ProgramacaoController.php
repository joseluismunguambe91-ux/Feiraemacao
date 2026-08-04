<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Painel\ProgramacaoItemRequest;
use App\Models\Atividade;
use App\Models\ProgramacaoItem;
use App\Services\ConflitoAgendaVerificador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * RF24/RF25 (Etapa 1): a "geração automática" já acontece em cada aprovação
 * de inscrição (Fase 8.4, InscricaoAprovacaoService) — este módulo cobre o
 * resto: agendar atividades de origem direta (Fase 8.3, ainda sem entrada
 * na Programação) e reorganizar qualquer agendamento já existente.
 */
class ProgramacaoController extends Controller
{
    public function index(Request $request): View
    {
        $feira = $request->attributes->get('feiraAtual');

        $itens = $feira
            ? ProgramacaoItem::where('feira_id', $feira->id)
                ->with('atividade')
                ->orderBy('data')
                ->orderBy('palco')
                ->orderBy('hora_inicio')
                ->get()
            : collect();

        $naoAgendadas = $feira
            ? $feira->atividades()->whereDoesntHave('itensProgramacao')->orderBy('titulo')->get()
            : collect();

        return view('painel.programacao.index', compact('itens', 'feira', 'naoAgendadas'));
    }

    public function create(Atividade $atividade): View
    {
        abort_unless($atividade->itensProgramacao()->doesntExist(), 404);

        return view('painel.programacao.form', ['atividade' => $atividade, 'item' => new ProgramacaoItem()]);
    }

    public function store(ProgramacaoItemRequest $request, Atividade $atividade, ConflitoAgendaVerificador $verificador): RedirectResponse
    {
        $this->assegurarFeiraEditavel($atividade->feira);

        $dados = $request->validated();

        if ($verificador->existe($atividade->feira_id, $dados['data'], $dados['palco'] ?? null, $dados['hora_inicio'], $dados['hora_fim'])) {
            return back()->withInput()->with('erro', 'Já existe outra atividade agendada nesse palco, nessa data e nesse horário.');
        }

        $dados['feira_id'] = $atividade->feira_id;
        $atividade->itensProgramacao()->create($dados);

        return redirect()->route('painel.programacao.index')->with('sucesso', 'Atividade agendada.');
    }

    public function edit(ProgramacaoItem $item): View
    {
        return view('painel.programacao.form', ['atividade' => $item->atividade, 'item' => $item]);
    }

    public function update(ProgramacaoItemRequest $request, ProgramacaoItem $item, ConflitoAgendaVerificador $verificador): RedirectResponse
    {
        $this->assegurarFeiraEditavel($item->feira);

        $dados = $request->validated();

        if ($verificador->existe($item->feira_id, $dados['data'], $dados['palco'] ?? null, $dados['hora_inicio'], $dados['hora_fim'], $item->id)) {
            return back()->withInput()->with('erro', 'Já existe outra atividade agendada nesse palco, nessa data e nesse horário.');
        }

        $item->update($dados);

        return redirect()->route('painel.programacao.index')->with('sucesso', 'Agendamento atualizado.');
    }

    /** Verificação em tempo real (Etapa 1, risco R8) antes de o formulário ser submetido. */
    public function verificarConflito(Request $request, ConflitoAgendaVerificador $verificador): JsonResponse
    {
        $dados = $request->validate([
            'feira_id' => ['required', 'integer'],
            'data' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fim' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'palco' => ['nullable', 'string'],
            'ignorar_item_id' => ['nullable', 'integer'],
        ]);

        $conflito = $verificador->existe(
            $dados['feira_id'],
            $dados['data'],
            $dados['palco'] ?? null,
            $dados['hora_inicio'],
            $dados['hora_fim'],
            $dados['ignorar_item_id'] ?? null,
        );

        return response()->json(['conflito' => $conflito]);
    }
}
