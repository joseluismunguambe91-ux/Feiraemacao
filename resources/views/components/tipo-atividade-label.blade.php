@props(['tipo'])
@php
    $rotulos = [
        'teatro' => 'Teatro',
        'danca' => 'Dança',
        'musica' => 'Música',
        'poesia' => 'Poesia',
        'ciencias' => 'Ciências',
        'artesanato' => 'Artesanato',
        'pintura' => 'Pintura',
        'jogos' => 'Jogos',
        'gastronomia' => 'Gastronomia',
        'outro' => 'Outro',
    ];
@endphp
{{ $rotulos[$tipo] ?? $tipo }}
