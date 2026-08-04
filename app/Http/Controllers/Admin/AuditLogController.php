<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Consulta de auditoria (Etapa 4, RC — só o Administrador). Os registos
 * já são gravados desde a Fase 8.1 pelo trait Auditavel; este módulo só
 * acrescenta a interface de consulta.
 */
class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $tipoFiltro = $request->query('entidade_tipo', 'todas');

        $logs = AuditLog::with(['user', 'feira'])
            ->when($tipoFiltro !== 'todas', fn ($query) => $query->where('entidade_tipo', $tipoFiltro))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $tiposDisponiveis = AuditLog::query()->distinct()->pluck('entidade_tipo');

        return view('admin.auditoria.index', compact('logs', 'tiposDisponiveis', 'tipoFiltro'));
    }
}
