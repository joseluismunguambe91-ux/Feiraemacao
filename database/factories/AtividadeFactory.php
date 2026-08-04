<?php

namespace Database\Factories;

use App\Models\Atividade;
use App\Models\Feira;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Atividade>
 */
class AtividadeFactory extends Factory
{
    protected $model = Atividade::class;

    public function definition(): array
    {
        return [
            'feira_id' => Feira::factory(),
            'tipo' => fake()->randomElement(['teatro', 'danca', 'musica', 'poesia', 'ciencias', 'artesanato', 'pintura', 'jogos', 'outro']),
            'titulo' => fake()->unique()->sentence(3),
            'descricao' => fake()->sentence(),
            'participantes_previstos' => fake()->numberBetween(5, 30),
            'estado' => 'planeada',
        ];
    }
}
