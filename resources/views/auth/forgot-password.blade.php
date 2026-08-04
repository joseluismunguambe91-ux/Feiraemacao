@extends('layouts.publico')

@section('titulo', 'Recuperar senha')

@section('conteudo')
<div class="container py-5" style="max-width: 26rem;">
    <h1 class="h3 mb-3">Recuperar senha</h1>
    <p class="text-body-secondary">Indica o teu email e enviamos um link para definires uma nova senha.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Enviar link de recuperação</button>
    </form>
</div>
@endsection
