@extends('layouts.painel')

@section('titulo', 'Patrocinadores')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Patrocinadores</h1>
    @if ($feira)
        <a href="{{ route('painel.patrocinadores.create') }}" class="btn btn-primary">Novo patrocinador</a>
    @endif
</div>

@if (! $feira)
    <div class="alert alert-warning">Seleciona uma edição da feira para gerir os patrocinadores.</div>
@elseif ($patrocinadores->isEmpty())
    <p class="text-body-secondary">Ainda não há patrocinadores nesta edição.</p>
@else
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Logótipo</th><th>Nome</th><th>Nível</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
                @foreach ($patrocinadores as $patrocinador)
                    <tr>
                        <td><img src="{{ \Illuminate\Support\Facades\Storage::url($patrocinador->logotipo_path) }}" alt="" style="height: 2rem;"></td>
                        <td>{{ $patrocinador->nome }}</td>
                        <td>{{ $patrocinador->nivel ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('painel.patrocinadores.edit', $patrocinador) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form method="POST" action="{{ route('painel.patrocinadores.destroy', $patrocinador) }}" class="d-inline confirmar-eliminacao">
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
    {{ $patrocinadores->links() }}
@endif
@endsection
