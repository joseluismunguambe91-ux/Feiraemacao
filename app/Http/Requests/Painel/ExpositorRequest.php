<?php

namespace App\Http\Requests\Painel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * RN03 (Etapa 3): 1 stand só pode estar associado a um expositor por vez —
 * já garantido por unique(stand_id) na BD, aqui só a mensagem amigável
 * antes de lá chegar.
 */
class ExpositorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'professor_id' => ['required', 'exists:users,id'],
            'turma' => ['required', 'string', 'max:50'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'descricao' => ['nullable', 'string'],
            'stand_id' => [
                'nullable',
                'exists:stands,id',
                Rule::unique('expositores', 'stand_id')
                    ->where(fn ($query) => $query->whereNull('deleted_at'))
                    ->ignore($this->route('expositor')),
            ],
            'estado' => ['nullable', Rule::in(['pendente', 'ativo', 'inativo'])],
            'fotos.*' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
