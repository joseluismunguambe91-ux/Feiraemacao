<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Registo público, exclusivo para Alunos (pedido pós-Etapa 10 — Professor,
 * Comissão e Administrador continuam a ser sempre criados pelo
 * Administrador, RF03). O papel nunca vem do pedido, é sempre "aluno",
 * atribuído no controller — nunca confiar em input do utilizador para isso.
 */
class RegistoAlunoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Já existe uma conta com este email — experimenta entrar em vez de criar outra.',
        ];
    }
}
