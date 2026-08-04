@extends('layouts.painel')

@section('titulo', $item->exists ? 'Editar item' : 'Novo item')

@section('conteudo')
<h1 class="h3 mb-4">{{ $item->exists ? 'Editar item de gastronomia' : 'Novo item de gastronomia' }}</h1>

<form method="POST"
      action="{{ $item->exists ? route('painel.gastronomia.update', $item) : route('painel.gastronomia.store') }}"
      enctype="multipart/form-data" style="max-width: 34rem;">
    @csrf
    @if ($item->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="nome" class="form-label">Nome</label>
        <input id="nome" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $item->nome) }}" required>
        @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="categoria" class="form-label">Categoria</label>
        <input id="categoria" name="categoria" class="form-control @error('categoria') is-invalid @enderror" value="{{ old('categoria', $item->categoria) }}">
        @error('categoria')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="descricao" class="form-label">Descrição</label>
        <textarea id="descricao" name="descricao" rows="2" class="form-control @error('descricao') is-invalid @enderror">{{ old('descricao', $item->descricao) }}</textarea>
        @error('descricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="ingredientes" class="form-label">Ingredientes (opcional)</label>
        <textarea id="ingredientes" name="ingredientes" rows="2" class="form-control @error('ingredientes') is-invalid @enderror">{{ old('ingredientes', $item->ingredientes) }}</textarea>
        @error('ingredientes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label for="preco" class="form-label">Preço (MT)</label>
            <input type="number" step="0.01" min="0" id="preco" name="preco" class="form-control @error('preco') is-invalid @enderror" value="{{ old('preco', $item->preco) }}" required>
            @error('preco')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
            <label for="quantidade_disponivel" class="form-label">Quantidade disponível (opcional)</label>
            <input type="number" min="0" id="quantidade_disponivel" name="quantidade_disponivel" class="form-control @error('quantidade_disponivel') is-invalid @enderror" value="{{ old('quantidade_disponivel', $item->quantidade_disponivel) }}">
            @error('quantidade_disponivel')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" name="disponivel" value="1" class="form-check-input" id="disponivel" {{ old('disponivel', $item->exists ? $item->disponivel : true) ? 'checked' : '' }}>
        <label class="form-check-label" for="disponivel">Disponível</label>
    </div>

    <div class="mb-4">
        <label for="foto" class="form-label">Fotografia</label>
        <input type="file" id="foto" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
        @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($item->foto_path)
            <div class="form-text"><a href="{{ \Illuminate\Support\Facades\Storage::url($item->foto_path) }}" target="_blank">Ver fotografia atual</a></div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('painel.gastronomia.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</form>
@endsection
