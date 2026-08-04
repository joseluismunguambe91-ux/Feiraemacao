<?php

namespace App\Jobs;

use App\Models\Atividade;
use App\Models\Expositor;
use App\Models\GastronomiaItem;
use App\Models\Inscricao;
use App\Models\ProgramacaoItem;
use App\Models\RelatorioGerado;
use App\Notifications\RelatorioConcluido;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Etapa 1, risco R5: geração de relatórios corre em background (Job/Queue),
 * não no próprio pedido HTTP — evita bloquear o servidor com exportações
 * pesadas. "Excel" gera CSV (abre nativamente no Excel/LibreOffice) em vez
 * de um .xlsx real, para não trazer uma dependência pesada (PhpSpreadsheet)
 * só por causa da formatação — decisão registada, não uma omissão.
 */
class GerarRelatorioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly RelatorioGerado $relatorio)
    {
    }

    public function handle(): void
    {
        try {
            $dados = $this->obterDados();
            $caminho = $this->relatorio->formato === 'pdf'
                ? $this->gerarPdf($dados)
                : $this->gerarCsv($dados);

            $this->relatorio->update(['path_ficheiro' => $caminho, 'estado' => 'concluido']);
        } catch (Throwable $e) {
            $this->relatorio->update(['estado' => 'falhou']);
            throw $e;
        }

        $this->relatorio->geradoPor->notify(new RelatorioConcluido($this->relatorio));
    }

    private function obterDados(): Collection
    {
        $feiraId = $this->relatorio->feira_id;

        return match ($this->relatorio->tipo) {
            'participantes' => $this->participantes($feiraId),
            'atividades' => Atividade::where('feira_id', $feiraId)->get(),
            'expositores' => Expositor::where('feira_id', $feiraId)->with(['professor', 'stand'])->get(),
            'gastronomia' => GastronomiaItem::where('feira_id', $feiraId)->get(),
            'programacao' => ProgramacaoItem::where('feira_id', $feiraId)->with('atividade')->orderBy('data')->orderBy('hora_inicio')->get(),
        };
    }

    /**
     * Uma linha por PESSOA, não por inscrição: quando a inscrição é em nome
     * de vários Alunos (pivot inscricao_aluno), cada um sai numa linha
     * própria com a sua turma — é isso que o Administrador pede ao
     * descarregar "quem se inscreveu, com que função e em que banca"
     * (decisão pós-Etapa 10, ver docs/10-documentacao.md).
     */
    private function participantes(int $feiraId): Collection
    {
        $rotulosFuncao = [
            'teatro' => 'Teatro', 'danca' => 'Dança', 'musica' => 'Música', 'poesia' => 'Poesia',
            'ciencias' => 'Ciências', 'artesanato' => 'Artesanato', 'pintura' => 'Pintura',
            'jogos' => 'Jogos', 'gastronomia' => 'Gastronomia', 'outro' => 'Outro',
        ];

        return Inscricao::where('feira_id', $feiraId)->where('estado', 'aprovada')
            ->with(['professor', 'alunos', 'expositor.stand'])
            ->get()
            ->flatMap(function (Inscricao $inscricao) use ($rotulosFuncao) {
                $funcao = $rotulosFuncao[$inscricao->tipo_atividade] ?? $inscricao->tipo_atividade;
                $banca = $inscricao->expositor?->stand?->numero;

                if ($inscricao->tipo_participante === 'aluno' && $inscricao->alunos->isNotEmpty()) {
                    return $inscricao->alunos->map(fn ($aluno) => (object) [
                        'nome' => $aluno->nome,
                        'papel' => 'Aluno',
                        'turma' => $aluno->turma,
                        'funcao' => $funcao,
                        'banca' => $banca,
                    ]);
                }

                return [(object) [
                    'nome' => $inscricao->professor->name,
                    'papel' => $inscricao->tipo_participante === 'aluno' ? 'Aluno' : 'Professor',
                    'turma' => $inscricao->turma,
                    'funcao' => $funcao,
                    'banca' => $banca,
                ]];
            });
    }

    private function gerarPdf(Collection $dados): string
    {
        $pdf = Pdf::loadView('relatorios.pdf.'.$this->relatorio->tipo, [
            'itens' => $dados,
            'feira' => $this->relatorio->feira,
        ]);

        $nomeFicheiro = 'relatorios/'.$this->relatorio->tipo.'-'.$this->relatorio->id.'.pdf';
        Storage::disk('public')->put($nomeFicheiro, $pdf->output());

        return $nomeFicheiro;
    }

    private function gerarCsv(Collection $dados): string
    {
        $handle = fopen('php://temp', 'r+');

        foreach ($this->linhasCsv($dados) as $linha) {
            fputcsv($handle, $linha);
        }

        rewind($handle);
        $conteudo = stream_get_contents($handle);
        fclose($handle);

        $nomeFicheiro = 'relatorios/'.$this->relatorio->tipo.'-'.$this->relatorio->id.'.csv';
        Storage::disk('public')->put($nomeFicheiro, $conteudo);

        return $nomeFicheiro;
    }

    private function linhasCsv(Collection $dados): array
    {
        return match ($this->relatorio->tipo) {
            'participantes' => array_merge(
                [['Nome', 'Papel', 'Turma', 'Função', 'Banca']],
                $dados->map(fn ($p) => [$p->nome, $p->papel, $p->turma, $p->funcao, $p->banca ?? '—'])->toArray(),
            ),
            'atividades' => array_merge(
                [['Título', 'Tipo', 'Estado', 'Participantes previstos']],
                $dados->map(fn ($a) => [$a->titulo, $a->tipo, $a->estado, $a->participantes_previstos])->toArray(),
            ),
            'expositores' => array_merge(
                [['Turma', 'Categoria', 'Professor', 'Stand', 'Estado']],
                $dados->map(fn ($e) => [$e->turma, $e->categoria, $e->professor->name, $e->stand->numero ?? '—', $e->estado])->toArray(),
            ),
            'gastronomia' => array_merge(
                [['Nome', 'Categoria', 'Preço', 'Disponível']],
                $dados->map(fn ($g) => [$g->nome, $g->categoria, $g->preco, $g->disponivel ? 'Sim' : 'Não'])->toArray(),
            ),
            'programacao' => array_merge(
                [['Data', 'Hora início', 'Hora fim', 'Atividade', 'Local', 'Palco']],
                $dados->map(fn ($p) => [
                    $p->data->format('d/m/Y'), substr($p->hora_inicio, 0, 5), substr($p->hora_fim, 0, 5),
                    $p->atividade->titulo, $p->local, $p->palco,
                ])->toArray(),
            ),
        };
    }
}
