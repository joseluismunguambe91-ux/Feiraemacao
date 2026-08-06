@extends('layouts.publico')

@section('titulo', $feira->tema ?? 'Feira Gastronómica e Cultural')

@section('conteudo')
@if ($feira)
    <section class="hero-publico py-5 {{ $feira->banner_path ? 'hero-publico--com-banner' : '' }}"
        @if ($feira->banner_path) style="background-image: linear-gradient(135deg, rgba(36,27,16,.55), rgba(36,27,16,.25)), url('{{ \Illuminate\Support\Facades\Storage::url($feira->banner_path) }}');" @endif>
        <div class="container">
            <div class="row align-items-end g-4">
                <div class="col-lg-8">
                    @if ($feira->logotipo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($feira->logotipo_path) }}" alt="Logótipo {{ $feira->tema }}" class="mb-3" style="max-height: 64px;">
                    @endif
                    <span class="fw-semibold">{{ $feira->data_inicio->format('d/m/Y') }} – {{ $feira->data_fim->format('d/m/Y') }} · {{ $feira->local }}</span>
                    <h1 class="display-6 mt-1 mb-3">{{ $feira->tema }}</h1>
                    <div class="d-flex gap-4 fw-semibold mb-4 flex-wrap">
                        <span>{{ $totalExpositores }} expositores</span>
                        <span>{{ $totalStands }} stands</span>
                        <span>{{ $totalAtividades }} atividades</span>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('publico.programacao') }}" class="btn btn-dark">Ver programação</a>
                        <a href="{{ route('publico.mapa') }}" class="btn btn-outline-dark">Ver mapa</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    @if ($destaques->isNotEmpty())
                        <div class="bg-white bg-opacity-50 border rounded p-3">
                            <h2 class="h6">Destaques de hoje</h2>
                            <ol class="mb-0 ps-3 small">
                                @foreach ($destaques as $item)
                                    <li><strong>{{ substr($item->hora_inicio, 0, 5) }}</strong> — {{ $item->atividade->titulo }}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@else
    <section class="py-5 text-center">
        <div class="container">
            <h1 class="display-6">Feira Gastronómica e Cultural</h1>
            <p class="text-body-secondary">Não há nenhuma edição da feira aberta de momento. Volta a visitar-nos em breve.</p>
        </div>
    </section>
@endif

<section class="container py-5">
    <div class="row g-3">
        @foreach ([
            ['rota' => 'publico.atividades', 'titulo' => 'Atividades', 'texto' => 'Teatro, dança, música e muito mais.'],
            ['rota' => 'publico.gastronomia', 'titulo' => 'Gastronomia', 'texto' => 'O que há para provar este ano.'],
            ['rota' => 'publico.expositores', 'titulo' => 'Expositores', 'texto' => 'Conhece as turmas e os seus projetos.'],
            ['rota' => 'publico.galeria', 'titulo' => 'Galeria', 'texto' => 'Fotos e vídeos da feira.'],
        ] as $bloco)
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route($bloco['rota']) }}" class="d-block border rounded p-3 h-100 text-decoration-none text-body">
                    <strong>{{ $bloco['titulo'] }}</strong>
                    <p class="small text-body-secondary mb-0">{{ $bloco['texto'] }}</p>
                </a>
            </div>
        @endforeach
    </div>
</section>
@endsection
