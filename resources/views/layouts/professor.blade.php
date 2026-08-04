<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'As minhas inscrições') — Feira em Ação</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>
<div class="d-flex flex-column flex-lg-row">
    <aside class="painel-sidebar p-3">
        <span class="app-logo fs-5 mb-4 d-block">Feira em Ação</span>
        <nav class="nav flex-column gap-1">
            <a class="nav-link px-2 py-1 {{ request()->is('professor/inscricoes*') ? 'active' : '' }}" href="{{ route('professor.inscricoes.index') }}">As minhas inscrições</a>
            @if (auth()->user()->hasRole('professor'))
                <a class="nav-link px-2 py-1 {{ request()->is('professor/alunos*') ? 'active' : '' }}" href="{{ route('professor.alunos.index') }}">Os meus alunos</a>
            @endif
        </nav>
    </aside>
    <div class="flex-fill min-vw-0">
        <header class="d-flex justify-content-between align-items-center border-bottom px-3 py-2">
            <span class="fw-semibold">{{ auth()->user()->name }}</span>
            <div class="d-flex align-items-center gap-2">
                @include('partials.notificacoes-sino')
                <form method="POST" action="{{ route('logout') }}" class="mb-0">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">Sair</button>
                </form>
            </div>
        </header>
        <main class="p-3 p-md-4">
            @if (session('sucesso'))
                <div class="alert alert-success">{{ session('sucesso') }}</div>
            @endif
            @if (session('erro'))
                <div class="alert alert-danger">{{ session('erro') }}</div>
            @endif

            @yield('conteudo')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
