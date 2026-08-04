@extends('layouts.publico')

@section('titulo', 'Mapa')

@section('conteudo')
<div class="container py-5">
    <h1 class="h3 mb-4">Mapa</h1>

    @if (! $feira)
        <p class="text-body-secondary">Não há nenhuma edição da feira aberta de momento.</p>
    @elseif ($stands->isEmpty())
        <p class="text-body-secondary">Ainda não há stands confirmados.</p>
    @else
        <div class="stand-grid">
            @foreach ($stands as $stand)
                <a href="{{ route('publico.stand', $stand->qr_token) }}" class="border rounded p-3 text-decoration-none text-body">
                    <strong class="fs-5 d-block">{{ $stand->numero }}</strong>
                    <span class="small text-body-secondary d-block">{{ $stand->localizacao }}</span>
                    <span class="small d-block mt-1">{{ $stand->expositor->turma }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
