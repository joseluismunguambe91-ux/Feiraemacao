<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Painel') — Feira em Ação</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>
<div class="d-flex flex-column flex-lg-row">
    <aside class="painel-sidebar p-3">
        <span class="app-logo fs-5 mb-4 d-block">Feira em Ação</span>
        <nav class="nav flex-column gap-1">
            <a class="nav-link px-2 py-1 {{ request()->is('painel') ? 'active' : '' }}" href="{{ route('painel.dashboard') }}">Dashboard</a>
            @if (auth()->user()->hasRole('administrador'))
                <a class="nav-link px-2 py-1 {{ request()->is('admin/feiras*') ? 'active' : '' }}" href="{{ route('admin.feiras.index') }}">Gestão da Feira</a>
                <a class="nav-link px-2 py-1 {{ request()->is('admin/utilizadores*') ? 'active' : '' }}" href="{{ route('admin.utilizadores.index') }}">Utilizadores</a>
            @endif
            <a class="nav-link px-2 py-1 {{ request()->is('painel/inscricoes*') ? 'active' : '' }}" href="{{ route('painel.inscricoes.index') }}">Inscrições</a>
            <a class="nav-link px-2 py-1 {{ request()->is('painel/expositores*') ? 'active' : '' }}" href="{{ route('painel.expositores.index') }}">Expositores</a>
            <a class="nav-link px-2 py-1 {{ request()->is('painel/stands*') ? 'active' : '' }}" href="{{ route('painel.stands.index') }}">Stands</a>
            <a class="nav-link px-2 py-1 {{ request()->is('painel/atividades*') ? 'active' : '' }}" href="{{ route('painel.atividades.index') }}">Atividades</a>
            <a class="nav-link px-2 py-1 {{ request()->is('painel/gastronomia*') ? 'active' : '' }}" href="{{ route('painel.gastronomia.index') }}">Gastronomia</a>
            <a class="nav-link px-2 py-1 {{ request()->is('painel/programacao*') ? 'active' : '' }}" href="{{ route('painel.programacao.index') }}">Programação</a>
            <a class="nav-link px-2 py-1 {{ request()->is('painel/galeria*') ? 'active' : '' }}" href="{{ route('painel.galeria.index') }}">Galeria</a>
            <a class="nav-link px-2 py-1 {{ request()->is('painel/patrocinadores*') ? 'active' : '' }}" href="{{ route('painel.patrocinadores.index') }}">Patrocinadores</a>
            <a class="nav-link px-2 py-1 {{ request()->is('painel/mensagens-contacto*') ? 'active' : '' }}" href="{{ route('painel.mensagens-contacto.index') }}">Mensagens de contacto</a>
            <a class="nav-link px-2 py-1 {{ request()->is('painel/relatorios*') ? 'active' : '' }}" href="{{ route('painel.relatorios.index') }}">Relatórios</a>
            @if (auth()->user()->hasRole('administrador'))
                <a class="nav-link px-2 py-1 {{ request()->is('admin/auditoria*') ? 'active' : '' }}" href="{{ route('admin.auditoria.index') }}">Auditoria</a>
            @endif
        </nav>
    </aside>
    <div class="flex-fill min-vw-0">
        <header class="d-flex justify-content-between align-items-center border-bottom px-3 py-2">
            @if (isset($feiraAtual) && $feiraAtual)
                <a href="{{ route('painel.trocar-feira') }}" class="fw-semibold link-body-emphasis text-decoration-none">{{ $feiraAtual->tema }}</a>
            @else
                <span class="fw-semibold text-body-secondary">Nenhuma edição selecionada</span>
            @endif
            <div class="d-flex align-items-center gap-2">
                @include('partials.notificacoes-sino')
                <form method="POST" action="{{ route('logout') }}" class="mb-0">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">Sair</button>
                </form>
            </div>
        </header>
        <main class="p-3 p-md-4">
            {{-- Centralizado aqui para nunca se perder, seja qual for a página onde back() aterrar. --}}
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
