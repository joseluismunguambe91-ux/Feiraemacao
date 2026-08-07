@extends('layouts.painel')

@section('titulo', 'Relatórios')

@section('conteudo')
<h1 class="h3 mb-4">Relatórios</h1>

@if (! $feira)
    <div class="alert alert-warning">Seleciona uma edição da feira para gerar relatórios.</div>
@else
    <form method="POST" action="{{ route('painel.relatorios.store') }}" class="border rounded p-3 mb-4" style="max-width: 26rem;">
        @csrf
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo de relatório</label>
            <select id="tipo" name="tipo" class="form-select" required>
                <option value="participantes">Participantes (nome, papel, turma, banca)</option>
                <option value="atividades">Atividades</option>
                <option value="expositores">Expositores</option>
                <option value="gastronomia">Gastronomia</option>
                <option value="programacao">Programação</option>
                <option value="visitantes">Visitantes (por dia)</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="formato" class="form-label">Formato</label>
            <select id="formato" name="formato" class="form-select" required>
                <option value="pdf">PDF</option>
                <option value="excel">Excel (.csv)</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Gerar relatório</button>
    </form>

    <h2 class="h6">Relatórios gerados</h2>
    @if ($relatorios->isEmpty())
        <p class="text-body-secondary">Ainda não foi gerado nenhum relatório nesta edição.</p>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr><th>Tipo</th><th>Formato</th><th>Pedido em</th><th>Estado</th><th class="text-end">Ações</th></tr>
                </thead>
                <tbody>
                    @foreach ($relatorios as $relatorio)
                        <tr>
                            <td>{{ ucfirst($relatorio->tipo) }}</td>
                            <td>{{ strtoupper($relatorio->formato) }}</td>
                            <td>{{ $relatorio->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge {{ match ($relatorio->estado) {
                                    'concluido' => 'badge-capim',
                                    'falhou' => 'badge-tijolo',
                                    default => 'badge-ambar',
                                } }}">{{ ucfirst($relatorio->estado) }}</span>
                            </td>
                            <td class="text-end">
                                @if ($relatorio->estado === 'concluido')
                                    <a href="{{ route('painel.relatorios.download', $relatorio) }}" class="btn btn-sm btn-outline-secondary">Descarregar</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $relatorios->links() }}
    @endif
@endif
@endsection
