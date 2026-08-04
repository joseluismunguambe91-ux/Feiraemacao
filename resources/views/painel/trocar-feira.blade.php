@extends('layouts.painel')

@section('titulo', 'Trocar de edição')

@section('conteudo')
<h1 class="h3 mb-4">Trocar de edição</h1>

@if ($feiras->isEmpty())
    <p class="text-body-secondary">Ainda não existe nenhuma edição da feira.</p>
@else
    <div class="list-group" style="max-width: 40rem;">
        @foreach ($feiras as $feira)
            <form method="POST" action="{{ route('painel.trocar-feira.store') }}"
                  class="list-group-item d-flex justify-content-between align-items-center">
                @csrf
                <input type="hidden" name="feira_id" value="{{ $feira->id }}">
                <span>
                    <strong>{{ $feira->tema }}</strong>
                    <span class="text-body-secondary small d-block">{{ $feira->data_inicio->format('d/m/Y') }} – {{ $feira->data_fim->format('d/m/Y') }}</span>
                </span>
                <span class="d-flex align-items-center gap-2">
                    <x-estado-feira-badge :estado="$feira->estado" />
                    <button type="submit" class="btn btn-sm btn-primary">Selecionar</button>
                </span>
            </form>
        @endforeach
    </div>
@endif
@endsection
