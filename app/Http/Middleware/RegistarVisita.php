<?php

namespace App\Http\Middleware;

use App\Models\Feira;
use App\Models\Visita;
use Closure;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contador de visitantes pedido pelo utilizador — uma linha por
 * sessão/dia (não por pedido), para "número de visitantes" significar
 * pessoas, não pedidos HTTP. Só em GETs normais (não AJAX/JSON), para não
 * contar chamadas de apoio como a pesquisa ou a verificação de conflito.
 *
 * `whereDate()` em vez de `firstOrCreate(['data' => ...])`: o mesmo motivo
 * já documentado neste projeto para outras colunas date-cast — comparação
 * simples por igualdade não é fiável entre motores de BD (ver
 * feiraemacao_sqlite_mysql_portability na memória do projeto).
 */
class RegistarVisita
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('get') && ! $request->ajax() && ! $request->wantsJson()) {
            $sessaoId = $request->session()->getId();
            $hoje = now()->toDateString();

            $jaRegistada = Visita::where('sessao_id', $sessaoId)->whereDate('data', $hoje)->exists();

            if (! $jaRegistada) {
                try {
                    Visita::create([
                        'sessao_id' => $sessaoId,
                        'data' => $hoje,
                        'feira_id' => Feira::ativa()->value('id'),
                    ]);
                } catch (UniqueConstraintViolationException) {
                    // Outro pedido em paralelo da mesma sessão já registou a visita — ignorar.
                }
            }
        }

        return $next($request);
    }
}
