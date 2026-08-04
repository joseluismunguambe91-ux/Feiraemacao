@props(['estado'])
@php
    $classes = match ($estado) {
        'aprovada' => 'badge-capim',
        'rejeitada' => 'badge-tijolo',
        default => 'badge-ambar',
    };
    $rotulos = [
        'pendente' => 'Pendente',
        'aprovada' => 'Aprovada',
        'rejeitada' => 'Rejeitada',
    ];
@endphp
<span class="badge {{ $classes }}">{{ $rotulos[$estado] ?? $estado }}</span>
