@extends('relatorios.pdf.layout')

@section('titulo', 'Relatório de Participantes')

@section('conteudo')
<table>
    <thead>
        <tr><th>Nome</th><th>Papel</th><th>Classe</th><th>Turma</th><th>O que vai apresentar</th><th>Banca</th></tr>
    </thead>
    <tbody>
        @foreach ($itens as $item)
            <tr>
                <td>{{ $item->nome }}</td>
                <td>{{ $item->papel }}</td>
                <td>{{ $item->classe ?? '—' }}</td>
                <td>{{ $item->turma ?? '—' }}</td>
                <td>{{ $item->funcao }}</td>
                <td>{{ $item->banca ?? '—' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
