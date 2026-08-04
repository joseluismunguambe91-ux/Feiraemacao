@extends('layouts.publico')

@section('titulo', 'Contacto')

@section('conteudo')
<div class="container py-5" style="max-width: 30rem;">
    <h1 class="h3 mb-4">Contacto</h1>

    <form method="POST" action="{{ route('publico.contacto.store') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input id="nome" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome') }}" required>
            @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="assunto" class="form-label">Assunto</label>
            <input id="assunto" name="assunto" class="form-control @error('assunto') is-invalid @enderror" value="{{ old('assunto') }}">
            @error('assunto')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label for="mensagem" class="form-label">Mensagem</label>
            <textarea id="mensagem" name="mensagem" rows="4" class="form-control @error('mensagem') is-invalid @enderror" required>{{ old('mensagem') }}</textarea>
            @error('mensagem')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
</div>
@endsection
