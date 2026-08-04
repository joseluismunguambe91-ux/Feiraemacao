<?php

namespace Tests\Unit\Services;

use App\Models\Feira;
use App\Models\ProgramacaoItem;
use App\Services\ConflitoAgendaVerificador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RN04 (Etapa 3): sem sobreposição de horário no mesmo palco, na mesma data.
 */
class ConflitoAgendaVerificadorTest extends TestCase
{
    use RefreshDatabase;

    private ConflitoAgendaVerificador $verificador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->verificador = new ConflitoAgendaVerificador();
    }

    private function criarItemExistente(Feira $feira): ProgramacaoItem
    {
        return ProgramacaoItem::factory()->create([
            'feira_id' => $feira->id,
            'data' => '2026-09-12',
            'hora_inicio' => '09:30:00',
            'hora_fim' => '10:15:00',
            'palco' => 'Palco Principal',
        ]);
    }

    public function test_deteta_sobreposicao_no_mesmo_palco_e_data(): void
    {
        $feira = Feira::factory()->create();
        $this->criarItemExistente($feira);

        $conflito = $this->verificador->existe($feira->id, '2026-09-12', 'Palco Principal', '09:45', '10:30');

        $this->assertTrue($conflito);
    }

    public function test_sem_conflito_em_palco_diferente(): void
    {
        $feira = Feira::factory()->create();
        $this->criarItemExistente($feira);

        $conflito = $this->verificador->existe($feira->id, '2026-09-12', 'Palco Secundário', '09:45', '10:30');

        $this->assertFalse($conflito);
    }

    public function test_sem_conflito_em_horario_adjacente_nao_sobreposto(): void
    {
        $feira = Feira::factory()->create();
        $this->criarItemExistente($feira);

        $conflito = $this->verificador->existe($feira->id, '2026-09-12', 'Palco Principal', '10:15', '11:00');

        $this->assertFalse($conflito);
    }

    public function test_sem_palco_nunca_ha_conflito(): void
    {
        $feira = Feira::factory()->create();
        $this->criarItemExistente($feira);

        $conflito = $this->verificador->existe($feira->id, '2026-09-12', null, '09:45', '10:30');

        $this->assertFalse($conflito);
    }

    public function test_ignora_o_proprio_item_ao_reorganizar(): void
    {
        $feira = Feira::factory()->create();
        $item = $this->criarItemExistente($feira);

        $conflito = $this->verificador->existe(
            $feira->id, '2026-09-12', 'Palco Principal', '09:30', '10:15', $item->id
        );

        $this->assertFalse($conflito);
    }
}
