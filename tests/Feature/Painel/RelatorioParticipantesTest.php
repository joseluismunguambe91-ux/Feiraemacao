<?php

namespace Tests\Feature\Painel;

use App\Models\Aluno;
use App\Models\Feira;
use App\Models\Inscricao;
use App\Models\RelatorioGerado;
use App\Models\Stand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CriaUtilizadoresComPapel;
use Tests\TestCase;

/**
 * Pedido pós-Etapa 10: o Administrador/Comissão descarrega uma lista com
 * todos os participantes aprovados, um por linha (mesmo quando uma
 * inscrição representa vários Alunos), com papel, turma e banca — ver
 * docs/10-documentacao.md.
 */
class RelatorioParticipantesTest extends TestCase
{
    use CriaUtilizadoresComPapel;
    use RefreshDatabase;

    public function test_relatorio_de_participantes_lista_uma_linha_por_pessoa_com_banca(): void
    {
        Storage::fake('public');
        Notification::fake();

        $feira = Feira::factory()->publicada()->create();
        $comissao = $this->criarComissao();
        $professor = $this->criarProfessor();
        $stand = Stand::factory()->create(['feira_id' => $feira->id]);

        // Inscrição de gastronomia em nome de dois alunos, já aprovada com stand.
        $aluno1 = Aluno::factory()->create(['professor_id' => $professor->id, 'turma' => '9C']);
        $aluno2 = Aluno::factory()->create(['professor_id' => $professor->id, 'turma' => '9C']);
        $inscricaoGastronomia = Inscricao::factory()->aprovada()->create([
            'feira_id' => $feira->id,
            'professor_id' => $professor->id,
            'tipo_participante' => 'aluno',
            'tipo_atividade' => 'gastronomia',
            'turma' => '9C',
        ]);
        $inscricaoGastronomia->alunos()->sync([$aluno1->id, $aluno2->id]);
        $inscricaoGastronomia->expositor()->create([
            'feira_id' => $feira->id,
            'professor_id' => $professor->id,
            'turma' => '9C',
            'categoria' => 'Gastronomia',
            'stand_id' => $stand->id,
            'estado' => 'ativo',
        ]);

        // Inscrição do próprio professor, sem banca.
        Inscricao::factory()->aprovada()->create([
            'feira_id' => $feira->id,
            'professor_id' => $professor->id,
            'tipo_participante' => 'professor',
            'tipo_atividade' => 'danca',
            'turma' => '10A',
        ]);

        $this->withSession(['feira_atual_id' => $feira->id]);
        $response = $this->actingAs($comissao)->post('/painel/relatorios', [
            'tipo' => 'participantes',
            'formato' => 'excel',
        ]);

        $response->assertRedirect('/painel/relatorios');

        $relatorio = RelatorioGerado::firstOrFail();
        $this->assertSame('concluido', $relatorio->estado);

        $csv = Storage::disk('public')->get($relatorio->path_ficheiro);

        $this->assertStringContainsString($aluno1->nome, $csv);
        $this->assertStringContainsString($aluno2->nome, $csv);
        $this->assertStringContainsString('Aluno', $csv);
        $this->assertStringContainsString($stand->numero, $csv);
        $this->assertStringContainsString($professor->name, $csv);
        $this->assertStringContainsString('Professor', $csv);
        $this->assertStringContainsString('Dança', $csv);
    }
}
