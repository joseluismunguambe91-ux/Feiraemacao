@extends('layouts.publico')

@section('titulo', 'Atividades')

@section('conteudo')
<div class="container py-5">
    <h1 class="h3 mb-4">Atividades</h1>

    @if (! $feira)
        <p class="text-body-secondary">Não há nenhuma edição da feira aberta de momento.</p>
    @elseif ($atividades->isEmpty())
        <p class="text-body-secondary">Ainda não há atividades divulgadas.</p>
    @else
        <div class="row g-3">
            @foreach ($atividades as $atividade)
                <div class="col-sm-6 col-lg-4">
                    <div class="border rounded p-3 h-100">
                        <span class="badge badge-neutro mb-2"><x-tipo-atividade-label :tipo="$atividade->tipo" /></span>
                        <h2 class="h6">{{ $atividade->titulo }}</h2>
                        <p class="small text-body-secondary">{{ str($atividade->descricao ?? '')->limit(120) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
