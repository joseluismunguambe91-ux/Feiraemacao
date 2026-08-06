<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * RF03 (Etapa 1): só o Administrador cria/edita contas e atribui papéis.
 * Password é obrigatória a criar, opcional a editar (deixa em branco para
 * manter a atual).
 */
class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $utilizador = $this->route('utilizador');

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($utilizador)],
            'telefone' => ['nullable', 'string', 'max:30'],
            // Só relevante para contas de Aluno que se inscrevem sozinhas,
            // sem passar por um plantel registado por um Professor.
            'turma' => ['nullable', 'string', 'max:50'],
            'password' => [$utilizador ? 'nullable' : 'required', 'string', 'min:8'],
            'ativo' => ['nullable', 'boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ];
    }
}
