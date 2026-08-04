@php
    $notificacoesNaoLidas = auth()->user()->unreadNotifications()->limit(5)->get();
    $totalNaoLidas = auth()->user()->unreadNotifications()->count();
@endphp
<div class="dropdown">
    <button class="btn btn-sm btn-outline-secondary position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        Notificações
        @if ($totalNaoLidas > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $totalNaoLidas }}</span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 20rem;">
        @forelse ($notificacoesNaoLidas as $notificacao)
            <form method="POST" action="{{ route('notificacoes.marcar-lida', $notificacao->id) }}" class="mb-1">
                @csrf
                <button type="submit" class="dropdown-item small text-wrap border-0 bg-transparent text-start p-2">
                    <strong class="d-block">{{ $notificacao->data['titulo'] }}</strong>
                    {{ $notificacao->data['mensagem'] }}
                </button>
            </form>
        @empty
            <span class="dropdown-item-text small text-body-secondary">Sem notificações novas.</span>
        @endforelse
    </div>
</div>
