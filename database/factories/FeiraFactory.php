<?php

namespace Database\Factories;

use App\Models\Feira;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feira>
 */
class FeiraFactory extends Factory
{
    protected $model = Feira::class;

    public function definition(): array
    {
        $inicio = fake()->dateTimeBetween('+1 week', '+2 weeks');
        $fim = (clone $inicio)->modify('+1 day');

        return [
            'tema' => 'Feira '.fake()->unique()->words(2, true),
            'descricao' => fake()->sentence(),
            'data_inicio' => $inicio->format('Y-m-d'),
            'data_fim' => $fim->format('Y-m-d'),
            'hora_abertura' => '08:00:00',
            'hora_encerramento' => '18:00:00',
            'local' => fake()->company(),
            'estado' => 'rascunho',
        ];
    }

    public function publicada(): static
    {
        return $this->state(fn (array $attributes) => ['estado' => 'publicada']);
    }

    public function emCurso(): static
    {
        return $this->state(fn (array $attributes) => ['estado' => 'em_curso']);
    }

    public function arquivada(): static
    {
        return $this->state(fn (array $attributes) => ['estado' => 'arquivada']);
    }
}
