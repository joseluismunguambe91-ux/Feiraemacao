<?php

namespace Database\Factories;

use App\Models\Expositor;
use App\Models\Feira;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expositor>
 */
class ExpositorFactory extends Factory
{
    protected $model = Expositor::class;

    public function definition(): array
    {
        return [
            'feira_id' => Feira::factory(),
            'professor_id' => User::factory(),
            'turma' => fake()->randomElement(['9A', '9B', '9C', '10A', '10B', '11A']),
            'categoria' => fake()->randomElement(['Gastronomia', 'Artesanato', 'Ciências']),
            'descricao' => fake()->sentence(),
            'estado' => 'pendente',
        ];
    }
}
