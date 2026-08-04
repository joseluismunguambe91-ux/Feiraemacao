<?php

namespace App\Http\Requests\Painel;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Regras espelham o dicionário de dados da Etapa 3 (tabela gastronomia_itens)
 * — CHECK (preco >= 0) já garantido na BD, aqui só a validação amigável.
 */
class GastronomiaItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:120'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'descricao' => ['nullable', 'string'],
            'preco' => ['required', 'numeric', 'min:0'],
            'foto' => ['nullable', 'image', 'max:4096'],
            'ingredientes' => ['nullable', 'string'],
            'disponivel' => ['nullable', 'boolean'],
            'quantidade_disponivel' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
