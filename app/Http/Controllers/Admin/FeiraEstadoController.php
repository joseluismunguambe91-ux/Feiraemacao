<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feira;
use App\Services\FeiraEstadoTransicao;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class FeiraEstadoController extends Controller
{
    public function reverter(Feira $feira, FeiraEstadoTransicao $transicao): RedirectResponse
    {
        try {
            $transicao->reverter($feira);
        } catch (RuntimeException $e) {
            return back()->with('erro', $e->getMessage());
        }

        return back()->with('sucesso', "Edição voltou para \"{$feira->fresh()->estado}\".");
    }
}
