<?php

namespace App\Http\Controllers;

use App\Models\Feira;
use Illuminate\Support\Facades\Gate;

abstract class Controller
{
    /**
     * RC02 (Etapa 4): ninguém edita conteúdo operacional de uma edição
     * arquivada, nem o Administrador. Partilhado por todos os Controllers
     * que escrevem em módulos dependentes de uma feira (Expositores,
     * Stands, Atividades, Gastronomia, Galeria, Patrocinadores).
     */
    protected function assegurarFeiraEditavel(Feira $feira): void
    {
        abort_if(Gate::denies('feira-editavel', $feira), 403, 'Esta edição está arquivada — não é possível editar.');
    }
}
