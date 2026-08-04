<?php

namespace App\Notifications;

use App\Models\Inscricao;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * RF23 (Etapa 1): o professor tem de saber se a inscrição foi aprovada ou
 * rejeitada. Vai por mail (canal "log" em desenvolvimento — Etapa 7) e por
 * database (sino no painel).
 */
class InscricaoAvaliada extends Notification
{
    use Queueable;

    public function __construct(private readonly Inscricao $inscricao)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $aprovada = $this->inscricao->estado === 'aprovada';

        $mensagem = (new MailMessage())
            ->subject($aprovada ? 'A tua inscrição foi aprovada' : 'A tua inscrição foi rejeitada')
            ->greeting("Olá {$notifiable->name},");

        if ($aprovada && $this->inscricao->tipo_atividade === 'gastronomia') {
            $mensagem->line('A tua inscrição foi aprovada pela Comissão Organizadora e já tens uma banca atribuída.');
        } elseif ($aprovada) {
            $mensagem->line('A tua inscrição foi aprovada pela Comissão Organizadora e já está agendada na Programação.');
        } else {
            $mensagem->line('A tua inscrição foi rejeitada pela Comissão Organizadora.')
                ->line("Motivo: {$this->inscricao->comentario_avaliacao}");
        }

        return $mensagem->action('Ver as minhas inscrições', route('professor.inscricoes.index'));
    }

    public function toArray(object $notifiable): array
    {
        $aprovada = $this->inscricao->estado === 'aprovada';

        return [
            'inscricao_id' => $this->inscricao->id,
            'titulo' => $aprovada ? 'Inscrição aprovada' : 'Inscrição rejeitada',
            'mensagem' => match (true) {
                $aprovada && $this->inscricao->tipo_atividade === 'gastronomia' => 'A tua inscrição foi aprovada e já tens uma banca atribuída.',
                $aprovada => 'A tua inscrição foi aprovada e agendada.',
                default => "A tua inscrição foi rejeitada: {$this->inscricao->comentario_avaliacao}",
            },
            'url' => route('professor.inscricoes.index'),
        ];
    }
}
