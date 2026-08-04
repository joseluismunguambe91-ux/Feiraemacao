<?php

namespace Tests\Feature\Painel;

use App\Models\Feira;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaUtilizadoresComPapel;
use Tests\TestCase;

/**
 * RF06-RF09 (Etapa 1), RN02 (Etapa 3) e RC02/RC05 (Etapa 4) testados ao
 * nível HTTP, não só no Service isolado.
 */
class FeiraGestaoTest extends TestCase
{
    use CriaUtilizadoresComPapel;
    use RefreshDatabase;

    public function test_administrador_cria_uma_edicao(): void
    {
        $admin = $this->criarAdministrador();

        $response = $this->actingAs($admin)->post('/admin/feiras', [
            'tema' => 'Feira Sabores 2026',
            'data_inicio' => '2026-09-12',
            'data_fim' => '2026-09-13',
            'hora_abertura' => '08:00',
            'hora_encerramento' => '18:00',
            'local' => 'Escola Secundária',
        ]);

        $response->assertRedirect('/admin/feiras');
        $this->assertDatabaseHas('feiras', ['tema' => 'Feira Sabores 2026', 'estado' => 'rascunho']);
    }

    public function test_comissao_nao_pode_criar_edicoes(): void
    {
        $comissao = $this->criarComissao();

        $this->actingAs($comissao)->post('/admin/feiras', [
            'tema' => 'Feira Ilegal',
            'data_inicio' => '2026-09-12',
            'data_fim' => '2026-09-13',
            'hora_abertura' => '08:00',
            'hora_encerramento' => '18:00',
            'local' => 'Escola',
        ])->assertForbidden();

        $this->assertDatabaseMissing('feiras', ['tema' => 'Feira Ilegal']);
    }

    public function test_comissao_pode_avancar_o_estado_mas_nao_reverter(): void
    {
        $feira = Feira::factory()->create(['estado' => 'rascunho']);
        $comissao = $this->criarComissao();

        $this->actingAs($comissao)->post("/painel/feiras/{$feira->id}/avancar-estado")
            ->assertRedirect();
        $this->assertSame('publicada', $feira->fresh()->estado);

        $this->actingAs($comissao)->post("/admin/feiras/{$feira->id}/reverter-estado")
            ->assertForbidden();
    }

    public function test_rn02_bloqueia_segunda_edicao_ativa(): void
    {
        Feira::factory()->publicada()->create();
        $candidata = Feira::factory()->create(['estado' => 'rascunho']);
        $admin = $this->criarAdministrador();

        $response = $this->actingAs($admin)->post("/painel/feiras/{$candidata->id}/avancar-estado");

        $response->assertRedirect();
        $response->assertSessionHas('erro');
        $this->assertSame('rascunho', $candidata->fresh()->estado);
    }

    public function test_gate_bloqueia_edicao_de_feira_arquivada(): void
    {
        $feira = Feira::factory()->arquivada()->create();
        $admin = $this->criarAdministrador();

        $response = $this->actingAs($admin)->put("/admin/feiras/{$feira->id}", [
            'tema' => 'Tentativa de editar',
            'data_inicio' => $feira->data_inicio->format('Y-m-d'),
            'data_fim' => $feira->data_fim->format('Y-m-d'),
            'hora_abertura' => '08:00',
            'hora_encerramento' => '18:00',
            'local' => $feira->local,
        ]);

        $response->assertRedirect('/admin/feiras');
        $response->assertSessionHas('erro');
        $this->assertNotSame('Tentativa de editar', $feira->fresh()->tema);
    }
}
