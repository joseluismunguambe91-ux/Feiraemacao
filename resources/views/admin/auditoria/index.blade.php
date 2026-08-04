@extends('layouts.painel')

@section('titulo', 'Auditoria')

@section('conteudo')
<h1 class="h3 mb-4">Auditoria</h1>

<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ $tipoFiltro === 'todas' ? 'active' : '' }}" href="{{ route('admin.auditoria.index') }}">Todas</a>
    </li>
    @foreach ($tiposDisponiveis as $tipo)
        <li class="nav-item">
            <a class="nav-link {{ $tipoFiltro === $tipo ? 'active' : '' }}" href="{{ route('admin.auditoria.index', ['entidade_tipo' => $tipo]) }}">{{ $tipo }}</a>
        </li>
    @endforeach
</ul>

@if ($logs->isEmpty())
    <p class="text-body-secondary">Ainda não há registos de auditoria{{ $tipoFiltro !== 'todas' ? ' para '.$tipoFiltro : '' }}.</p>
@else
    <div class="table-responsive">
        <table class="table align-middle small">
            <thead>
                <tr><th>Data</th><th>Utilizador</th><th>Ação</th><th>Entidade</th><th>IP</th></tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td class="font-monospace">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $log->user->name ?? 'Sistema' }}</td>
                        <td>{{ $log->acao }}</td>
                        <td>{{ $log->entidade_tipo }} #{{ $log->entidade_id }}</td>
                        <td>{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $logs->links() }}
@endif
@endsection
