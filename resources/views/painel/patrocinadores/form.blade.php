@extends('layouts.painel')

@section('titulo', $patrocinador->exists ? 'Editar patrocinador' : 'Novo patrocinador')

@section('conteudo')
<h1 class="h3 mb-4">{{ $patrocinador->exists ? 'Editar patrocinador' : 'Novo patrocinador' }}</h1>

<form method="POST"
      action="{{ $patrocinador->exists ? route('painel.patrocinadores.update', $patrocinador) : route('painel.patrocinadores.store') }}"
      enctype="multipart/form-data" style="max-width: 30rem;">
    @csrf
    @if ($patrocinador->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="nome" class="form-label">Nome</label>
        <input id="nome" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $patrocinador->nome) }}" required>
        @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="logotipo" class="form-label">Logótipo</label>
        <input type="file" id="logotipo" name="logotipo" class="form-control @error('logotipo') is-invalid @enderror" accept="image/*">
        @error('logotipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($patrocinador->logotipo_path)
            <div class="form-text"><a href="{{ \Illuminate\Support\Facades\Storage::url($patrocinador->logotipo_path) }}" target="_blank">Ver logótipo atual</a></div>
        @endif
    </div>

    <div class="mb-3">
        <label for="url_site" class="form-label">Site (opcional)</label>
        <input id="url_site" name="url_site" class="form-control @error('url_site') is-invalid @enderror" value="{{ old('url_site', $patrocinador->url_site) }}" placeholder="https://…">
        @error('url_site')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="nivel" class="form-label">Nível (opcional)</label>
        <input id="nivel" name="nivel" class="form-control @error('nivel') is-invalid @enderror" value="{{ old('nivel', $patrocinador->nivel) }}" placeholder="Ouro, Prata, Bronze…">
        @error('nivel')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-4">
        <label for="ordem" class="form-label">Ordem</label>
        <input type="number" min="0" id="ordem" name="ordem" class="form-control @error('ordem') is-invalid @enderror" value="{{ old('ordem', $patrocinador->ordem ?? 0) }}">
        @error('ordem')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('painel.patrocinadores.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</form>
@endsection
