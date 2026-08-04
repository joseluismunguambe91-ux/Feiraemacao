<?php

namespace Tests\Feature\Painel;

use App\Models\Expositor;
use App\Models\Feira;
use App\Models\Stand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaUtilizadoresComPapel;
use Tests\TestCase;

/**
 * RN03 (Etapa 3): 1 stand só pode estar associado a um expositor de cada vez.
 */
class ExpositorStandTest extends TestCase
{
    use CriaUtilizadoresComPapel;
    use RefreshDatabase;

    public function test_comissao_cria_stand_com_qr_token_automatico(): void
    {
        $feira = Feira::factory()->create();
        $comissao = $this->criarComissao();
        $this->sessionFeiraAtual($feira);

        $this->actingAs($comissao)->post('/painel/stands', [
            'numero' => '07',
            'localizacao' => 'Pátio Central',
        ])->assertRedirect('/painel/stands');

        $stand = Stand::where('numero', '07')->first();
        $this->assertNotNull($stand);
        $this->assertNotEmpty($stand->qr_token);
    }

    public function test_rn03_bloqueia_atribuir_o_mesmo_stand_a_dois_expositores(): void
    {
        $feira = Feira::factory()->create();
        $stand = Stand::factory()->create(['feira_id' => $feira->id]);
        $professor = $this->criarProfessor();
        $comissao = $this->criarComissao();
        $this->sessionFeiraAtual($feira);

        Expositor::factory()->create([
            'feira_id' => $feira->id,
            'professor_id' => $professor->id,
            'stand_id' => $stand->id,
        ]);

        $outroProfessor = $this->criarProfessor();

        $response = $this->actingAs($comissao)->post('/painel/expositores', [
            'professor_id' => $outroProfessor->id,
            'turma' => '10B',
            'stand_id' => $stand->id,
        ]);

        $response->assertSessionHasErrors('stand_id');
        $this->assertSame(1, Expositor::where('stand_id', $stand->id)->count());
    }

    /** Regista a feira em contexto na sessão, como o seletor de /painel/trocar-feira faria. */
    private function sessionFeiraAtual(Feira $feira): void
    {
        $this->withSession(['feira_atual_id' => $feira->id]);
    }
}
