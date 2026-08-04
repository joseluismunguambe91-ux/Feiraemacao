@extends('layouts.painel')

@section('titulo', 'Atividades')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Atividades</h1>
    @if ($feira)
        <a href="{{ route('painel.atividades.create') }}" class="btn btn-primary">Nova atividade</a>
    @endif
</div>

@if (! $feira)
    <div class="alert alert-warning">Seleciona uma edição da feira para gerir as atividades.</div>
@elseif ($atividades->isEmpty())
    <p class="text-body-secondary">Ainda não existem atividades nesta edição.</p>
@else
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Origem</th>
                    <th>Estado</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($atividades as $atividade)
                    <tr>
                        <td>{{ $atividade->titulo }}</td>
                        <td><x-tipo-atividade-label :tipo="$atividade->tipo" /></td>
                        <td>
                            <span class="badge {{ $atividade->inscricao_id ? 'badge-ambar' : 'badge-neutro' }}">
                                {{ $atividade->inscricao_id ? 'Inscrição' : 'Direta' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $atividade->estado === 'confirmada' ? 'badge-capim' : ($atividade->estado === 'cancelada' ? 'badge-tijolo' : 'badge-neutro') }}">
                                {{ ucfirst($atividade->estado) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('painel.atividades.edit', $atividade) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form method="POST" action="{{ route('painel.atividades.destroy', $atividade) }}" class="d-inline confirmar-eliminacao">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $atividades->links() }}
@endif
@endsection
