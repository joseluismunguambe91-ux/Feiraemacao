@extends('layouts.publico')

@section('titulo', 'Stand '.$stand->numero)

@section('conteudo')
<div class="container py-5" style="max-width: 34rem;">
    <a href="{{ route('publico.mapa') }}" class="small">← Voltar ao mapa</a>
    <h1 class="h3 mt-2 mb-1">Stand {{ $stand->numero }}</h1>
    <p class="text-body-secondary mb-4">{{ $stand->localizacao }}</p>

    @if ($stand->expositor)
        <div class="border rounded p-3">
            <strong class="fs-5">{{ $stand->expositor->turma }}</strong>
            <span class="text-body-secondary small d-block">{{ $stand->expositor->categoria }}</span>
            @if ($stand->expositor->descricao)
                <p class="mt-3 mb-0">{{ $stand->expositor->descricao }}</p>
            @endif

            @if ($stand->expositor->fotos->isNotEmpty())
                <div class="d-flex gap-2 flex-wrap mt-3">
                    @foreach ($stand->expositor->fotos as $foto)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($foto->path) }}" target="_blank" class="small">Foto #{{ $loop->iteration }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <p class="text-body-secondary">Este stand ainda não tem expositor atribuído.</p>
    @endif
</div>
@endsection
