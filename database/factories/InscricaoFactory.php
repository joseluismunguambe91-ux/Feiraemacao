<?php

namespace Database\Factories;

use App\Models\Feira;
use App\Models\Inscricao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inscricao>
 */
class InscricaoFactory extends Factory
{
    protected $model = Inscricao::class;

    public function definition(): array
    {
        return [
            'feira_id' => Feira::factory(),
            'professor_id' => User::factory(),
            'tipo_participante' => 'professor',
            'turma' => fake()->randomElement(['9A', '9B', '9C', '10A']),
            'telefone' => fake()->numerify('84#######'),
            'email' => fake()->safeEmail(),
            'tipo_atividade' => fake()->randomElement(['teatro', 'danca', 'musica', 'poesia']),
            'descricao' => fake()->sentence(),
            'numero_participantes' => fake()->numberBetween(5, 30),
            'horario_pretendido' => '09:30:00',
            'duracao_minutos' => 45,
            'estado' => 'pendente',
        ];
    }

    public function aprovada(): static
    {
        return $this->state(fn (array $attributes) => ['estado' => 'aprovada', 'avaliado_em' => now()]);
    }

    public function rejeitada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'rejeitada',
            'comentario_avaliacao' => fake()->sentence(),
            'avaliado_em' => now(),
        ]);
    }
}
