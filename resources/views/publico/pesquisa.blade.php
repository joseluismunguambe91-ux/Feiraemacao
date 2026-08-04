@extends('layouts.publico')

@section('titulo', 'Pesquisa')

@section('conteudo')
<div class="container py-5">
    <h1 class="h3 mb-4">Pesquisa</h1>

    <form method="GET" action="{{ route('publico.pesquisa') }}" class="mb-4" style="max-width: 26rem;">
        <div class="input-group">
            <input type="search" name="q" class="form-control" value="{{ $termo }}" placeholder="Atividades, pratos, professores, turmas, stands…" autofocus>
            <button type="submit" class="btn btn-primary">Pesquisar</button>
        </div>
    </form>

    @if (! $feira)
        <p class="text-body-secondary">Não há nenhuma edição da feira aberta de momento.</p>
    @elseif ($termo === '')
        <p class="text-body-secondary">Escreve algo para pesquisar.</p>
    @else
        @php
            $totalResultados = collect($resultados)->sum->count();
        @endphp

        @if ($totalResultados === 0)
            <p class="text-body-secondary">Sem resultados para "{{ $termo }}".</p>
        @else
            @if ($resultados['atividades']->isNotEmpty())
                <h2 class="h6 text-uppercase text-body-secondary mt-4">Atividades</h2>
                <ul class="list-unstyled">
                    @foreach ($resultados['atividades'] as $atividade)
                        <li class="py-1"><x-tipo-atividade-label :tipo="$atividade->tipo" /> — {{ $atividade->titulo }}</li>
                    @endforeach
                </ul>
            @endif

            @if ($resultados['gastronomia']->isNotEmpty())
                <h2 class="h6 text-uppercase text-body-secondary mt-4">Gastronomia</h2>
                <ul class="list-unstyled">
                    @foreach ($resultados['gastronomia'] as $item)
                        <li class="py-1">{{ $item->nome }} — {{ number_format($item->preco, 2, ',', '.') }} MT</li>
                    @endforeach
                </ul>
            @endif

            @if ($resultados['expositores']->isNotEmpty())
                <h2 class="h6 text-uppercase text-body-secondary mt-4">Expositores e turmas</h2>
                <ul class="list-unstyled">
                    @foreach ($resultados['expositores'] as $expositor)
                        <li class="py-1">{{ $expositor->turma }} — {{ $expositor->categoria }} ({{ $expositor->professor->name }})</li>
                    @endforeach
                </ul>
            @endif

            @if ($resultados['stands']->isNotEmpty())
                <h2 class="h6 text-uppercase text-body-secondary mt-4">Stands</h2>
                <ul class="list-unstyled">
                    @foreach ($resultados['stands'] as $stand)
                        <li class="py-1"><a href="{{ route('publico.stand', $stand->qr_token) }}">Stand {{ $stand->numero }} — {{ $stand->localizacao }}</a></li>
                    @endforeach
                </ul>
            @endif

            @if ($resultados['professores']->isNotEmpty())
                <h2 class="h6 text-uppercase text-body-secondary mt-4">Professores</h2>
                <ul class="list-unstyled">
                    @foreach ($resultados['professores'] as $professor)
                        <li class="py-1">{{ $professor->name }}</li>
                    @endforeach
                </ul>
            @endif
        @endif
    @endif
</div>
@endsection
