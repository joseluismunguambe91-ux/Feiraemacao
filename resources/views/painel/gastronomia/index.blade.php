@extends('layouts.painel')

@section('titulo', 'Gastronomia')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Gastronomia</h1>
    @if ($feira)
        <a href="{{ route('painel.gastronomia.create') }}" class="btn btn-primary">Novo item</a>
    @endif
</div>

@if (! $feira)
    <div class="alert alert-warning">Seleciona uma edição da feira para gerir o cardápio.</div>
@elseif ($itens->isEmpty())
    <p class="text-body-secondary">Ainda não existem itens de gastronomia nesta edição.</p>
@else
    <div class="row g-3">
        @foreach ($itens as $item)
            <div class="col-sm-6 col-lg-4">
                <div class="border rounded p-3 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $item->nome }}</strong>
                        <span class="font-monospace">{{ number_format($item->preco, 2, ',', '.') }} MT</span>
                    </div>
                    <span class="text-body-secondary small">{{ $item->categoria }}</span>
                    <x-gastronomia-origem :item="$item" />
                    <span class="badge {{ $item->disponivel ? 'badge-capim' : 'badge-neutro' }} mt-2 align-self-start">
                        {{ $item->disponivel ? 'Disponível' : 'Indisponível' }}
                    </span>
                    <div class="mt-auto pt-3 d-flex gap-2">
                        <a href="{{ route('painel.gastronomia.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form method="POST" action="{{ route('painel.gastronomia.destroy', $item) }}" class="confirmar-eliminacao">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{ $itens->links() }}
@endif
@endsection
