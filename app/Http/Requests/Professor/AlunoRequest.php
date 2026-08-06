<?php

namespace App\Http\Requests\Professor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * RF04 (Etapa 1): o professor regista os seus próprios alunos. `user_id`
 * é opcional — só quando um aluno já tem conta própria e o professor quer
 * ligá-la, para as inscrições desse aluno se autoatribuírem sem escolha
 * manual (Professor\InscricaoController::alunoDoUtilizadorAtual()).
 */
class AlunoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:150'],
            'classe' => ['nullable', 'string', 'max:20'],
            'turma' => ['required', 'string', 'max:50'],
            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('alunos', 'user_id')->ignore($this->route('aluno')),
            ],
        ];
    }
}
