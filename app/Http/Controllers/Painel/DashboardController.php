<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\ProgramacaoItem;
use App\Models\Role;
use App\Models\Visita;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $feira = $request->attributes->get('feiraAtual');

        if (! $feira) {
            return view('painel.dashboard', [
                'stats' => [],
                'inscricoesPorEstado' => collect(),
                'proximasApresentacoes' => collect(),
            ]);
        }

        $stats = [
            'inscritos' => $feira->inscricoes()->count(),
            'expositores' => $feira->expositores()->count(),
            'stands' => $feira->stands()->count(),
            'atividades' => $feira->atividades()->count(),
            'professores' => Role::where('slug', 'professor')->first()?->users()->count() ?? 0,
            'alunos' => Aluno::query()->count(),
            'visitantes' => Visita::where('feira_id', $feira->id)->count(),
        ];

        $inscricoesPorEstado = $feira->inscricoes()
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $proximasApresentacoes = ProgramacaoItem::query()
            ->where('feira_id', $feira->id)
            ->where('data', '>=', now()->toDateString())
            ->orderBy('data')
            ->orderBy('hora_inicio')
            ->with('atividade')
            ->limit(5)
            ->get();

        return view('painel.dashboard', compact('stats', 'inscricoesPorEstado', 'proximasApresentacoes'));
    }
}
