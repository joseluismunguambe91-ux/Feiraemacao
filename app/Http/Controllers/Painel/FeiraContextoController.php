<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Feira;
use App\Services\FeiraContexto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeiraContextoController extends Controller
{
    public function index(): View
    {
        $feiras = Feira::orderByDesc('data_inicio')->get();

        return view('painel.trocar-feira', compact('feiras'));
    }

    public function store(Request $request, FeiraContexto $contexto): RedirectResponse
    {
        $request->validate(['feira_id' => ['required', 'exists:feiras,id']]);

        $feira = Feira::findOrFail($request->integer('feira_id'));
        $contexto->definir($feira);

        return redirect()->route('painel.dashboard')->with('sucesso', "A trabalhar agora em \"{$feira->tema}\".");
    }
}
