<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Inscricao;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InscricaoController extends Controller
{
    public function index(Request $request): View
    {
        $feira = $request->attributes->get('feiraAtual');
        $estadoFiltro = $request->query('estado', 'pendente');

        $inscricoes = $feira
            ? $feira->inscricoes()
                ->with('professor')
                ->when($estadoFiltro !== 'todas', fn ($query) => $query->where('estado', $estadoFiltro))
                ->orderByDesc('created_at')
                ->paginate(15)
                ->withQueryString()
            : null;

        return view('painel.inscricoes.index', compact('inscricoes', 'feira', 'estadoFiltro'));
    }

    public function show(Inscricao $inscricao): View
    {
        $inscricao->load(['professor', 'fotos', 'avaliadoPor', 'atividade.itensProgramacao', 'expositor.stand']);

        $horaFimSugerida = null;
        if ($inscricao->horario_pretendido && $inscricao->duracao_minutos) {
            $horaFimSugerida = \Carbon\Carbon::createFromFormat('H:i:s', $inscricao->horario_pretendido)
                ->addMinutes($inscricao->duracao_minutos)
                ->format('H:i');
        }

        $standsLivres = $inscricao->tipo_atividade === 'gastronomia'
            ? $inscricao->feira->stands()->whereDoesntHave('expositor')->orderBy('numero')->get()
            : collect();

        return view('painel.inscricoes.show', compact('inscricao', 'horaFimSugerida', 'standsLivres'));
    }
}
