@extends('relatorios.pdf.layout')

@section('titulo', 'Relatório de Visitantes')

@section('conteudo')
<table>
    <thead>
        <tr><th>Data</th><th>Visitantes</th></tr>
    </thead>
    <tbody>
        @foreach ($itens as $item)
            <tr>
                <td>{{ $item->data->format('d/m/Y') }}</td>
                <td>{{ $item->total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<p style="margin-top: 1rem;">Total: {{ $itens->sum('total') }} visitantes.</p>
@endsection
