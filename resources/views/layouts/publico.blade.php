<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Feira Gastronómica e Cultural')</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body border-bottom">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('publico.inicio') }}">Feira em Ação</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navPublico" aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navPublico">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('publico.sobre') ? 'active' : '' }}" href="{{ route('publico.sobre') }}">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('publico.programacao') ? 'active' : '' }}" href="{{ route('publico.programacao') }}">Programação</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('publico.atividades') ? 'active' : '' }}" href="{{ route('publico.atividades') }}">Atividades</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('publico.gastronomia') ? 'active' : '' }}" href="{{ route('publico.gastronomia') }}">Gastronomia</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('publico.expositores') ? 'active' : '' }}" href="{{ route('publico.expositores') }}">Expositores</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('publico.mapa') ? 'active' : '' }}" href="{{ route('publico.mapa') }}">Mapa</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('publico.galeria') ? 'active' : '' }}" href="{{ route('publico.galeria') }}">Galeria</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('publico.patrocinadores') ? 'active' : '' }}" href="{{ route('publico.patrocinadores') }}">Patrocinadores</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('publico.contacto') ? 'active' : '' }}" href="{{ route('publico.contacto') }}">Contacto</a></li>
                </ul>
                <form method="GET" action="{{ route('publico.pesquisa') }}" class="d-flex" role="search">
                    <input type="search" name="q" class="form-control form-control-sm" placeholder="Pesquisar…" value="{{ request('q') }}" aria-label="Pesquisar">
                </form>
            </div>
        </div>
    </nav>

    <main>
        @if (session('sucesso') || session('erro'))
            <div class="container pt-4">
                @if (session('sucesso'))
                    <div class="alert alert-success">{{ session('sucesso') }}</div>
                @endif
                @if (session('erro'))
                    <div class="alert alert-danger">{{ session('erro') }}</div>
                @endif
            </div>
        @endif

        @yield('conteudo')
    </main>

    <footer class="border-top py-4 mt-5">
        <div class="container small text-body-secondary">
            &copy; {{ now()->year }} Feira Gastronómica e Cultural Escolar.
        </div>
    </footer>

    <div id="avisoCookies" class="d-none position-fixed bottom-0 start-0 end-0 bg-dark text-white py-3 px-3" style="z-index: 1050;">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
            <p class="small mb-0">
                Este site usa cookies essenciais para funcionar corretamente (sessão, segurança). Ao continuar a navegar, aceita a sua utilização.
            </p>
            <button type="button" id="aceitarCookies" class="btn btn-sm btn-light flex-shrink-0">Aceitar</button>
        </div>
    </div>
    <script>
        (function () {
            var aviso = document.getElementById('avisoCookies');
            if (! aviso) return;
            if (! localStorage.getItem('cookies-aceites')) {
                aviso.classList.remove('d-none');
            }
            document.getElementById('aceitarCookies').addEventListener('click', function () {
                localStorage.setItem('cookies-aceites', '1');
                aviso.classList.add('d-none');
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
