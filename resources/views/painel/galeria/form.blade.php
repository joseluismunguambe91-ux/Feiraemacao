@extends('layouts.painel')

@section('titulo', $item->exists ? 'Editar item da galeria' : 'Novo item da galeria')

@section('conteudo')
<h1 class="h3 mb-4">{{ $item->exists ? 'Editar item da galeria' : 'Novo item da galeria' }}</h1>

<form method="POST"
      action="{{ $item->exists ? route('painel.galeria.update', $item) : route('painel.galeria.store') }}"
      enctype="multipart/form-data" style="max-width: 32rem;">
    @csrf
    @if ($item->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="tipo" class="form-label">Tipo</label>
        <select id="tipo" name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
            <option value="foto" {{ old('tipo', $item->tipo) === 'foto' ? 'selected' : '' }}>Foto</option>
            <option value="video" {{ old('tipo', $item->tipo) === 'video' ? 'selected' : '' }}>Vídeo (link do YouTube/Vimeo)</option>
        </select>
        @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="titulo" class="form-label">Título (opcional)</label>
        <input id="titulo" name="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo', $item->titulo) }}">
        @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="categoria" class="form-label">Categoria (opcional)</label>
        <input id="categoria" name="categoria" class="form-control @error('categoria') is-invalid @enderror" value="{{ old('categoria', $item->categoria) }}">
        @error('categoria')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="foto" class="form-label">Ficheiro da foto <span class="text-body-secondary">(obrigatório se o tipo for Foto)</span></label>
        <input type="file" id="foto" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
        @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($item->exists && $item->tipo === 'foto')
            <div class="form-text"><a href="{{ \Illuminate\Support\Facades\Storage::url($item->path_ou_url) }}" target="_blank">Ver foto atual</a></div>
        @endif
    </div>

    <div class="mb-4">
        <label for="url_video" class="form-label">Link do vídeo <span class="text-body-secondary">(obrigatório se o tipo for Vídeo)</span></label>
        <input id="url_video" name="url_video" class="form-control @error('url_video') is-invalid @enderror"
               value="{{ old('url_video', $item->tipo === 'video' ? $item->path_ou_url : '') }}" placeholder="https://…">
        @error('url_video')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-4">
        <label for="ordem" class="form-label">Ordem</label>
        <input type="number" min="0" id="ordem" name="ordem" class="form-control @error('ordem') is-invalid @enderror" value="{{ old('ordem', $item->ordem ?? 0) }}">
        @error('ordem')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('painel.galeria.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</form>
@endsection
