<?php

namespace App\Http\Requests\Painel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Duas formas de aprovar, consoante inscricao.tipo_atividade: uma atividade
 * agendada (a Comissão decide data/hora/local/palco definitivos — a
 * inscrição só guarda o horário PRETENDIDO) ou uma banca de gastronomia (a
 * Comissão só atribui um Stand livre; sem agenda/horário envolvidos).
 */
class InscricaoAprovacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $inscricao = $this->route('inscricao');

        if ($inscricao?->tipo_atividade === 'gastronomia') {
            return [
                'stand_id' => [
                    'required',
                    'exists:stands,id',
                    Rule::unique('expositores', 'stand_id')->where(fn ($query) => $query->whereNull('deleted_at')),
                ],
                // Pré-preenchido com o que o professor/aluno indicou na
                // inscrição — a Comissão só confirma ou ajusta aqui.
                'produto_nome' => ['required', 'string', 'max:120'],
                'produto_preco' => ['required', 'numeric', 'min:0'],
            ];
        }

        $feira = $inscricao?->feira;

        return [
            'titulo' => ['required', 'string', 'max:150'],
            'data' => array_filter([
                'required',
                'date',
                $feira ? 'after_or_equal:'.$feira->data_inicio->format('Y-m-d') : null,
                $feira ? 'before_or_equal:'.$feira->data_fim->format('Y-m-d') : null,
            ]),
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fim' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'local' => ['required', 'string', 'max:150'],
            'palco' => [$inscricao?->necessita_palco ? 'required' : 'nullable', 'string', 'max:80'],
        ];
    }
}
