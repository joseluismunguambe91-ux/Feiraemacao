@extends('layouts.publico')

@section('titulo', 'Sobre a Feira')

@section('conteudo')
<div class="container py-5" style="max-width: 40rem;">
    <h1 class="h3 mb-4">Sobre a Feira</h1>

    @if ($feira)
        <p>{{ $feira->descricao ?? 'Ainda não há uma descrição publicada para esta edição.' }}</p>
        <dl class="row mt-4">
            <dt class="col-sm-4">Datas</dt>
            <dd class="col-sm-8">{{ $feira->data_inicio->format('d/m/Y') }} – {{ $feira->data_fim->format('d/m/Y') }}</dd>
            <dt class="col-sm-4">Horário</dt>
            <dd class="col-sm-8">{{ substr($feira->hora_abertura, 0, 5) }}–{{ substr($feira->hora_encerramento, 0, 5) }}</dd>
            <dt class="col-sm-4">Local</dt>
            <dd class="col-sm-8">{{ $feira->local }}</dd>
        </dl>
        @if ($feira->regulamento_path)
            <a href="{{ \Illuminate\Support\Facades\Storage::url($feira->regulamento_path) }}" target="_blank" class="btn btn-outline-secondary">Ver regulamento (PDF)</a>
        @endif
    @else
        <p class="text-body-secondary">Não há nenhuma edição da feira aberta de momento.</p>
    @endif
</div>
@endsection
