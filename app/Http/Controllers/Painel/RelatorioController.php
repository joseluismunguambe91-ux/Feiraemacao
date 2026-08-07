<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Jobs\GerarRelatorioJob;
use App\Models\RelatorioGerado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RelatorioController extends Controller
{
    public function index(Request $request): View
    {
        $feira = $request->attributes->get('feiraAtual');
        $relatorios = $feira ? $feira->relatoriosGerados()->orderByDesc('created_at')->paginate(15) : null;

        return view('painel.relatorios.index', compact('relatorios', 'feira'));
    }

    public function store(Request $request): RedirectResponse
    {
        $feira = $request->attributes->get('feiraAtual');

        if (! $feira) {
            return redirect()->route('painel.dashboard')->with('erro', 'Seleciona ou cria uma edição da feira primeiro.');
        }

        $dados = $request->validate([
            'tipo' => ['required', Rule::in(['participantes', 'atividades', 'expositores', 'gastronomia', 'programacao', 'visitantes'])],
            'formato' => ['required', Rule::in(['pdf', 'excel'])],
        ]);

        $relatorio = RelatorioGerado::create([
            'feira_id' => $feira->id,
            'tipo' => $dados['tipo'],
            'formato' => $dados['formato'],
            'gerado_por' => $request->user()->id,
            'estado' => 'processando',
        ]);

        GerarRelatorioJob::dispatch($relatorio);

        return redirect()->route('painel.relatorios.index')
            ->with('sucesso', 'Relatório a ser gerado — vais receber uma notificação quando estiver pronto.');
    }

    public function download(RelatorioGerado $relatorio): StreamedResponse
    {
        abort_unless($relatorio->estado === 'concluido' && $relatorio->path_ficheiro, 404);

        return Storage::disk('public')->download($relatorio->path_ficheiro);
    }
}
