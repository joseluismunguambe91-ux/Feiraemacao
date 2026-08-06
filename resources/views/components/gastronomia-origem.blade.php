@props(['item'])
@php
    $inscricao = $item->inscricao;
    $alunos = $inscricao?->alunos;
    // Nomes a mostrar: os Alunos do plantel se a inscrição os tiver ligados
    // (Professor escolheu-os); senão, quem submeteu a inscrição — que é o
    // próprio Aluno quando se inscreve sozinho com a turma na conta.
    $nomes = $alunos && $alunos->isNotEmpty() ? $alunos->pluck('nome') : collect([$inscricao?->professor?->name])->filter();
@endphp
@if ($inscricao && ($inscricao->turma || $nomes->isNotEmpty()))
    <span class="text-body-secondary small d-block">
        {{ $nomes->join(', ') }}{{ $nomes->isNotEmpty() && $inscricao->turma ? ' — ' : '' }}{{ $inscricao->turma ? 'Turma '.$inscricao->turma : '' }}
    </span>
@endif
