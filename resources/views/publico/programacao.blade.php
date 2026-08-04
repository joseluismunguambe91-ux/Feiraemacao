@extends('layouts.publico')

@section('titulo', 'Programação')

@section('conteudo')
<div class="container py-5">
    <h1 class="h3 mb-4">Programação</h1>

    @if (! $feira)
        <p class="text-body-secondary">Não há nenhuma edição da feira aberta de momento.</p>
    @elseif ($itens->isEmpty())
        <p class="text-body-secondary">A programação ainda vai ser divulgada — volta em breve.</p>
    @else
        @foreach ($itens->groupBy(fn ($item) => $item->data->format('Y-m-d')) as $dia => $itensDoDia)
            <h2 class="h6 text-uppercase text-body-secondary mt-4">{{ \Carbon\Carbon::parse($dia)->format('d/m/Y') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr><th>Hora</th><th>Atividade</th><th>Tipo</th><th>Local</th><th>Palco</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($itensDoDia as $item)
                            <tr>
                                <td class="font-monospace">{{ substr($item->hora_inicio, 0, 5) }}–{{ substr($item->hora_fim, 0, 5) }}</td>
                                <td>{{ $item->atividade->titulo }}</td>
                                <td><x-tipo-atividade-label :tipo="$item->atividade->tipo" /></td>
                                <td>{{ $item->local }}</td>
                                <td>{{ $item->palco ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
</div>
@endsection
