<?php

namespace App\Http\Middleware;

use App\Services\FeiraContexto;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class DefinirFeiraAtual
{
    public function __construct(private readonly FeiraContexto $feiraContexto)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $feiraAtual = $this->feiraContexto->atual();

        $request->attributes->set('feiraAtual', $feiraAtual);
        View::share('feiraAtual', $feiraAtual);

        return $next($request);
    }
}
