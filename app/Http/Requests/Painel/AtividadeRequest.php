<?php

namespace App\Http\Requests\Painel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Só cobre atividades de origem direta (criadas pela Comissão) — as que
 * nascem de uma inscrição aprovada são geridas pelo Service da Fase 8.4
 * (Etapa 3, decisão 1.2), por isso inscricao_id nunca aparece aqui.
 */
class AtividadeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(['teatro', 'danca', 'musica', 'poesia', 'ciencias', 'artesanato', 'pintura', 'jogos', 'outro'])],
            'titulo' => ['required', 'string', 'max:150'],
            'descricao' => ['nullable', 'string'],
            'responsavel_id' => ['nullable', 'exists:users,id'],
            'participantes_previstos' => ['nullable', 'integer', 'min:0'],
            'foto' => ['nullable', 'image', 'max:4096'],
            'estado' => ['nullable', Rule::in(['planeada', 'confirmada', 'cancelada'])],
        ];
    }
}
