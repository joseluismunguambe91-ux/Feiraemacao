<?php

namespace App\Services;

use App\Models\ProgramacaoItem;
use Carbon\Carbon;

/**
 * RN04 (Etapa 3): nenhuma sobreposição de horário no mesmo palco, na mesma
 * data. Extraído para aqui porque é usado em três sítios (aprovação de
 * inscrição — Fase 8.4, agendamento/reorganização direta — Fase 8.5,
 * verificação AJAX em tempo real) — evita repetir a mesma query três vezes.
 */
class ConflitoAgendaVerificador
{
    public function existe(int $feiraId, string $data, ?string $palco, string $horaInicio, string $horaFim, ?int $ignorarItemId = null): bool
    {
        if (empty($palco)) {
            return false;
        }

        // whereTime formata a coluna com segundos (strftime/TIME_FORMAT)
        // mas não normaliza o valor do outro lado — "10:15" (vindo de um
        // <input type="time">) tem de virar "10:15:00" antes de comparar,
        // senão a comparação de texto trata-os como diferentes (apanhado
        // pelos testes da Etapa 9 em SQLite; mascarado em MySQL porque a
        // coluna TIME já coage o literal automaticamente).
        $horaInicio = Carbon::parse($horaInicio)->format('H:i:s');
        $horaFim = Carbon::parse($horaFim)->format('H:i:s');

        return ProgramacaoItem::where('feira_id', $feiraId)
            // whereDate pela mesma razão, aplicada à coluna "data".
            ->whereDate('data', $data)
            ->where('palco', $palco)
            ->whereTime('hora_inicio', '<', $horaFim)
            ->whereTime('hora_fim', '>', $horaInicio)
            ->when($ignorarItemId, fn ($query) => $query->where('id', '!=', $ignorarItemId))
            ->exists();
    }
}
