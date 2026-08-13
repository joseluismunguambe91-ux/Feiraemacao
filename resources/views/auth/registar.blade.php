@extends('layouts.publico')

@section('titulo', 'Criar conta')

@section('conteudo')
<div class="container py-5" style="max-width: 28rem;">
    <h1 class="h3 mb-2">Criar a tua conta</h1>
    <p class="text-body-secondary mb-4">
        É rápido: escreve o teu nome, um email e inventa uma senha. Depois de guardares,
        já podes entrar sempre que quiseres com esse mesmo email e senha.
    </p>

    <form method="POST" action="{{ route('registar.store') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">O teu nome</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">O teu email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required
                   placeholder="exemplo@gmail.com">
            <div class="form-text">Vais usar este email sempre que quiseres entrar — escreve com atenção.</div>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Inventa uma senha</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror" required>
            <div class="form-text">Pelo menos 8 letras/números. Não te esqueças dela!</div>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Escreve a senha outra vez</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Criar a minha conta</button>
        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="small">Já tens conta? Entra aqui</a>
        </div>
    </form>
</div>
@endsection
