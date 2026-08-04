@extends('layouts.painel')

@section('titulo', $feira->exists ? 'Editar edição' : 'Nova edição')

@section('conteudo')
<h1 class="h3 mb-4">{{ $feira->exists ? 'Editar edição' : 'Nova edição' }}</h1>

<form method="POST" action="{{ $feira->exists ? route('admin.feiras.update', $feira) : route('admin.feiras.store') }}"
      enctype="multipart/form-data" style="max-width: 40rem;">
    @csrf
    @if ($feira->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="tema" class="form-label">Tema</label>
        <input id="tema" name="tema" class="form-control @error('tema') is-invalid @enderror"
               value="{{ old('tema', $feira->tema) }}" required>
        @error('tema')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="descricao" class="form-label">Descrição</label>
        <textarea id="descricao" name="descricao" rows="3" class="form-control @error('descricao') is-invalid @enderror">{{ old('descricao', $feira->descricao) }}</textarea>
        @error('descricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label for="data_inicio" class="form-label">Data de início</label>
            <input type="date" id="data_inicio" name="data_inicio" class="form-control @error('data_inicio') is-invalid @enderror"
                   value="{{ old('data_inicio', optional($feira->data_inicio)->format('Y-m-d')) }}" required>
            @error('data_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
            <label for="data_fim" class="form-label">Data de fim</label>
            <input type="date" id="data_fim" name="data_fim" class="form-control @error('data_fim') is-invalid @enderror"
                   value="{{ old('data_fim', optional($feira->data_fim)->format('Y-m-d')) }}" required>
            @error('data_fim')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label for="hora_abertura" class="form-label">Hora de abertura</label>
            <input type="time" id="hora_abertura" name="hora_abertura" class="form-control @error('hora_abertura') is-invalid @enderror"
                   value="{{ old('hora_abertura', $feira->hora_abertura) }}" required>
            @error('hora_abertura')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
            <label for="hora_encerramento" class="form-label">Hora de encerramento</label>
            <input type="time" id="hora_encerramento" name="hora_encerramento" class="form-control @error('hora_encerramento') is-invalid @enderror"
                   value="{{ old('hora_encerramento', $feira->hora_encerramento) }}" required>
            @error('hora_encerramento')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="local" class="form-label">Local</label>
        <input id="local" name="local" class="form-control @error('local') is-invalid @enderror"
               value="{{ old('local', $feira->local) }}" required>
        @error('local')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="banner" class="form-label">Banner</label>
        <input type="file" id="banner" name="banner" class="form-control @error('banner') is-invalid @enderror" accept="image/*">
        @error('banner')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($feira->banner_path)
            <div class="form-text"><a href="{{ \Illuminate\Support\Facades\Storage::url($feira->banner_path) }}" target="_blank">Ver banner atual</a></div>
        @endif
    </div>

    <div class="mb-3">
        <label for="logotipo" class="form-label">Logótipo</label>
        <input type="file" id="logotipo" name="logotipo" class="form-control @error('logotipo') is-invalid @enderror" accept="image/*">
        @error('logotipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($feira->logotipo_path)
            <div class="form-text"><a href="{{ \Illuminate\Support\Facades\Storage::url($feira->logotipo_path) }}" target="_blank">Ver logótipo atual</a></div>
        @endif
    </div>

    <div class="mb-4">
        <label for="regulamento" class="form-label">Regulamento (PDF)</label>
        <input type="file" id="regulamento" name="regulamento" class="form-control @error('regulamento') is-invalid @enderror" accept="application/pdf">
        @error('regulamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($feira->regulamento_path)
            <div class="form-text"><a href="{{ \Illuminate\Support\Facades\Storage::url($feira->regulamento_path) }}" target="_blank">Ver regulamento atual</a></div>
        @endif
    </div>

    @if ($feira->exists)
        <div class="mb-4">
            <span class="form-label d-block">Estado atual</span>
            <x-estado-feira-badge :estado="$feira->estado" />
            <div class="form-text">O estado só se altera através das ações de avançar/reverter, não neste formulário.</div>
        </div>
    @endif

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('admin.feiras.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</form>
@endsection
