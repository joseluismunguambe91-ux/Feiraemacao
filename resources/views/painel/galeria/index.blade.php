@extends('layouts.painel')

@section('titulo', 'Galeria')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Galeria</h1>
    @if ($feira)
        <a href="{{ route('painel.galeria.create') }}" class="btn btn-primary">Novo item</a>
    @endif
</div>

@if (! $feira)
    <div class="alert alert-warning">Seleciona uma edição da feira para gerir a galeria.</div>
@elseif ($itens->isEmpty())
    <p class="text-body-secondary">Ainda não há fotos ou vídeos nesta edição.</p>
@else
    <div class="row g-3">
        @foreach ($itens as $item)
            <div class="col-sm-6 col-lg-4">
                <div class="border rounded p-3 h-100 d-flex flex-column">
                    <span class="badge badge-neutro align-self-start mb-2">{{ $item->tipo === 'foto' ? 'Foto' : 'Vídeo' }}</span>
                    <strong>{{ $item->titulo ?? 'Sem título' }}</strong>
                    <span class="small text-body-secondary">{{ $item->categoria }}</span>
                    <div class="mt-auto pt-3 d-flex gap-2">
                        <a href="{{ route('painel.galeria.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form method="POST" action="{{ route('painel.galeria.destroy', $item) }}" class="confirmar-eliminacao">
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
