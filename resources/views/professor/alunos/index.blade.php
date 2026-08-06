@extends('layouts.professor')

@section('titulo', 'Os meus alunos')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Os meus alunos</h1>
    <a href="{{ route('professor.alunos.create') }}" class="btn btn-primary">Novo aluno</a>
</div>

@if ($alunos->isEmpty())
    <p class="text-body-secondary">Ainda não registaste nenhum aluno. Regista aqui os alunos da tua turma para os poderes escolher ao submeter uma inscrição.</p>
@else
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Classe</th>
                    <th>Turma</th>
                    <th>Conta própria</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alunos as $aluno)
                    <tr>
                        <td>{{ $aluno->nome }}</td>
                        <td>{{ $aluno->classe ?? '—' }}</td>
                        <td>{{ $aluno->turma }}</td>
                        <td>{{ $aluno->user?->name ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('professor.alunos.edit', $aluno) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form method="POST" action="{{ route('professor.alunos.destroy', $aluno) }}" class="d-inline confirmar-eliminacao">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $alunos->links() }}
@endif
@endsection
