@extends('layouts.painel')

@section('titulo', $item->exists ? 'Reorganizar agendamento' : 'Agendar atividade')

@section('conteudo')
<h1 class="h3 mb-1">{{ $item->exists ? 'Reorganizar agendamento' : 'Agendar atividade' }}</h1>
<p class="text-body-secondary mb-4">{{ $atividade->titulo }} — <x-tipo-atividade-label :tipo="$atividade->tipo" /></p>

<div id="aviso-conflito" class="alert alert-danger d-none">
    Este horário e palco já estão ocupados por outra atividade — ajusta antes de gravar.
</div>

<form id="form-agendamento" method="POST"
      action="{{ $item->exists ? route('painel.programacao.update', $item) : route('painel.programacao.store', $atividade) }}"
      data-verificar-url="{{ route('painel.programacao.verificar-conflito') }}"
      data-feira-id="{{ $atividade->feira_id }}"
      data-ignorar-item-id="{{ $item->id }}"
      style="max-width: 30rem;">
    @csrf
    @if ($item->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="data" class="form-label">Data</label>
        <input type="date" id="data" name="data" class="form-control @error('data') is-invalid @enderror"
               value="{{ old('data', optional($item->data)->format('Y-m-d')) }}"
               min="{{ $atividade->feira->data_inicio->format('Y-m-d') }}"
               max="{{ $atividade->feira->data_fim->format('Y-m-d') }}" required>
        @error('data')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="row g-3 mb-3">
        <div class="col-6">
            <label for="hora_inicio" class="form-label">Hora início</label>
            <input type="time" id="hora_inicio" name="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror"
                   value="{{ old('hora_inicio', $item->hora_inicio ? substr($item->hora_inicio, 0, 5) : '') }}" required>
            @error('hora_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
            <label for="hora_fim" class="form-label">Hora fim</label>
            <input type="time" id="hora_fim" name="hora_fim" class="form-control @error('hora_fim') is-invalid @enderror"
                   value="{{ old('hora_fim', $item->hora_fim ? substr($item->hora_fim, 0, 5) : '') }}" required>
            @error('hora_fim')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="mb-3">
        <label for="local" class="form-label">Local</label>
        <input id="local" name="local" class="form-control @error('local') is-invalid @enderror" value="{{ old('local', $item->local) }}" required>
        @error('local')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-4">
        <label for="palco" class="form-label">Palco</label>
        <input id="palco" name="palco" class="form-control @error('palco') is-invalid @enderror" value="{{ old('palco', $item->palco) }}">
        @error('palco')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('painel.programacao.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-agendamento');
    const aviso = document.getElementById('aviso-conflito');
    if (!form) return;

    function verificar() {
        const data = form.data.value;
        const horaInicio = form.hora_inicio.value;
        const horaFim = form.hora_fim.value;
        const palco = form.palco.value;

        if (!data || !horaInicio || !horaFim || horaFim <= horaInicio || !palco) {
            aviso.classList.add('d-none');
            return;
        }

        fetch(form.dataset.verificarUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                feira_id: form.dataset.feiraId,
                data: data,
                hora_inicio: horaInicio,
                hora_fim: horaFim,
                palco: palco,
                ignorar_item_id: form.dataset.ignorarItemId || null,
            }),
        })
            .then(function (resposta) { return resposta.json(); })
            .then(function (corpo) { aviso.classList.toggle('d-none', !corpo.conflito); });
    }

    ['data', 'hora_inicio', 'hora_fim', 'palco'].forEach(function (nome) {
        form[nome].addEventListener('change', verificar);
    });
});
</script>
@endpush
@endsection
