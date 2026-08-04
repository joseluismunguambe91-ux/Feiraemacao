<?php

namespace Database\Factories;

use App\Models\Feira;
use App\Models\GastronomiaItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GastronomiaItem>
 */
class GastronomiaItemFactory extends Factory
{
    protected $model = GastronomiaItem::class;

    public function definition(): array
    {
        return [
            'feira_id' => Feira::factory(),
            'nome' => fake()->unique()->word(),
            'categoria' => fake()->randomElement(['Prato principal', 'Sobremesa', 'Bebida']),
            'descricao' => fake()->sentence(),
            'preco' => fake()->randomFloat(2, 20, 500),
            'disponivel' => true,
        ];
    }
}
