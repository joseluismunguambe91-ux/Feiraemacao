@extends('layouts.publico')

@section('titulo', 'Patrocinadores')

@section('conteudo')
<div class="container py-5">
    <h1 class="h3 mb-4">Patrocinadores</h1>

    @if (! $feira)
        <p class="text-body-secondary">Não há nenhuma edição da feira aberta de momento.</p>
    @elseif ($patrocinadores->isEmpty())
        <p class="text-body-secondary">Ainda não há patrocinadores confirmados para esta edição.</p>
    @else
        <div class="row g-3 align-items-center">
            @foreach ($patrocinadores as $patrocinador)
                <div class="col-sm-4 col-lg-3 text-center">
                    <a href="{{ $patrocinador->url_site ?? '#' }}" target="_blank" class="d-block border rounded p-3 text-decoration-none text-body">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($patrocinador->logotipo_path) }}" alt="{{ $patrocinador->nome }}" class="img-fluid mb-2" style="max-height: 4rem;">
                        <span class="small d-block">{{ $patrocinador->nome }}</span>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
