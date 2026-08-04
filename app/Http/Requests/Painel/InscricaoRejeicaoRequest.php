<?php

namespace App\Http\Requests\Painel;

use Illuminate\Foundation\Http\FormRequest;

/**
 * RN06 (Etapa 3): comentário obrigatório sempre que uma inscrição é
 * rejeitada.
 */
class InscricaoRejeicaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comentario_avaliacao' => ['required', 'string', 'max:1000'],
        ];
    }
}
