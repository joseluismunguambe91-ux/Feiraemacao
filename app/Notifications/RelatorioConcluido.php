<?php

namespace App\Notifications;

use App\Models\RelatorioGerado;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RelatorioConcluido extends Notification
{
    use Queueable;

    public function __construct(private readonly RelatorioGerado $relatorio)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'relatorio_id' => $this->relatorio->id,
            'titulo' => 'Relatório pronto',
            'mensagem' => "O relatório de {$this->relatorio->tipo} já está disponível para download.",
            'url' => route('painel.relatorios.index'),
        ];
    }
}
