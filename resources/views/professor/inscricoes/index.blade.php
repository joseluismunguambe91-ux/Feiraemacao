@extends('layouts.professor')

@section('titulo', 'As minhas inscrições')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-2">
    <h1 class="h3 mb-0">As minhas inscrições</h1>
    <a href="{{ route('professor.inscricoes.create') }}" class="btn btn-primary">Nova inscrição</a>
</div>
<p class="text-body-secondary mb-4">
    Aqui vês tudo o que já pediste para fazer na feira, e se já foi aceite.
    <span class="badge badge-ambar">Pendente</span> significa que ainda estão a decidir;
    <span class="badge badge-capim">Aprovada</span> significa que já podes preparar tudo;
    <span class="badge badge-tijolo">Rejeitada</span> significa que não foi desta vez — lê o comentário para saber porquê.
</p>

@if ($inscricoes->isEmpty())
    <p class="text-body-secondary">Ainda não te inscreveste em nada. Clica em "Nova inscrição" para começares!</p>
@else
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Tipo de atividade</th>
                    <th>Turma</th>
                    <th>Estado</th>
                    <th>Comentário da Comissão</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inscricoes as $inscricao)
                    <tr>
                        <td><x-tipo-atividade-label :tipo="$inscricao->tipo_atividade" /></td>
                        <td>{{ $inscricao->turma ?? '—' }}</td>
                        <td><x-estado-inscricao-badge :estado="$inscricao->estado" /></td>
                        <td class="small text-body-secondary">{{ $inscricao->comentario_avaliacao ?? '—' }}</td>
                        <td class="text-end">
                            @if ($inscricao->estado === 'pendente')
                                <a href="{{ route('professor.inscricoes.edit', $inscricao) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $inscricoes->links() }}
@endif
@endsection
