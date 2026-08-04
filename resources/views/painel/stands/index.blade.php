@extends('layouts.painel')

@section('titulo', 'Stands')

@section('conteudo')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Stands</h1>
    @if ($feira)
        <a href="{{ route('painel.stands.create') }}" class="btn btn-primary">Novo stand</a>
    @endif
</div>

@if (! $feira)
    <div class="alert alert-warning">Seleciona uma edição da feira para gerir os stands.</div>
@elseif ($stands->isEmpty())
    <p class="text-body-secondary">Ainda não existem stands nesta edição.</p>
@else
    <div class="stand-grid">
        @foreach ($stands as $stand)
            @php
                $corEstado = match ($stand->estado) {
                    'ocupado' => 'badge-capim',
                    'reservado' => 'badge-ambar',
                    'inativo' => 'badge-neutro',
                    default => 'badge-neutro',
                };
            @endphp
            <div class="border rounded p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <strong class="fs-5">{{ $stand->numero }}</strong>
                    <span class="badge {{ $corEstado }}">{{ ucfirst($stand->estado) }}</span>
                </div>
                <div class="small text-body-secondary">{{ $stand->localizacao }}</div>
                <div class="small text-body-secondary">{{ $stand->expositor?->turma ? 'Expositor: '.$stand->expositor->turma : 'Sem expositor atribuído' }}</div>
                <div class="mt-3 d-flex gap-2 flex-wrap">
                    <a href="{{ route('painel.stands.edit', $stand) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                    <a href="{{ route('painel.stands.qr', $stand) }}" class="btn btn-sm btn-outline-secondary">QR Code</a>
                    <form method="POST" action="{{ route('painel.stands.destroy', $stand) }}" class="confirmar-eliminacao">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-3">{{ $stands->links() }}</div>
@endif
@endsection
