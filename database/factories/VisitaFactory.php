<?php

namespace Database\Factories;

use App\Models\Feira;
use App\Models\Visita;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Visita>
 */
class VisitaFactory extends Factory
{
    protected $model = Visita::class;

    public function definition(): array
    {
        return [
            'feira_id' => Feira::factory(),
            'sessao_id' => Str::random(40),
            'data' => now()->toDateString(),
        ];
    }
}
