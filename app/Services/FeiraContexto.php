<?php

namespace App\Services;

use App\Models\Feira;
use Illuminate\Support\Facades\Session;

/**
 * Resolve a "feira em que se está a trabalhar" dentro de /painel. As rotas
 * partilhadas (Etapa 5, secção 0) não têm {feira} no caminho, por isso é
 * preciso um contexto implícito: a edição ativa (publicada/em_curso) ou,
 * na sua falta, a mais recente em preparação — sem isto nenhum módulo
 * operacional sabe em que edição está a escrever (Etapa 3, decisão 1.7).
 */
class FeiraContexto
{
    private const CHAVE_SESSAO = 'feira_atual_id';

    public function atual(): ?Feira
    {
        $id = Session::get(self::CHAVE_SESSAO);

        if ($id && $feira = Feira::find($id)) {
            return $feira;
        }

        $feira = Feira::ativa()->first() ?? Feira::orderByDesc('created_at')->first();

        if ($feira) {
            Session::put(self::CHAVE_SESSAO, $feira->id);
        }

        return $feira;
    }

    public function definir(Feira $feira): void
    {
        Session::put(self::CHAVE_SESSAO, $feira->id);
    }
}
