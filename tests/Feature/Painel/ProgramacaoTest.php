<?php

namespace Tests\Feature\Painel;

use App\Models\Atividade;
use App\Models\Feira;
use App\Models\ProgramacaoItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaUtilizadoresComPapel;
use Tests\TestCase;

/**
 * RF24/RF25 (Etapa 1) e RN04 (Etapa 3): agendar uma atividade direta e
 * reorganizar sem nunca permitir sobreposição no mesmo palco/data.
 */
class ProgramacaoTest extends TestCase
{
    use CriaUtilizadoresComPapel;
    use RefreshDatabase;

    public function test_pagina_vazia_orienta_a_criar_uma_atividade(): void
    {
        $feira = Feira::factory()->create();
        $comissao = $this->criarComissao();
        $this->withSession(['feira_atual_id' => $feira->id]);

        $response = $this->actingAs($comissao)->get('/painel/programacao');

        $response->assertOk();
        $response->assertSee('Ainda não há nenhuma atividade para agendar');
        $response->assertSee(route('painel.atividades.create'), false);
    }

    public function test_agenda_uma_atividade_sem_conflito(): void
    {
        $feira = Feira::factory()->create();
        $atividade = Atividade::factory()->create(['feira_id' => $feira->id]);
        $comissao = $this->criarComissao();
        $this->withSession(['feira_atual_id' => $feira->id]);

        $response = $this->actingAs($comissao)->post("/painel/programacao/agendar/{$atividade->id}", [
            'data' => $feira->data_inicio->format('Y-m-d'),
            'hora_inicio' => '11:00',
            'hora_fim' => '11:45',
            'local' => 'Pátio Central',
            'palco' => 'Palco Secundário',
        ]);

        $response->assertRedirect('/painel/programacao');
        $this->assertDatabaseHas('programacao_itens', [
            'atividade_id' => $atividade->id,
            'palco' => 'Palco Secundário',
        ]);
    }

    public function test_rn04_bloqueia_agendamento_sobreposto(): void
    {
        $feira = Feira::factory()->create();
        ProgramacaoItem::factory()->create([
            'feira_id' => $feira->id,
            'data' => $feira->data_inicio->format('Y-m-d'),
            'hora_inicio' => '09:30:00',
            'hora_fim' => '10:15:00',
            'palco' => 'Palco Principal',
        ]);
        $atividade = Atividade::factory()->create(['feira_id' => $feira->id]);
        $comissao = $this->criarComissao();
        $this->withSession(['feira_atual_id' => $feira->id]);

        $response = $this->actingAs($comissao)->post("/painel/programacao/agendar/{$atividade->id}", [
            'data' => $feira->data_inicio->format('Y-m-d'),
            'hora_inicio' => '09:45',
            'hora_fim' => '10:30',
            'local' => 'Pátio Central',
            'palco' => 'Palco Principal',
        ]);

        $response->assertSessionHas('erro');
        $this->assertDatabaseMissing('programacao_itens', ['atividade_id' => $atividade->id]);
    }

    public function test_endpoint_ajax_de_verificacao_de_conflito(): void
    {
        $feira = Feira::factory()->create();
        ProgramacaoItem::factory()->create([
            'feira_id' => $feira->id,
            'data' => $feira->data_inicio->format('Y-m-d'),
            'hora_inicio' => '09:30:00',
            'hora_fim' => '10:15:00',
            'palco' => 'Palco Principal',
        ]);
        $comissao = $this->criarComissao();
        $this->withSession(['feira_atual_id' => $feira->id]);

        $comConflito = $this->actingAs($comissao)->postJson('/painel/programacao/verificar-conflito', [
            'feira_id' => $feira->id,
            'data' => $feira->data_inicio->format('Y-m-d'),
            'hora_inicio' => '09:45',
            'hora_fim' => '10:30',
            'palco' => 'Palco Principal',
        ]);
        $comConflito->assertJson(['conflito' => true]);

        $semConflito = $this->actingAs($comissao)->postJson('/painel/programacao/verificar-conflito', [
            'feira_id' => $feira->id,
            'data' => $feira->data_inicio->format('Y-m-d'),
            'hora_inicio' => '14:00',
            'hora_fim' => '14:30',
            'palco' => 'Palco Principal',
        ]);
        $semConflito->assertJson(['conflito' => false]);
    }
}
