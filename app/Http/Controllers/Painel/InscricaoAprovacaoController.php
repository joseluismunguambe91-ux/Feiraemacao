<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Painel\InscricaoAprovacaoRequest;
use App\Http\Requests\Painel\InscricaoRejeicaoRequest;
use App\Models\Inscricao;
use App\Services\InscricaoAprovacaoService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class InscricaoAprovacaoController extends Controller
{
    public function aprovar(InscricaoAprovacaoRequest $request, Inscricao $inscricao, InscricaoAprovacaoService $service): RedirectResponse
    {
        $this->assegurarFeiraEditavel($inscricao->feira);

        try {
            $service->aprovar($inscricao, $request->user(), $request->validated());
        } catch (RuntimeException $e) {
            return back()->withInput()->with('erro', $e->getMessage());
        }

        $mensagem = $inscricao->tipo_atividade === 'gastronomia'
            ? 'Inscrição aprovada e banca atribuída.'
            : 'Inscrição aprovada e agendada na Programação.';

        return redirect()->route('painel.inscricoes.index')->with('sucesso', $mensagem);
    }

    public function rejeitar(InscricaoRejeicaoRequest $request, Inscricao $inscricao, InscricaoAprovacaoService $service): RedirectResponse
    {
        $this->assegurarFeiraEditavel($inscricao->feira);

        try {
            $service->rejeitar($inscricao, $request->user(), $request->validated()['comentario_avaliacao']);
        } catch (RuntimeException $e) {
            return back()->with('erro', $e->getMessage());
        }

        return redirect()->route('painel.inscricoes.index')->with('sucesso', 'Inscrição rejeitada.');
    }
}
