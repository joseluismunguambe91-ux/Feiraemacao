@props(['estado'])
@php
    $classes = match ($estado) {
        'publicada' => 'badge-capim',
        'em_curso' => 'badge-ambar',
        default => 'badge-neutro',
    };
    $rotulos = [
        'rascunho' => 'Rascunho',
        'publicada' => 'Publicada',
        'em_curso' => 'Em curso',
        'encerrada' => 'Encerrada',
        'arquivada' => 'Arquivada',
    ];
@endphp
<span class="badge {{ $classes }}">{{ $rotulos[$estado] ?? $estado }}</span>
