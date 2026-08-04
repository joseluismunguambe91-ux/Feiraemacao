@extends('layouts.painel')

@section('titulo', 'Inscrições')

@section('conteudo')
<h1 class="h3 mb-4">Inscrições</h1>

@if (! $feira)
    <div class="alert alert-warning">Seleciona uma edição da feira para rever as inscrições.</div>
@else
    <ul class="nav nav-pills mb-4">
        @foreach (['pendente' => 'Pendentes', 'aprovada' => 'Aprovadas', 'rejeitada' => 'Rejeitadas', 'todas' => 'Todas'] as $valor => $rotulo)
            <li class="nav-item">
                <a class="nav-link {{ $estadoFiltro === $valor ? 'active' : '' }}"
                   href="{{ route('painel.inscricoes.index', ['estado' => $valor]) }}">{{ $rotulo }}</a>
            </li>
        @endforeach
    </ul>

    @if ($inscricoes->isEmpty())
        <p class="text-body-secondary">Nenhuma inscrição encontrada neste filtro.</p>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Professor</th>
                        <th>Tipo de atividade</th>
                        <th>Participantes</th>
                        <th>Estado</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inscricoes as $inscricao)
                        <tr>
                            <td>{{ $inscricao->professor->name }}</td>
                            <td><x-tipo-atividade-label :tipo="$inscricao->tipo_atividade" /></td>
                            <td>{{ $inscricao->numero_participantes }}</td>
                            <td><x-estado-inscricao-badge :estado="$inscricao->estado" /></td>
                            <td class="text-end">
                                <a href="{{ route('painel.inscricoes.show', $inscricao) }}" class="btn btn-sm btn-outline-secondary">Rever</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $inscricoes->links() }}
    @endif
@endif
@endsection
