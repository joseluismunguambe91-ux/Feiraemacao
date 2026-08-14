<?php

namespace Tests\Unit\Services;

use App\Models\Expositor;
use App\Models\Feira;
use App\Models\Inscricao;
use App\Models\ProgramacaoItem;
use App\Models\Stand;
use App\Models\User;
use App\Notifications\InscricaoAvaliada;
use App\Services\ConflitoAgendaVerificador;
use App\Services\InscricaoAprovacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

/**
 * RF21/RF22 (Etapa 1), RN04/RN07 (Etapa 3): o coração do fluxo de aprovação
 * — aprovar cria Atividade + ProgramacaoItem numa transação e nunca deixa
 * passar um conflito de horário.
 */
class InscricaoAprovacaoServiceTest extends TestCase
{
    use RefreshDatabase;

    private InscricaoAprovacaoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InscricaoAprovacaoService(new ConflitoAgendaVerificador());
    }

    public function test_aprovar_cria_atividade_e_item_de_programacao(): void
    {
        Notification::fake();

        $feira = Feira::factory()->create();
        $professor = User::factory()->create();
        $inscricao = Inscricao::factory()->create([
            'feira_id' => $feira->id,
            'professor_id' => $professor->id,
            'numero_participantes' => 15,
        ]);
        $avaliador = User::factory()->create();

        $this->service->aprovar($inscricao, $avaliador, [
            'titulo' => 'Marrabenta',
            'data' => '2026-09-12',
            'hora_inicio' => '09:30',
            'hora_fim' => '10:15',
            'local' => 'Pátio Central',
            'palco' => 'Palco Principal',
        ]);

        $inscricao->refresh();
        $this->assertSame('aprovada', $inscricao->estado);
        $this->assertSame($avaliador->id, $inscricao->avaliado_por);
        $this->assertNotNull($inscricao->avaliado_em);

        $this->assertDatabaseHas('atividades', [
            'inscricao_id' => $inscricao->id,
            'titulo' => 'Marrabenta',
            'participantes_previstos' => 15,
            'estado' => 'confirmada',
        ]);

        $atividade = $inscricao->fresh()->atividade;
        $this->assertNotNull($atividade);

        // Comparação via Carbon (atributo já convertido pelo cast), não por
        // igualdade de string crua — a serialização de "data" varia consoante
        // o driver (MySQL trunca para DATE, SQLite guarda com hora).
        $item = $atividade->itensProgramacao()->first();
        $this->assertNotNull($item);
        $this->assertSame('2026-09-12', $item->data->format('Y-m-d'));
        $this->assertSame('Palco Principal', $item->palco);

        Notification::assertSentTo($professor, InscricaoAvaliada::class);
    }

    public function test_aprovar_bloqueia_quando_ha_conflito_de_horario(): void
    {
        $feira = Feira::factory()->create();

        ProgramacaoItem::factory()->create([
            'feira_id' => $feira->id,
            'data' => '2026-09-12',
            'hora_inicio' => '09:30:00',
            'hora_fim' => '10:15:00',
            'palco' => 'Palco Principal',
        ]);

        $inscricao = Inscricao::factory()->create(['feira_id' => $feira->id]);
        $avaliador = User::factory()->create();

        try {
            $this->service->aprovar($inscricao, $avaliador, [
                'titulo' => 'Atividade em conflito',
                'data' => '2026-09-12',
                'hora_inicio' => '09:45',
                'hora_fim' => '10:30',
                'local' => 'Pátio Central',
                'palco' => 'Palco Principal',
            ]);
            $this->fail('Devia ter lançado RuntimeException por conflito de horário.');
        } catch (RuntimeException) {
            // esperado
        }

        $this->assertSame('pendente', $inscricao->fresh()->estado);
        $this->assertSame(0, $inscricao->fresh()->atividade()->count());
    }

    public function test_aprovar_bloqueia_inscricao_ja_avaliada(): void
    {
        $inscricao = Inscricao::factory()->aprovada()->create();
        $avaliador = User::factory()->create();

        $this->expectException(RuntimeException::class);

        $this->service->aprovar($inscricao, $avaliador, [
            'titulo' => 'Qualquer',
            'data' => '2026-09-12',
            'hora_inicio' => '09:00',
            'hora_fim' => '09:30',
            'local' => 'Pátio Central',
        ]);
    }

    public function test_aprovar_gastronomia_cria_expositor_com_stand_atribuido(): void
    {
        Notification::fake();

        $feira = Feira::factory()->create();
        $professor = User::factory()->create();
        $stand = Stand::factory()->create(['feira_id' => $feira->id]);
        $inscricao = Inscricao::factory()->create([
            'feira_id' => $feira->id,
            'professor_id' => $professor->id,
            'tipo_atividade' => 'gastronomia',
            'turma' => '9C',
        ]);
        $avaliador = User::factory()->create();

        $this->service->aprovar($inscricao, $avaliador, [
            'stand_id' => $stand->id,
            'produto_nome' => 'Matapa com Camarão',
            'produto_preco' => 150,
        ]);

        $inscricao->refresh();
        $this->assertSame('aprovada', $inscricao->estado);

        $this->assertDatabaseHas('expositores', [
            'inscricao_id' => $inscricao->id,
            'stand_id' => $stand->id,
            'turma' => '9C',
            'categoria' => 'Gastronomia',
            'estado' => 'ativo',
        ]);

        $this->assertDatabaseHas('gastronomia_itens', [
            'feira_id' => $feira->id,
            'nome' => 'Matapa com Camarão',
            'preco' => 150,
        ]);

        Notification::assertSentTo($professor, InscricaoAvaliada::class);
    }

    public function test_aprovar_gastronomia_de_aluno_sem_turma_nao_rebenta(): void
    {
        Notification::fake();

        $feira = Feira::factory()->create();
        $aluno = User::factory()->create();
        $stand = Stand::factory()->create(['feira_id' => $feira->id]);
        $inscricao = Inscricao::factory()->create([
            'feira_id' => $feira->id,
            'professor_id' => $aluno->id,
            'tipo_participante' => 'aluno',
            'tipo_atividade' => 'gastronomia',
            'turma' => null,
        ]);
        $avaliador = User::factory()->create();

        $this->service->aprovar($inscricao, $avaliador, [
            'stand_id' => $stand->id,
            'produto_nome' => 'Bolo de coco',
            'produto_preco' => 80,
        ]);

        $this->assertSame('aprovada', $inscricao->fresh()->estado);
        $this->assertDatabaseHas('expositores', [
            'inscricao_id' => $inscricao->id,
            'stand_id' => $stand->id,
            'turma' => null,
        ]);
    }

    public function test_aprovar_gastronomia_bloqueia_stand_ja_ocupado(): void
    {
        $feira = Feira::factory()->create();
        $stand = Stand::factory()->create(['feira_id' => $feira->id]);
        Expositor::factory()->create(['feira_id' => $feira->id, 'stand_id' => $stand->id]);

        $inscricao = Inscricao::factory()->create([
            'feira_id' => $feira->id,
            'tipo_atividade' => 'gastronomia',
            'turma' => '9C',
        ]);
        $avaliador = User::factory()->create();

        try {
            $this->service->aprovar($inscricao, $avaliador, ['stand_id' => $stand->id]);
            $this->fail('Devia ter lançado RuntimeException por stand já ocupado.');
        } catch (RuntimeException) {
            // esperado
        }

        $this->assertSame('pendente', $inscricao->fresh()->estado);
        $this->assertSame(0, $inscricao->fresh()->expositor()->count());
    }

    public function test_rejeitar_grava_comentario_e_notifica_professor(): void
    {
        Notification::fake();

        $professor = User::factory()->create();
        $inscricao = Inscricao::factory()->create(['professor_id' => $professor->id]);
        $avaliador = User::factory()->create();

        $this->service->rejeitar($inscricao, $avaliador, 'Falta espaço disponível.');

        $inscricao->refresh();
        $this->assertSame('rejeitada', $inscricao->estado);
        $this->assertSame('Falta espaço disponível.', $inscricao->comentario_avaliacao);

        Notification::assertSentTo($professor, InscricaoAvaliada::class);
    }

    public function test_rejeitar_bloqueia_inscricao_ja_avaliada(): void
    {
        $inscricao = Inscricao::factory()->rejeitada()->create();
        $avaliador = User::factory()->create();

        $this->expectException(RuntimeException::class);

        $this->service->rejeitar($inscricao, $avaliador, 'Outro motivo qualquer.');
    }
}
