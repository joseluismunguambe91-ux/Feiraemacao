@extends('layouts.painel')

@section('titulo', $expositor->exists ? 'Editar expositor' : 'Novo expositor')

@section('conteudo')
<h1 class="h3 mb-4">{{ $expositor->exists ? 'Editar expositor' : 'Novo expositor' }}</h1>

<form method="POST"
      action="{{ $expositor->exists ? route('painel.expositores.update', $expositor) : route('painel.expositores.store') }}"
      enctype="multipart/form-data" style="max-width: 34rem;">
    @csrf
    @if ($expositor->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="professor_id" class="form-label">Professor responsável</label>
        <select id="professor_id" name="professor_id" class="form-select @error('professor_id') is-invalid @enderror" required>
            <option value="">— Selecionar —</option>
            @foreach ($professores as $professor)
                <option value="{{ $professor->id }}" {{ (int) old('professor_id', $expositor->professor_id) === $professor->id ? 'selected' : '' }}>
                    {{ $professor->name }}
                </option>
            @endforeach
        </select>
        @error('professor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($professores->isEmpty())
            <div class="form-text text-warning">Ainda não existe nenhum professor com conta no sistema.</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="turma" class="form-label">Turma</label>
        <input id="turma" name="turma" class="form-control @error('turma') is-invalid @enderror" value="{{ old('turma', $expositor->turma) }}" required>
        @error('turma')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="categoria" class="form-label">Categoria</label>
        <input id="categoria" name="categoria" class="form-control @error('categoria') is-invalid @enderror" value="{{ old('categoria', $expositor->categoria) }}">
        @error('categoria')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="descricao" class="form-label">Descrição</label>
        <textarea id="descricao" name="descricao" rows="3" class="form-control @error('descricao') is-invalid @enderror">{{ old('descricao', $expositor->descricao) }}</textarea>
        @error('descricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="stand_id" class="form-label">Stand</label>
        <select id="stand_id" name="stand_id" class="form-select @error('stand_id') is-invalid @enderror">
            <option value="">— Sem stand atribuído —</option>
            @foreach ($stands as $stand)
                <option value="{{ $stand->id }}" {{ (int) old('stand_id', $expositor->stand_id) === $stand->id ? 'selected' : '' }}>
                    Stand {{ $stand->numero }}
                </option>
            @endforeach
        </select>
        @error('stand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if ($expositor->exists)
        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror">
                @foreach (['pendente' => 'Pendente', 'ativo' => 'Ativo', 'inativo' => 'Inativo'] as $valor => $rotulo)
                    <option value="{{ $valor }}" {{ old('estado', $expositor->estado) === $valor ? 'selected' : '' }}>{{ $rotulo }}</option>
                @endforeach
            </select>
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endif

    <div class="mb-4">
        <label for="fotos" class="form-label">Fotografias</label>
        <input type="file" id="fotos" name="fotos[]" class="form-control @error('fotos.*') is-invalid @enderror" accept="image/*" multiple>
        @error('fotos.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($expositor->exists && $expositor->fotos->isNotEmpty())
            <div class="d-flex gap-2 flex-wrap mt-2">
                @foreach ($expositor->fotos as $foto)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($foto->path) }}" target="_blank" class="small">Foto #{{ $loop->iteration }}</a>
                @endforeach
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('painel.expositores.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</form>
@endsection
