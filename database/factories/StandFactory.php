<?php

namespace Database\Factories;

use App\Models\Feira;
use App\Models\Stand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Stand>
 */
class StandFactory extends Factory
{
    protected $model = Stand::class;

    public function definition(): array
    {
        return [
            'feira_id' => Feira::factory(),
            'numero' => (string) fake()->unique()->numberBetween(1, 9000),
            'localizacao' => fake()->randomElement(['Pátio Central', 'Sala de Artes', 'Corredor Principal']),
            'capacidade' => fake()->numberBetween(2, 10),
            'categoria' => fake()->randomElement(['Gastronomia', 'Artesanato', 'Ciências']),
            'estado' => 'disponivel',
            'qr_token' => Str::random(12),
        ];
    }
}
