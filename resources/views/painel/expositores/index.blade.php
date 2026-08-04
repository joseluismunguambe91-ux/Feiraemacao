@extends('layouts.painel')

@section('titulo', 'Expositores')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Expositores</h1>
    @if ($feira)
        <a href="{{ route('painel.expositores.create') }}" class="btn btn-primary">Novo expositor</a>
    @endif
</div>

@if (! $feira)
    <div class="alert alert-warning">Seleciona uma edição da feira para gerir os expositores.</div>
@elseif ($expositores->isEmpty())
    <p class="text-body-secondary">Ainda não existem expositores nesta edição.</p>
@else
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Turma</th>
                    <th>Professor</th>
                    <th>Categoria</th>
                    <th>Stand</th>
                    <th>Estado</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expositores as $expositor)
                    <tr>
                        <td>{{ $expositor->turma }}</td>
                        <td>{{ $expositor->professor->name }}</td>
                        <td>{{ $expositor->categoria }}</td>
                        <td>{{ $expositor->stand?->numero ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $expositor->estado === 'ativo' ? 'badge-capim' : ($expositor->estado === 'inativo' ? 'badge-neutro' : 'badge-ambar') }}">
                                {{ ucfirst($expositor->estado) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('painel.expositores.edit', $expositor) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form method="POST" action="{{ route('painel.expositores.destroy', $expositor) }}" class="d-inline confirmar-eliminacao">
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
    {{ $expositores->links() }}
@endif
@endsection
