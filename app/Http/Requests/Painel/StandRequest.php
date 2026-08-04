<?php

namespace App\Http\Requests\Painel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * numero é único por edição (unique(feira_id, numero) — Etapa 3), não
 * globalmente: duas edições diferentes podem ambas ter um "Stand 01".
 */
class StandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $feira = $this->attributes->get('feiraAtual');

        return [
            'numero' => [
                'required', 'string', 'max:20',
                Rule::unique('stands', 'numero')
                    ->where(fn ($query) => $query->where('feira_id', $feira?->id)->whereNull('deleted_at'))
                    ->ignore($this->route('stand')),
            ],
            'localizacao' => ['nullable', 'string', 'max:150'],
            'capacidade' => ['nullable', 'integer', 'min:0'],
            'responsavel_id' => ['nullable', 'exists:users,id'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'estado' => ['nullable', Rule::in(['disponivel', 'reservado', 'ocupado', 'inativo'])],
        ];
    }
}
