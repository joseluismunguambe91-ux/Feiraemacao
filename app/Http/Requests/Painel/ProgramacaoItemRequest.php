<?php

namespace App\Http\Requests\Painel;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Usado tanto para agendar uma atividade ainda sem entrada na Programação
 * como para reorganizar uma já existente (RF24/RF25, Etapa 1).
 */
class ProgramacaoItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $atividade = $this->route('atividade') ?? $this->route('item')?->atividade;
        $feira = $atividade?->feira;

        return [
            'data' => array_filter([
                'required',
                'date',
                $feira ? 'after_or_equal:'.$feira->data_inicio->format('Y-m-d') : null,
                $feira ? 'before_or_equal:'.$feira->data_fim->format('Y-m-d') : null,
            ]),
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fim' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'local' => ['required', 'string', 'max:150'],
            'palco' => ['nullable', 'string', 'max:80'],
        ];
    }
}
