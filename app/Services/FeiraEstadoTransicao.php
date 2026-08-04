<?php

namespace App\Services;

use App\Models\Feira;
use RuntimeException;

/**
 * Motor do ciclo de vida da feira (RF07/RF08, Etapa 1; RC02/RC05, Etapa 4):
 * rascunho -> publicada -> em_curso -> encerrada -> arquivada. "Avançar" é
 * partilhado (Administrador + Comissão); "reverter" é exclusivo do
 * Administrador, incluindo a exceção de desarquivar (Etapa 4, secção 2).
 *
 * A validação aqui é só para dar um erro amigável antes de chegar à base de
 * dados — a garantia real de que só existe uma edição ativa é o índice
 * único sobre estado_ativo (Etapa 3, decisão 1.5).
 */
class FeiraEstadoTransicao
{
    private const ORDEM = ['rascunho', 'publicada', 'em_curso', 'encerrada', 'arquivada'];

    public function avancar(Feira $feira): void
    {
        $indiceAtual = array_search($feira->estado, self::ORDEM, true);

        if ($indiceAtual === false || $indiceAtual === count(self::ORDEM) - 1) {
            throw new RuntimeException('Esta edição já está no último estado possível.');
        }

        $proximo = self::ORDEM[$indiceAtual + 1];

        if (in_array($proximo, ['publicada', 'em_curso'], true)) {
            $this->assegurarSemOutraAtiva($feira);
        }

        $feira->update(['estado' => $proximo]);
    }

    public function reverter(Feira $feira): void
    {
        $indiceAtual = array_search($feira->estado, self::ORDEM, true);

        if ($indiceAtual === false || $indiceAtual === 0) {
            throw new RuntimeException('Esta edição já está no primeiro estado possível.');
        }

        $feira->update(['estado' => self::ORDEM[$indiceAtual - 1]]);
    }

    private function assegurarSemOutraAtiva(Feira $feira): void
    {
        $existeOutraAtiva = Feira::ativa()->where('id', '!=', $feira->id)->exists();

        if ($existeOutraAtiva) {
            throw new RuntimeException('Já existe outra edição publicada ou em curso. Encerra-a primeiro.');
        }
    }
}
