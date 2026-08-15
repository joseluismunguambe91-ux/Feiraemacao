<?php

namespace App\Http\Requests\Professor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Espelha RF19 (Etapa 1) e o dicionário de dados da tabela inscricoes
 * (Etapa 3). O professor nunca define feira_id, professor_id, estado ou os
 * campos de avaliação — esses são responsabilidade do Controller/Service.
 */
class InscricaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_participante' => ['required', Rule::in(['professor', 'aluno'])],
            // Obrigatória quando: (a) é o próprio Professor a participar em
            // gastronomia, ou (b) é um Aluno a inscrever-se sozinho sem ter
            // um registo de plantel gerido por um Professor — nesse caso
            // escreve a turma aqui mesmo, no momento da inscrição (pedido
            // pós-Etapa 10: não deve depender de mais ninguém). Quando o
            // Professor escolhe Aluno(s) do seu plantel, ou quando o Aluno
            // já tem um registo ligado (alunoLigado), a turma vem de lá e
            // este campo é ignorado (Controller).
            'turma' => [
                $this->deveIndicarTurma() ? 'required' : 'nullable',
                'string', 'max:50',
            ],
            'classe' => ['nullable', 'string', 'max:20'],
            // Só exigido quando é um Professor a inscrever em nome de
            // Aluno(s) — cada um tem de pertencer ao plantel do próprio
            // Professor (RF04). Quando é o Aluno a inscrever-se a si
            // próprio, o Controller resolve isto sozinho (alunoLigado()),
            // sem escolha manual.
            'alunos' => [
                $this->input('tipo_participante') === 'aluno' && $this->user()?->hasRole('professor') ? 'required' : 'nullable',
                'array',
            ],
            'alunos.*' => [
                Rule::exists('alunos', 'id')->where(fn ($query) => $query->where('professor_id', $this->user()?->id)),
            ],
            'telefone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:190'],
            'tipo_atividade' => ['required', Rule::in(['teatro', 'danca', 'musica', 'poesia', 'ciencias', 'artesanato', 'pintura', 'jogos', 'gastronomia', 'outro'])],
            'descricao' => ['nullable', 'string'],
            // Só para gastronomia: a Comissão só confirma/ajusta estes dois
            // valores ao aprovar, não pede-os de novo.
            'produto_nome' => [$this->input('tipo_atividade') === 'gastronomia' ? 'required' : 'nullable', 'string', 'max:120'],
            'produto_preco' => [$this->input('tipo_atividade') === 'gastronomia' ? 'required' : 'nullable', 'numeric', 'min:0'],
            // Opcional: quem tiver uma foto do prato à mão já a envia, para
            // aparecer na página pública assim que a Comissão aprovar.
            'produto_foto' => ['nullable', 'image', 'max:4096'],
            'numero_participantes' => ['required', 'integer', 'min:1'],
            'necessita_palco' => ['nullable', 'boolean'],
            'necessita_eletricidade' => ['nullable', 'boolean'],
            'necessita_projetor' => ['nullable', 'boolean'],
            'necessita_som' => ['nullable', 'boolean'],
            'numero_mesas' => ['nullable', 'integer', 'min:0'],
            'numero_cadeiras' => ['nullable', 'integer', 'min:0'],
            'horario_pretendido' => ['nullable', 'date_format:H:i'],
            'duracao_minutos' => ['nullable', 'integer', 'min:1'],
            'observacoes' => ['nullable', 'string'],
            'fotos.*' => ['nullable', 'image', 'max:4096'],
        ];
    }

    private function deveIndicarTurma(): bool
    {
        if ($this->input('tipo_participante') === 'professor') {
            return $this->input('tipo_atividade') === 'gastronomia';
        }

        $user = $this->user();

        return $user?->hasRole('aluno') && ! $user->alunoLigado;
    }

    /** Nomes amigáveis nas mensagens de erro — o formulário é pensado para crianças preencherem sozinhas. */
    public function attributes(): array
    {
        return [
            'tipo_participante' => 'quem vai participar',
            'turma' => 'turma',
            'classe' => 'classe',
            'alunos' => 'aluno(s)',
            'telefone' => 'número de telefone',
            'email' => 'email',
            'tipo_atividade' => 'o que vais fazer',
            'descricao' => 'descrição',
            'produto_nome' => 'nome do prato',
            'produto_preco' => 'preço',
            'produto_foto' => 'foto do prato',
            'numero_participantes' => 'número de participantes',
            'numero_mesas' => 'número de mesas',
            'numero_cadeiras' => 'número de cadeiras',
            'horario_pretendido' => 'hora que preferes',
            'duracao_minutos' => 'duração',
            'observacoes' => 'observações',
        ];
    }
}
