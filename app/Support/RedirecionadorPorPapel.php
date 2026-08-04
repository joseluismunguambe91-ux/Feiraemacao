<?php

namespace App\Support;

use App\Models\User;

/**
 * Único ponto de decisão para onde cada papel vai depois do login (Etapa 7,
 * item 1) — evita espalhar esta lógica pelo Controller de login e por
 * qualquer outro sítio que precise de saber "para onde mando este utilizador".
 *
 * Aponta sempre para uma rota que já existe nesta fase — nunca para um
 * prefixo "vazio" sem página de destino (ex.: /professor sozinho não tem
 * rota; /professor/inscricoes tem).
 */
class RedirecionadorPorPapel
{
    public static function destino(User $user): string
    {
        return match (true) {
            $user->hasAnyRole(['administrador', 'comissao']) => '/painel',
            // Professor e Aluno partilham a mesma área de inscrições —
            // ambos submetem e acompanham a sua própria inscrição.
            $user->hasAnyRole(['professor', 'aluno']) => '/professor/inscricoes',
            default => '/',
        };
    }
}
