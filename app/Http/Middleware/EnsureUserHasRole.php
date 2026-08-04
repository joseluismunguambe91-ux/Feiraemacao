<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware "role:administrador,comissao" — controlo de permissões da
 * Etapa 4/7. Aceita uma lista de papéis; passa se o utilizador tiver
 * qualquer um deles (User::hasAnyRole).
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$papeis): Response
    {
        if (! $request->user() || ! $request->user()->hasAnyRole($papeis)) {
            abort(403, 'Não tens permissão para aceder a esta área.');
        }

        return $next($request);
    }
}
