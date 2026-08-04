@extends('layouts.publico')

@section('titulo', 'Galeria')

@section('conteudo')
<div class="container py-5">
    <h1 class="h3 mb-4">Galeria</h1>

    @if (! $feira)
        <p class="text-body-secondary">Não há nenhuma edição da feira aberta de momento.</p>
    @elseif ($itens->isEmpty())
        <p class="text-body-secondary">Ainda não há fotos ou vídeos publicados desta edição.</p>
    @else
        <div class="row g-3">
            @foreach ($itens as $item)
                <div class="col-sm-6 col-lg-4">
                    <div class="border rounded overflow-hidden h-100">
                        @if ($item->tipo === 'foto')
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($item->path_ou_url) }}" alt="{{ $item->titulo }}" class="w-100" style="aspect-ratio: 4/3; object-fit: cover;">
                        @else
                            <a href="{{ $item->path_ou_url }}" target="_blank" class="d-block p-3 text-decoration-none">Ver vídeo: {{ $item->titulo ?? 'sem título' }}</a>
                        @endif
                        @if ($item->titulo)
                            <div class="p-2 small text-body-secondary">{{ $item->titulo }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
