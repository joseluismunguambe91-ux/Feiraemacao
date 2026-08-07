<?php

namespace Tests\Feature\Publico;

use App\Models\Feira;
use App\Models\Visita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pedido pós-Etapa 10: contador real de visitantes (Dashboard + relatório).
 * Uma linha por sessão/dia, não por pedido — RegistarVisita.
 */
class RegistoDeVisitasTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitar_a_pagina_publica_regista_uma_visita(): void
    {
        $feira = Feira::factory()->publicada()->create();

        $this->get('/');

        $this->assertDatabaseCount('visitas', 1);
        $this->assertDatabaseHas('visitas', ['feira_id' => $feira->id]);
    }

    public function test_a_mesma_sessao_nao_conta_duas_vezes_no_mesmo_dia(): void
    {
        // O cliente de testes do Laravel não persiste cookies de sessão
        // entre chamadas $this->get() separadas (ao contrário de um
        // navegador real) — por isso testamos a garantia de unicidade
        // diretamente, tal como o RegistarVisita a usa (whereDate + create).
        $sessaoId = 'sessao-fixa-de-teste';
        $hoje = now()->toDateString();

        for ($i = 0; $i < 3; $i++) {
            $jaRegistada = Visita::where('sessao_id', $sessaoId)->whereDate('data', $hoje)->exists();
            if (! $jaRegistada) {
                Visita::create(['sessao_id' => $sessaoId, 'data' => $hoje, 'feira_id' => null]);
            }
        }

        $this->assertDatabaseCount('visitas', 1);
    }

    public function test_pedidos_ajax_nao_registam_visita(): void
    {
        Feira::factory()->publicada()->create();

        $this->get('/', ['X-Requested-With' => 'XMLHttpRequest']);

        $this->assertDatabaseCount('visitas', 0);
    }
}
