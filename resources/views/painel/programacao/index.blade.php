@extends('layouts.painel')

@section('titulo', 'Programação')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Programação</h1>
    @if ($feira)
        <a href="{{ route('painel.atividades.create') }}" class="btn btn-primary">Nova atividade</a>
    @endif
</div>

@if (! $feira)
    <div class="alert alert-warning">Seleciona uma edição da feira para gerir a programação.</div>
@else
    @if ($naoAgendadas->isEmpty() && $itens->isEmpty())
        <div class="alert alert-info">
            Ainda não há nenhuma atividade para agendar. Cria uma em <a href="{{ route('painel.atividades.create') }}">"Nova atividade"</a> (acima) — assim que existir, aparece aqui para lhe atribuíres data, hora e local. Atividades vindas de inscrições aprovadas também aparecem aqui automaticamente.
        </div>
    @endif

    @if ($naoAgendadas->isNotEmpty())
        <div class="border rounded p-3 mb-4">
            <h2 class="h6">Atividades por agendar</h2>
            <ul class="list-unstyled mb-0">
                @foreach ($naoAgendadas as $atividade)
                    <li class="d-flex justify-content-between align-items-center py-1">
                        <span><x-tipo-atividade-label :tipo="$atividade->tipo" /> — {{ $atividade->titulo }}</span>
                        <a href="{{ route('painel.programacao.create', $atividade) }}" class="btn btn-sm btn-primary">Agendar</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($itens->isEmpty() && $naoAgendadas->isNotEmpty())
        <p class="text-body-secondary">Ainda não há nada agendado nesta edição.</p>
    @elseif ($itens->isNotEmpty())
        @foreach ($itens->groupBy(fn ($item) => $item->data->format('Y-m-d')) as $dia => $itensDoDia)
            <h2 class="h6 text-uppercase text-body-secondary mt-4">{{ \Carbon\Carbon::parse($dia)->format('d/m/Y') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Atividade</th>
                            <th>Tipo</th>
                            <th>Local</th>
                            <th>Palco</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($itensDoDia as $item)
                            <tr>
                                <td class="font-monospace">{{ substr($item->hora_inicio, 0, 5) }}–{{ substr($item->hora_fim, 0, 5) }}</td>
                                <td>{{ $item->atividade->titulo }}</td>
                                <td><x-tipo-atividade-label :tipo="$item->atividade->tipo" /></td>
                                <td>{{ $item->local }}</td>
                                <td>{{ $item->palco ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('painel.programacao.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Reorganizar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
@endif
@endsection
