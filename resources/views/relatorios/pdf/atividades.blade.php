@extends('relatorios.pdf.layout')

@section('titulo', 'Relatório de Atividades')

@section('conteudo')
<table>
    <thead>
        <tr><th>Título</th><th>Tipo</th><th>Estado</th><th>Participantes previstos</th></tr>
    </thead>
    <tbody>
        @foreach ($itens as $item)
            <tr>
                <td>{{ $item->titulo }}</td>
                <td>{{ $item->tipo }}</td>
                <td>{{ $item->estado }}</td>
                <td>{{ $item->participantes_previstos }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
