<?php

/**
 * Mensagens de validação em português (Etapa 7, item 6 — "mensagens de
 * validação em português"). Cobre as regras usadas nos módulos já
 * construídos; novas regras que apareçam nas próximas fases só precisam de
 * acrescentar a chave correspondente aqui, não de duplicar texto no código.
 */
return [
    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute deve ser texto.',
    'max' => [
        'string' => 'O campo :attribute não pode ter mais de :max carateres.',
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'file' => 'O ficheiro :attribute não pode ter mais de :max kilobytes.',
    ],
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min carateres.',
        'numeric' => 'O campo :attribute deve ser pelo menos :min.',
    ],
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'numeric' => 'O campo :attribute deve ser um número.',
    'email' => 'O campo :attribute deve ser um email válido.',
    'date' => 'O campo :attribute deve ser uma data válida.',
    'date_format' => 'O campo :attribute deve estar no formato :format.',
    'after' => 'O campo :attribute deve ser posterior a :date.',
    'after_or_equal' => 'O campo :attribute deve ser posterior ou igual a :date.',
    'image' => 'O campo :attribute deve ser uma imagem.',
    'mimes' => 'O campo :attribute deve ser um ficheiro do tipo: :values.',
    'exists' => 'O valor selecionado para :attribute é inválido.',
    'unique' => 'Já existe um registo com este valor em :attribute.',
    'confirmed' => 'A confirmação de :attribute não corresponde.',
    'in' => 'O valor selecionado para :attribute é inválido.',
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',

    'attributes' => [
        'tema' => 'tema',
        'descricao' => 'descrição',
        'data_inicio' => 'data de início',
        'data_fim' => 'data de fim',
        'hora_abertura' => 'hora de abertura',
        'hora_encerramento' => 'hora de encerramento',
        'local' => 'local',
        'banner' => 'banner',
        'logotipo' => 'logótipo',
        'regulamento' => 'regulamento',
        'nome' => 'nome',
        'categoria' => 'categoria',
        'preco' => 'preço',
        'foto' => 'fotografia',
        'ingredientes' => 'ingredientes',
        'quantidade_disponivel' => 'quantidade disponível',
        'tipo' => 'tipo',
        'titulo' => 'título',
        'responsavel_id' => 'responsável',
        'participantes_previstos' => 'participantes previstos',
        'professor_id' => 'professor',
        'turma' => 'turma',
        'stand_id' => 'stand',
        'estado' => 'estado',
        'numero' => 'número',
        'localizacao' => 'localização',
        'capacidade' => 'capacidade',
        'email' => 'email',
        'password' => 'senha',
    ],
];
