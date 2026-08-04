@extends('layouts.painel')

@section('titulo', 'QR Code — Stand '.$stand->numero)

@section('conteudo')
<h1 class="h3 mb-4">QR Code — Stand {{ $stand->numero }}</h1>

<div class="border rounded p-4 d-inline-block bg-white">
    {!! QrCode::format('svg')->size(220)->generate($urlPublica) !!}
</div>

<p class="mt-3 text-body-secondary small">
    Aponta para <code>{{ $urlPublica }}</code> — a página pública do stand (mapa da feira) é construída na Fase 8.6.
</p>

<a href="{{ route('painel.stands.index') }}" class="btn btn-outline-secondary">Voltar</a>
@endsection
