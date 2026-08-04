<?php

namespace App\Http\Requests\Publico;

use Illuminate\Foundation\Http\FormRequest;

class ContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190'],
            'assunto' => ['nullable', 'string', 'max:150'],
            'mensagem' => ['required', 'string', 'max:2000'],
        ];
    }
}
