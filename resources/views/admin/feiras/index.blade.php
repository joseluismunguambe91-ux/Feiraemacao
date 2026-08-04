@extends('layouts.painel')

@section('titulo', 'Gestão da Feira')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edições da Feira</h1>
    <a href="{{ route('admin.feiras.create') }}" class="btn btn-primary">Nova edição</a>
</div>

<div class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Tema</th>
                <th>Datas</th>
                <th>Estado</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($feiras as $feira)
                <tr>
                    <td>{{ $feira->tema }}</td>
                    <td>{{ $feira->data_inicio->format('d/m/Y') }} – {{ $feira->data_fim->format('d/m/Y') }}</td>
                    <td><x-estado-feira-badge :estado="$feira->estado" /></td>
                    <td class="text-end">
                        <a href="{{ route('admin.feiras.edit', $feira) }}" class="btn btn-sm btn-outline-secondary">Editar</a>

                        @if ($feira->estado !== 'arquivada')
                            <form method="POST" action="{{ route('painel.feiras.avancar-estado', $feira) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Avançar estado</button>
                            </form>
                        @endif

                        @if ($feira->estado !== 'rascunho')
                            <form method="POST" action="{{ route('admin.feiras.reverter-estado', $feira) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Reverter estado</button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.feiras.destroy', $feira) }}" class="d-inline confirmar-eliminacao">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-body-secondary">Ainda não existe nenhuma edição da feira.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $feiras->links() }}

<a href="{{ route('painel.dashboard') }}" class="small">← Voltar ao Painel</a>
@endsection
