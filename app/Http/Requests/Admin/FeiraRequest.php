<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Regras espelham o dicionário de dados da Etapa 3 (tabela feiras) — usada
 * tanto para criar como para editar; o estado nunca se muda por aqui,
 * só pelas ações dedicadas (avançar/reverter — Etapa 4, secção 2).
 */
class FeiraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tema' => ['required', 'string', 'max:150'],
            'descricao' => ['nullable', 'string'],
            'data_inicio' => ['required', 'date'],
            'data_fim' => ['required', 'date', 'after_or_equal:data_inicio'],
            'hora_abertura' => ['required', 'date_format:H:i'],
            'hora_encerramento' => ['required', 'date_format:H:i', 'after:hora_abertura'],
            'local' => ['required', 'string', 'max:200'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'logotipo' => ['nullable', 'image', 'max:2048'],
            'regulamento' => ['nullable', 'mimes:pdf', 'max:8192'],
        ];
    }
}
