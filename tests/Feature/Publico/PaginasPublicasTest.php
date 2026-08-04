<?php

namespace Tests\Feature\Publico;

use App\Models\Atividade;
use App\Models\Expositor;
use App\Models\Feira;
use App\Models\GastronomiaItem;
use App\Models\Stand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RF28/RF29 (Etapa 1) e RN10 (Etapa 3): a página pública funciona sem
 * login, com ou sem edição ativa, e reflete sempre a edição publicada.
 */
class PaginasPublicasTest extends TestCase
{
    use RefreshDatabase;

    private const ROTAS = [
        '/', '/sobre', '/programacao', '/atividades', '/gastronomia',
        '/expositores', '/mapa', '/galeria', '/patrocinadores', '/contacto',
    ];

    public function test_todas_as_paginas_respondem_sem_feira_ativa(): void
    {
        foreach (self::ROTAS as $rota) {
            $this->get($rota)->assertOk();
        }
    }

    public function test_todas_as_paginas_respondem_com_feira_ativa_e_conteudo(): void
    {
        $feira = Feira::factory()->publicada()->create(['tema' => 'Feira Sabores e Saberes']);
        Atividade::factory()->create(['feira_id' => $feira->id, 'titulo' => 'Marrabenta']);
        GastronomiaItem::factory()->create(['feira_id' => $feira->id, 'nome' => 'Matapa com Camarão']);
        $expositor = Expositor::factory()->create(['feira_id' => $feira->id, 'estado' => 'ativo', 'turma' => '9C']);
        Stand::factory()->create(['feira_id' => $feira->id, 'numero' => '07']);

        $this->get('/')->assertOk()->assertSee('Feira Sabores e Saberes');
        $this->get('/atividades')->assertOk()->assertSee('Marrabenta');
        $this->get('/gastronomia')->assertOk()->assertSee('Matapa com Camarão');
        $this->get('/expositores')->assertOk()->assertSee('9C');
    }

    public function test_pagina_individual_do_stand_via_qr_token(): void
    {
        $feira = Feira::factory()->publicada()->create();
        $expositor = Expositor::factory()->create(['feira_id' => $feira->id]);
        $stand = Stand::factory()->create(['feira_id' => $feira->id]);
        $expositor->update(['stand_id' => $stand->id]);

        $response = $this->get("/stand/{$stand->qr_token}");

        $response->assertOk()->assertSee($expositor->fresh()->turma);
    }

    public function test_stand_com_token_invalido_devolve_404(): void
    {
        $this->get('/stand/token-que-nao-existe')->assertNotFound();
    }

    public function test_pesquisa_encontra_atividade_pelo_titulo(): void
    {
        $feira = Feira::factory()->publicada()->create();
        Atividade::factory()->create(['feira_id' => $feira->id, 'titulo' => 'Marrabenta: Raízes em Movimento']);

        $response = $this->get('/pesquisa?q=Marrabenta');

        $response->assertOk()->assertSee('Marrabenta: Raízes em Movimento');
    }

    public function test_contacto_grava_mensagem(): void
    {
        $response = $this->post('/contacto', [
            'nome' => 'Visitante',
            'email' => 'visitante@teste.local',
            'mensagem' => 'A que horas fecha no domingo?',
        ]);

        $response->assertRedirect('/contacto');
        $this->assertDatabaseHas('mensagens_contacto', [
            'nome' => 'Visitante',
            'mensagem' => 'A que horas fecha no domingo?',
        ]);
    }

    public function test_contacto_exige_campos_obrigatorios(): void
    {
        $response = $this->post('/contacto', []);

        $response->assertSessionHasErrors(['nome', 'email', 'mensagem']);
    }
}
