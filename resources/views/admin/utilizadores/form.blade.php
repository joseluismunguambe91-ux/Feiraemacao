@extends('layouts.painel')

@section('titulo', $utilizador->exists ? 'Editar utilizador' : 'Novo utilizador')

@section('conteudo')
<h1 class="h3 mb-4">{{ $utilizador->exists ? 'Editar utilizador' : 'Novo utilizador' }}</h1>

<form method="POST"
      action="{{ $utilizador->exists ? route('admin.utilizadores.update', $utilizador) : route('admin.utilizadores.store') }}"
      style="max-width: 28rem;">
    @csrf
    @if ($utilizador->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">Nome</label>
        <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $utilizador->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $utilizador->email) }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="telefone" class="form-label">Telefone (opcional)</label>
        <input id="telefone" name="telefone" class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone', $utilizador->telefone) }}">
        @error('telefone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">
            Senha {{ $utilizador->exists ? '(deixa em branco para manter a atual)' : '' }}
        </label>
        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <span class="form-label d-block">Papéis</span>
        @foreach ($papeis as $papel)
            <div class="form-check">
                <input type="checkbox" name="roles[]" value="{{ $papel->id }}" class="form-check-input" id="papel-{{ $papel->id }}"
                       {{ in_array($papel->id, old('roles', $utilizador->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                <label class="form-check-label" for="papel-{{ $papel->id }}">{{ $papel->nome }}</label>
            </div>
        @endforeach
        @error('roles')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="mb-4 form-check">
        <input type="checkbox" name="ativo" value="1" class="form-check-input" id="ativo"
               {{ old('ativo', $utilizador->exists ? $utilizador->ativo : true) ? 'checked' : '' }}>
        <label class="form-check-label" for="ativo">Conta ativa</label>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('admin.utilizadores.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</form>
@endsection
