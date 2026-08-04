@extends('layouts.painel')

@section('titulo', 'Mensagens de contacto')

@section('conteudo')
<h1 class="h3 mb-4">Mensagens de contacto</h1>

@if (! $feira)
    <div class="alert alert-warning">Seleciona uma edição da feira para ver as mensagens recebidas.</div>
@elseif ($mensagens->isEmpty())
    <p class="text-body-secondary">Ainda não chegou nenhuma mensagem através do formulário de contacto.</p>
@else
    <div class="d-flex flex-column gap-3">
        @foreach ($mensagens as $mensagem)
            <div class="border rounded p-3 {{ $mensagem->lida ? '' : 'border-warning' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>{{ $mensagem->nome }}</strong>
                        <span class="text-body-secondary small">&lt;{{ $mensagem->email }}&gt;</span>
                        @unless ($mensagem->lida)
                            <span class="badge badge-ambar">Nova</span>
                        @endunless
                    </div>
                    <span class="small text-body-secondary">{{ $mensagem->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if ($mensagem->assunto)
                    <p class="fw-semibold mb-1 mt-2">{{ $mensagem->assunto }}</p>
                @endif
                <p class="mb-2">{{ $mensagem->mensagem }}</p>
                @unless ($mensagem->lida)
                    <form method="POST" action="{{ route('painel.mensagens-contacto.marcar-lida', $mensagem) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Marcar como lida</button>
                    </form>
                @endunless
            </div>
        @endforeach
    </div>
    <div class="mt-3">{{ $mensagens->links() }}</div>
@endif
@endsection
