@extends('layouts.painel')

@section('titulo', $atividade->exists ? 'Editar atividade' : 'Nova atividade')

@section('conteudo')
<h1 class="h3 mb-4">{{ $atividade->exists ? 'Editar atividade' : 'Nova atividade' }}</h1>

@if ($atividade->exists && $atividade->inscricao_id)
    <div class="alert alert-warning">Esta atividade nasceu de uma inscrição aprovada — o conteúdo ainda pode ser editado aqui.</div>
@endif

<form method="POST"
      action="{{ $atividade->exists ? route('painel.atividades.update', $atividade) : route('painel.atividades.store') }}"
      enctype="multipart/form-data" style="max-width: 34rem;">
    @csrf
    @if ($atividade->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="titulo" class="form-label">Título</label>
        <input id="titulo" name="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo', $atividade->titulo) }}" required>
        @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="tipo" class="form-label">Tipo</label>
        <select id="tipo" name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
            @foreach (['teatro', 'danca', 'musica', 'poesia', 'ciencias', 'artesanato', 'pintura', 'jogos', 'outro'] as $tipo)
                <option value="{{ $tipo }}" {{ old('tipo', $atividade->tipo) === $tipo ? 'selected' : '' }}>
                    <x-tipo-atividade-label :tipo="$tipo" />
                </option>
            @endforeach
        </select>
        @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="descricao" class="form-label">Descrição</label>
        <textarea id="descricao" name="descricao" rows="3" class="form-control @error('descricao') is-invalid @enderror">{{ old('descricao', $atividade->descricao) }}</textarea>
        @error('descricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="responsavel_id" class="form-label">Responsável</label>
        <select id="responsavel_id" name="responsavel_id" class="form-select @error('responsavel_id') is-invalid @enderror">
            <option value="">— Sem responsável definido —</option>
            @foreach ($responsaveis as $utilizador)
                <option value="{{ $utilizador->id }}" {{ (int) old('responsavel_id', $atividade->responsavel_id) === $utilizador->id ? 'selected' : '' }}>
                    {{ $utilizador->name }}
                </option>
            @endforeach
        </select>
        @error('responsavel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="participantes_previstos" class="form-label">Participantes previstos</label>
        <input type="number" min="0" id="participantes_previstos" name="participantes_previstos"
               class="form-control @error('participantes_previstos') is-invalid @enderror"
               value="{{ old('participantes_previstos', $atividade->participantes_previstos) }}">
        @error('participantes_previstos')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if ($atividade->exists)
        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror">
                @foreach (['planeada' => 'Planeada', 'confirmada' => 'Confirmada', 'cancelada' => 'Cancelada'] as $valor => $rotulo)
                    <option value="{{ $valor }}" {{ old('estado', $atividade->estado) === $valor ? 'selected' : '' }}>{{ $rotulo }}</option>
                @endforeach
            </select>
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endif

    <div class="mb-4">
        <label for="foto" class="form-label">Fotografia</label>
        <input type="file" id="foto" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
        @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($atividade->foto_path)
            <div class="form-text"><a href="{{ \Illuminate\Support\Facades\Storage::url($atividade->foto_path) }}" target="_blank">Ver fotografia atual</a></div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('painel.atividades.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</form>
@endsection
