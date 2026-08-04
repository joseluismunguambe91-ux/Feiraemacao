@extends('relatorios.pdf.layout')

@section('titulo', 'Relatório de Gastronomia')

@section('conteudo')
<table>
    <thead>
        <tr><th>Nome</th><th>Categoria</th><th>Preço (MT)</th><th>Disponível</th></tr>
    </thead>
    <tbody>
        @foreach ($itens as $item)
            <tr>
                <td>{{ $item->nome }}</td>
                <td>{{ $item->categoria }}</td>
                <td>{{ number_format($item->preco, 2, ',', '.') }}</td>
                <td>{{ $item->disponivel ? 'Sim' : 'Não' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
