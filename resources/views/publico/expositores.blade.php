@extends('layouts.publico')

@section('titulo', 'Expositores')

@section('conteudo')
<div class="container py-5">
    <h1 class="h3 mb-4">Expositores</h1>

    @if (! $feira)
        <p class="text-body-secondary">Não há nenhuma edição da feira aberta de momento.</p>
    @elseif ($expositores->isEmpty())
        <p class="text-body-secondary">Ainda não há expositores confirmados.</p>
    @else
        <div class="row g-3">
            @foreach ($expositores as $expositor)
                <div class="col-sm-6 col-lg-4">
                    <div class="border rounded p-3 h-100">
                        <strong>{{ $expositor->turma }}</strong>
                        <span class="text-body-secondary small d-block">{{ $expositor->categoria }}</span>
                        @if ($expositor->descricao)
                            <p class="small mt-2 mb-2">{{ str($expositor->descricao)->limit(120) }}</p>
                        @endif
                        @if ($expositor->stand)
                            <a href="{{ route('publico.stand', $expositor->stand->qr_token) }}" class="small">Stand {{ $expositor->stand->numero }} →</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
