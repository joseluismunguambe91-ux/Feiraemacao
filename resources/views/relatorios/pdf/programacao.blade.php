@extends('relatorios.pdf.layout')

@section('titulo', 'Relatório de Programação')

@section('conteudo')
<table>
    <thead>
        <tr><th>Data</th><th>Hora início</th><th>Hora fim</th><th>Atividade</th><th>Local</th><th>Palco</th></tr>
    </thead>
    <tbody>
        @foreach ($itens as $item)
            <tr>
                <td>{{ $item->data->format('d/m/Y') }}</td>
                <td>{{ substr($item->hora_inicio, 0, 5) }}</td>
                <td>{{ substr($item->hora_fim, 0, 5) }}</td>
                <td>{{ $item->atividade->titulo }}</td>
                <td>{{ $item->local }}</td>
                <td>{{ $item->palco ?? '—' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
