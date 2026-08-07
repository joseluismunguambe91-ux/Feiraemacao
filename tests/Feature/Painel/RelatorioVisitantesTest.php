<?php

namespace Tests\Feature\Painel;

use App\Models\Feira;
use App\Models\RelatorioGerado;
use App\Models\Visita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CriaUtilizadoresComPapel;
use Tests\TestCase;

/**
 * Pedido pós-Etapa 10: relatório com o número de visitantes por dia
 * (dados vindos de RegistarVisita), ver docs/10-documentacao.md.
 */
class RelatorioVisitantesTest extends TestCase
{
    use CriaUtilizadoresComPapel;
    use RefreshDatabase;

    public function test_relatorio_de_visitantes_soma_por_dia(): void
    {
        Storage::fake('public');
        Notification::fake();

        $feira = Feira::factory()->publicada()->create();
        $comissao = $this->criarComissao();

        Visita::factory()->count(3)->create(['feira_id' => $feira->id, 'data' => '2026-09-01']);
        Visita::factory()->count(2)->create(['feira_id' => $feira->id, 'data' => '2026-09-02']);

        $this->withSession(['feira_atual_id' => $feira->id]);
        $response = $this->actingAs($comissao)->post('/painel/relatorios', [
            'tipo' => 'visitantes',
            'formato' => 'excel',
        ]);

        $response->assertRedirect('/painel/relatorios');

        $relatorio = RelatorioGerado::firstOrFail();
        $this->assertSame('concluido', $relatorio->estado);

        $csv = Storage::disk('public')->get($relatorio->path_ficheiro);

        $this->assertStringContainsString('01/09/2026', $csv);
        $this->assertStringContainsString('02/09/2026', $csv);
        $this->assertStringContainsString('3', $csv);
        $this->assertStringContainsString('2', $csv);
    }
}
