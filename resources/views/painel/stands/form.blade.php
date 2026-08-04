@extends('layouts.painel')

@section('titulo', $stand->exists ? 'Editar stand' : 'Novo stand')

@section('conteudo')
<h1 class="h3 mb-4">{{ $stand->exists ? 'Editar stand' : 'Novo stand' }}</h1>

<form method="POST"
      action="{{ $stand->exists ? route('painel.stands.update', $stand) : route('painel.stands.store') }}"
      style="max-width: 30rem;">
    @csrf
    @if ($stand->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="numero" class="form-label">Número</label>
        <input id="numero" name="numero" class="form-control @error('numero') is-invalid @enderror" value="{{ old('numero', $stand->numero) }}" required>
        @error('numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="localizacao" class="form-label">Localização</label>
        <input id="localizacao" name="localizacao" class="form-control @error('localizacao') is-invalid @enderror" value="{{ old('localizacao', $stand->localizacao) }}">
        @error('localizacao')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6">
            <label for="capacidade" class="form-label">Capacidade</label>
            <input type="number" min="0" id="capacidade" name="capacidade" class="form-control @error('capacidade') is-invalid @enderror" value="{{ old('capacidade', $stand->capacidade) }}">
            @error('capacidade')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
            <label for="categoria" class="form-label">Categoria</label>
            <input id="categoria" name="categoria" class="form-control @error('categoria') is-invalid @enderror" value="{{ old('categoria', $stand->categoria) }}">
            @error('categoria')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="responsavel_id" class="form-label">Responsável</label>
        <select id="responsavel_id" name="responsavel_id" class="form-select @error('responsavel_id') is-invalid @enderror">
            <option value="">— Sem responsável definido —</option>
            @foreach ($responsaveis as $utilizador)
                <option value="{{ $utilizador->id }}" {{ (int) old('responsavel_id', $stand->responsavel_id) === $utilizador->id ? 'selected' : '' }}>
                    {{ $utilizador->name }}
                </option>
            @endforeach
        </select>
        @error('responsavel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if ($stand->exists)
        <div class="mb-4">
            <label for="estado" class="form-label">Estado</label>
            <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror">
                @foreach (['disponivel' => 'Disponível', 'reservado' => 'Reservado', 'ocupado' => 'Ocupado', 'inativo' => 'Inativo'] as $valor => $rotulo)
                    <option value="{{ $valor }}" {{ old('estado', $stand->estado) === $valor ? 'selected' : '' }}>{{ $rotulo }}</option>
                @endforeach
            </select>
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endif

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('painel.stands.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</form>
@endsection
