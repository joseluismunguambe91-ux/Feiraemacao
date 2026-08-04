@extends('layouts.publico')

@section('titulo', 'Gastronomia')

@section('conteudo')
<div class="container py-5">
    <h1 class="h3 mb-4">Gastronomia</h1>

    @if (! $feira)
        <p class="text-body-secondary">Não há nenhuma edição da feira aberta de momento.</p>
    @elseif ($itens->isEmpty())
        <p class="text-body-secondary">O cardápio ainda vai ser divulgado.</p>
    @else
        <div class="row g-3">
            @foreach ($itens as $item)
                <div class="col-sm-6 col-lg-4">
                    <div class="border rounded h-100 overflow-hidden {{ ! $item->disponivel ? 'opacity-50' : '' }}">
                        @if ($item->foto_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($item->foto_path) }}" alt="{{ $item->nome }}" class="w-100" style="height: 140px; object-fit: cover;">
                        @endif
                        <div class="p-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $item->nome }}</strong>
                                <span class="font-monospace">{{ number_format($item->preco, 2, ',', '.') }} MT</span>
                            </div>
                            <span class="text-body-secondary small d-block">{{ $item->categoria }}</span>
                            <x-gastronomia-origem :item="$item" />
                            @if ($item->descricao)
                                <p class="small mt-2 mb-0">{{ $item->descricao }}</p>
                            @endif
                            @unless ($item->disponivel)
                                <span class="badge badge-neutro mt-2">Indisponível</span>
                            @endunless
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
