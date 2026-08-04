@extends('relatorios.pdf.layout')

@section('titulo', 'Relatório de Expositores')

@section('conteudo')
<table>
    <thead>
        <tr><th>Turma</th><th>Categoria</th><th>Professor</th><th>Stand</th><th>Estado</th></tr>
    </thead>
    <tbody>
        @foreach ($itens as $item)
            <tr>
                <td>{{ $item->turma }}</td>
                <td>{{ $item->categoria }}</td>
                <td>{{ $item->professor->name }}</td>
                <td>{{ $item->stand->numero ?? '—' }}</td>
                <td>{{ $item->estado }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
