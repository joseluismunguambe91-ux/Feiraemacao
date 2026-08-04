<?php

namespace Database\Factories;

use App\Models\Aluno;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Aluno>
 */
class AlunoFactory extends Factory
{
    protected $model = Aluno::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'turma' => fake()->randomElement(['9A', '9B', '9C', '10A', '10B', '11A']),
            'professor_id' => User::factory(),
        ];
    }
}
