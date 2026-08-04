<?php

namespace App\Notifications;

use App\Models\Inscricao;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * RF23 (Etapa 1) — a Comissão precisa de saber que há uma inscrição nova à
 * espera de avaliação. Só canal database nesta fase (sem mail — evita
 * inundar a caixa de correio da Comissão a cada submissão).
 */
class NovaInscricaoSubmetida extends Notification
{
    use Queueable;

    public function __construct(private readonly Inscricao $inscricao)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'inscricao_id' => $this->inscricao->id,
            'titulo' => 'Nova inscrição para avaliar',
            'mensagem' => "{$this->inscricao->professor->name} submeteu uma inscrição de {$this->inscricao->tipo_atividade}.",
            'url' => route('painel.inscricoes.show', $this->inscricao),
        ];
    }
}
