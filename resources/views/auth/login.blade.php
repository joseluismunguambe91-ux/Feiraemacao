@extends('layouts.publico')

@section('titulo', 'Entrar')

@section('conteudo')
<div class="container py-5" style="max-width: 26rem;">
    <h1 class="h3 mb-4">Entrar</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="lembrar" class="form-check-input" id="lembrar">
            <label class="form-check-label" for="lembrar">Lembrar-me</label>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
        <div class="text-center mt-3">
            <a href="{{ route('password.request') }}" class="small">Esqueceste a senha?</a>
        </div>
    </form>

    <div class="aviso-destaque mt-4 text-center">
        <p class="titulo mb-1">És aluno e ainda não tens conta?</p>
        <p class="small mb-3">Cria a tua própria conta em menos de um minuto — só precisas de um email e de inventares uma senha.</p>
        <a href="{{ route('registar') }}" class="btn btn-outline-secondary btn-sm">Criar a minha conta</a>
    </div>
</div>
@endsection
