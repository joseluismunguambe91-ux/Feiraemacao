@extends('layouts.painel')

@section('titulo', 'Utilizadores')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Utilizadores</h1>
    <a href="{{ route('admin.utilizadores.create') }}" class="btn btn-primary">Novo utilizador</a>
</div>

<div class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr><th>Nome</th><th>Email</th><th>Papéis</th><th>Estado</th><th class="text-end">Ações</th></tr>
        </thead>
        <tbody>
            @foreach ($utilizadores as $utilizador)
                <tr>
                    <td>{{ $utilizador->name }}</td>
                    <td>{{ $utilizador->email }}</td>
                    <td>
                        @foreach ($utilizador->roles as $papel)
                            <span class="badge badge-neutro">{{ $papel->nome }}</span>
                        @endforeach
                    </td>
                    <td>
                        <span class="badge {{ $utilizador->ativo ? 'badge-capim' : 'badge-tijolo' }}">
                            {{ $utilizador->ativo ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.utilizadores.edit', $utilizador) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        @unless ($utilizador->id === auth()->id())
                            <form method="POST" action="{{ route('admin.utilizadores.destroy', $utilizador) }}" class="d-inline confirmar-eliminacao">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $utilizadores->links() }}
@endsection
