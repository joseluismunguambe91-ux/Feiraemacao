<?php

namespace Database\Factories;

use App\Models\Atividade;
use App\Models\Feira;
use App\Models\ProgramacaoItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramacaoItem>
 */
class ProgramacaoItemFactory extends Factory
{
    protected $model = ProgramacaoItem::class;

    public function definition(): array
    {
        // feira_id normalmente é passado explicitamente pelo teste (tem de
        // corresponder ao feira_id da atividade) — o valor por omissão aqui
        // só serve para não rebentar quando ninguém o define.
        return [
            'feira_id' => Feira::factory(),
            'atividade_id' => Atividade::factory(),
            'data' => now()->addWeek()->format('Y-m-d'),
            'hora_inicio' => '09:00:00',
            'hora_fim' => '09:45:00',
            'local' => 'Pátio Central',
            'palco' => 'Palco Principal',
        ];
    }
}
