@extends('layouts.professor')

@section('titulo', $aluno->exists ? 'Editar aluno' : 'Novo aluno')

@section('conteudo')
<h1 class="h3 mb-4">{{ $aluno->exists ? 'Editar aluno' : 'Novo aluno' }}</h1>

<form method="POST"
      action="{{ $aluno->exists ? route('professor.alunos.update', $aluno) : route('professor.alunos.store') }}"
      style="max-width: 30rem;">
    @csrf
    @if ($aluno->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="nome" class="form-label">Nome</label>
        <input id="nome" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $aluno->nome) }}" required>
        @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="turma" class="form-label">Turma</label>
        <input id="turma" name="turma" class="form-control @error('turma') is-invalid @enderror" value="{{ old('turma', $aluno->turma) }}" required>
        @error('turma')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-4">
        <label for="user_id" class="form-label">Conta de acesso (opcional)</label>
        <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror">
            <option value="">— Sem conta própria —</option>
            @foreach ($utilizadoresAluno as $utilizador)
                <option value="{{ $utilizador->id }}" {{ (int) old('user_id', $aluno->user_id) === $utilizador->id ? 'selected' : '' }}>
                    {{ $utilizador->name }} ({{ $utilizador->email }})
                </option>
            @endforeach
        </select>
        <div class="form-text">Se este aluno já tiver uma conta própria no sistema, liga-a aqui — assim as inscrições que ele submeter ficam automaticamente atribuídas a ele, sem teres de escolher.</div>
        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('professor.alunos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</form>
@endsection
