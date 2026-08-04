@extends('layouts.painel')

@section('titulo', 'Dashboard')

@section('conteudo')
@if ($feiraAtual)
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $feiraAtual->tema }}</h1>
            <x-estado-feira-badge :estado="$feiraAtual->estado" />
        </div>
        <div class="d-flex gap-2">
            @if ($feiraAtual->estado !== 'arquivada')
                <form method="POST" action="{{ route('painel.feiras.avancar-estado', $feiraAtual) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">Avançar estado</button>
                </form>
            @endif
            @if (auth()->user()->hasRole('administrador') && $feiraAtual->estado !== 'rascunho')
                <form method="POST" action="{{ route('admin.feiras.reverter-estado', $feiraAtual) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Reverter estado</button>
                </form>
            @endif
            <a href="{{ route('painel.trocar-feira') }}" class="btn btn-outline-secondary btn-sm">Trocar de edição</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-tile"><span class="num">{{ $stats['inscritos'] }}</span><span class="lbl">Inscritos</span></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-tile"><span class="num">{{ $stats['expositores'] }}</span><span class="lbl">Expositores</span></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-tile"><span class="num">{{ $stats['stands'] }}</span><span class="lbl">Stands</span></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-tile"><span class="num">{{ $stats['atividades'] }}</span><span class="lbl">Atividades</span></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-tile"><span class="num">{{ $stats['professores'] }}</span><span class="lbl">Professores</span></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-tile"><span class="num">{{ $stats['alunos'] }}</span><span class="lbl">Alunos</span></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-tile"><span class="num">—</span><span class="lbl">Visitantes (fora do escopo atual)</span></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <h2 class="h6">Inscrições por estado</h2>
            <canvas id="graficoInscricoes" height="160"></canvas>
        </div>
        <div class="col-lg-6">
            <h2 class="h6">Próximas apresentações</h2>
            @forelse ($proximasApresentacoes as $item)
                <div class="d-flex justify-content-between border-bottom py-2 small">
                    <span>{{ $item->atividade->titulo }}</span>
                    <span class="text-body-secondary">{{ $item->data->format('d/m') }} · {{ substr($item->hora_inicio, 0, 5) }}</span>
                </div>
            @empty
                <p class="text-body-secondary small">Sem apresentações agendadas.</p>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Chart(document.getElementById('graficoInscricoes'), {
                type: 'bar',
                data: {
                    labels: @json($inscricoesPorEstado->keys()),
                    datasets: [{
                        label: 'Inscrições',
                        data: @json($inscricoesPorEstado->values()),
                        backgroundColor: '#ffc42b',
                    }],
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                },
            });
        });
    </script>
    @endpush
@else
    <div class="alert alert-warning">
        Ainda não existe nenhuma edição da feira.
        @if (auth()->user()->hasRole('administrador'))
            <a href="{{ route('admin.feiras.create') }}">Cria a primeira edição</a> para começar a preparar o evento.
        @else
            Pede a um Administrador para criar a primeira edição.
        @endif
    </div>
@endif
@endsection
