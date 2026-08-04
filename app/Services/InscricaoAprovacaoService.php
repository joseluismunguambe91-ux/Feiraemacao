<?php

namespace App\Services;

use App\Models\Atividade;
use App\Models\Expositor;
use App\Models\GastronomiaItem;
use App\Models\Inscricao;
use App\Models\Stand;
use App\Models\User;
use App\Notifications\InscricaoAvaliada;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * O coração do fluxo de aprovação (RF21/RF22, Etapa 1; fluxograma da Etapa 2,
 * secção 1.2; RN04/RN07, Etapa 3). Aprovar não é só mudar o estado — ramifica
 * em dois desfechos consoante inscricao.tipo_atividade: uma apresentação
 * agendada (Atividade+ProgramacaoItem, com verificação de conflito de
 * horário) ou uma banca de gastronomia (Expositor associado a um Stand
 * livre + um GastronomiaItem com o prato/preço que a Comissão confirmou,
 * sem agenda envolvida) — decisão tomada após a Etapa 10, ver
 * docs/10-documentacao.md.
 */
class InscricaoAprovacaoService
{
    public function __construct(private readonly ConflitoAgendaVerificador $conflitoVerificador)
    {
    }

    public function aprovar(Inscricao $inscricao, User $avaliador, array $dados): void
    {
        $this->assegurarPendente($inscricao);

        if ($inscricao->tipo_atividade === 'gastronomia') {
            $this->aprovarGastronomia($inscricao, $avaliador, $dados);
        } else {
            $this->aprovarAtividade($inscricao, $avaliador, $dados);
        }

        $inscricao->professor->notify(new InscricaoAvaliada($inscricao));
    }

    private function aprovarAtividade(Inscricao $inscricao, User $avaliador, array $agendamento): void
    {
        $this->assegurarSemConflito($inscricao, $agendamento);

        DB::transaction(function () use ($inscricao, $avaliador, $agendamento) {
            $atividade = Atividade::create([
                'feira_id' => $inscricao->feira_id,
                'inscricao_id' => $inscricao->id,
                'tipo' => $inscricao->tipo_atividade,
                'titulo' => $agendamento['titulo'],
                'descricao' => $inscricao->descricao,
                'responsavel_id' => $inscricao->professor_id,
                'participantes_previstos' => $inscricao->numero_participantes,
                'estado' => 'confirmada',
            ]);

            $atividade->itensProgramacao()->create([
                'feira_id' => $inscricao->feira_id,
                'data' => $agendamento['data'],
                'hora_inicio' => $agendamento['hora_inicio'],
                'hora_fim' => $agendamento['hora_fim'],
                'local' => $agendamento['local'],
                'palco' => $agendamento['palco'] ?? null,
            ]);

            $inscricao->update([
                'estado' => 'aprovada',
                'avaliado_por' => $avaliador->id,
                'avaliado_em' => now(),
            ]);
        });
    }

    private function aprovarGastronomia(Inscricao $inscricao, User $avaliador, array $dados): void
    {
        $this->assegurarStandLivre((int) $dados['stand_id']);

        DB::transaction(function () use ($inscricao, $avaliador, $dados) {
            Expositor::create([
                'feira_id' => $inscricao->feira_id,
                'inscricao_id' => $inscricao->id,
                'professor_id' => $inscricao->professor_id,
                'turma' => $inscricao->turma,
                'categoria' => 'Gastronomia',
                'descricao' => $inscricao->descricao,
                'stand_id' => $dados['stand_id'],
                'estado' => 'ativo',
            ]);

            GastronomiaItem::create([
                'feira_id' => $inscricao->feira_id,
                'inscricao_id' => $inscricao->id,
                'nome' => $dados['produto_nome'],
                'descricao' => $inscricao->descricao,
                'preco' => $dados['produto_preco'],
                'foto_path' => $inscricao->produto_foto_path,
                'disponivel' => true,
            ]);

            $inscricao->update([
                'estado' => 'aprovada',
                'avaliado_por' => $avaliador->id,
                'avaliado_em' => now(),
            ]);
        });
    }

    public function rejeitar(Inscricao $inscricao, User $avaliador, string $comentario): void
    {
        $this->assegurarPendente($inscricao);

        $inscricao->update([
            'estado' => 'rejeitada',
            'comentario_avaliacao' => $comentario,
            'avaliado_por' => $avaliador->id,
            'avaliado_em' => now(),
        ]);

        $inscricao->professor->notify(new InscricaoAvaliada($inscricao));
    }

    private function assegurarPendente(Inscricao $inscricao): void
    {
        if ($inscricao->estado !== 'pendente') {
            throw new RuntimeException('Esta inscrição já foi avaliada.');
        }
    }

    private function assegurarSemConflito(Inscricao $inscricao, array $agendamento): void
    {
        $existeConflito = $this->conflitoVerificador->existe(
            $inscricao->feira_id,
            $agendamento['data'],
            $agendamento['palco'] ?? null,
            $agendamento['hora_inicio'],
            $agendamento['hora_fim'],
        );

        if ($existeConflito) {
            throw new RuntimeException('Já existe outra atividade agendada nesse palco, nessa data e nesse horário. Ajusta o agendamento.');
        }
    }

    private function assegurarStandLivre(int $standId): void
    {
        $ocupado = Expositor::where('stand_id', $standId)->exists();

        if ($ocupado) {
            throw new RuntimeException('Esse stand já está atribuído a outro expositor. Escolhe outro.');
        }

        if (! Stand::whereKey($standId)->exists()) {
            throw new RuntimeException('Esse stand já não existe. Escolhe outro.');
        }
    }
}
