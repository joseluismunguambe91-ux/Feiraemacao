@props(['item'])
@php
    $inscricao = $item->inscricao;
    $alunos = $inscricao?->alunos;
@endphp
@if ($inscricao?->turma || ($alunos && $alunos->isNotEmpty()))
    <span class="text-body-secondary small d-block">
        @if ($alunos && $alunos->isNotEmpty())
            {{ $alunos->pluck('nome')->join(', ') }} — Turma {{ $inscricao->turma }}
        @else
            Turma {{ $inscricao->turma }}
        @endif
    </span>
@endif
