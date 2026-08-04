<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Feira;
use App\Services\FeiraEstadoTransicao;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class FeiraEstadoController extends Controller
{
    public function avancar(Feira $feira, FeiraEstadoTransicao $transicao): RedirectResponse
    {
        try {
            $transicao->avancar($feira);
        } catch (RuntimeException $e) {
            return back()->with('erro', $e->getMessage());
        }

        return back()->with('sucesso', "Edição avançou para \"{$feira->fresh()->estado}\".");
    }
}
