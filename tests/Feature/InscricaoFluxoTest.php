<?php

namespace Tests\Feature;

use App\Models\Aluno;
use App\Models\Feira;
use App\Models\Inscricao;
use App\Models\Stand;
use App\Notifications\InscricaoAvaliada;
use App\Notifications\NovaInscricaoSubmetida;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CriaUtilizadoresComPapel;
use Tests\TestCase;

/**
 * O fluxo mais sensível do sistema (Fase 8.4): submissão pelo Professor,
 * notificação à Comissão, aprovação/rejeição com notificação de volta,
 * e RC01 (só se edita enquanto pendente).
 */
class InscricaoFluxoTest extends TestCase
{
    use CriaUtilizadoresComPapel;
    use RefreshDatabase;

    public function test_professor_submete_e_comissao_e_notificada(): void
    {
        Notification::fake();

        $feira = Feira::factory()->publicada()->create();
        $professor = $this->criarProfessor();
        $comissao = $this->criarComissao();

        $response = $this->actingAs($professor)->post('/professor/inscricoes', [
            'tipo_participante' => 'professor',
            'turma' => '9C',
            'telefone' => '841234567',
            'email' => 'professor@teste.local',
            'tipo_atividade' => 'danca',
            'numero_participantes' => 15,
        ]);

        $response->assertRedirect('/professor/inscricoes');
        $this->assertDatabaseHas('inscricoes', [
            'feira_id' => $feira->id,
            'professor_id' => $professor->id,
            'estado' => 'pendente',
        ]);

        Notification::assertSentTo($comissao, NovaInscricaoSubmetida::class);
    }

    public function test_aluno_tambem_pode_submeter_e_comissao_e_notificada(): void
    {
        Notification::fake();

        $feira = Feira::factory()->publicada()->create();
        $professor = $this->criarProfessor();
        $aluno = $this->criarAluno();
        Aluno::factory()->create(['professor_id' => $professor->id, 'user_id' => $aluno->id, 'turma' => '9C']);
        $comissao = $this->criarComissao();

        $response = $this->actingAs($aluno)->post('/professor/inscricoes', [
            'tipo_participante' => 'aluno',
            'telefone' => '841234567',
            'email' => 'aluno@teste.local',
            'tipo_atividade' => 'poesia',
            'numero_participantes' => 1,
        ]);

        $response->assertRedirect('/professor/inscricoes');
        $this->assertDatabaseHas('inscricoes', [
            'feira_id' => $feira->id,
            'professor_id' => $aluno->id,
            'turma' => '9C',
            'estado' => 'pendente',
        ]);

        Notification::assertSentTo($comissao, NovaInscricaoSubmetida::class);
    }

    public function test_aluno_sem_registo_proprio_nao_consegue_submeter(): void
    {
        $aluno = $this->criarAluno();

        $response = $this->actingAs($aluno)->post('/professor/inscricoes', [
            'tipo_participante' => 'aluno',
            'telefone' => '841234567',
            'email' => 'aluno@teste.local',
            'tipo_atividade' => 'poesia',
            'numero_participantes' => 1,
        ]);

        $response->assertRedirect('/professor/inscricoes');
        $response->assertSessionHas('erro');
        $this->assertDatabaseCount('inscricoes', 0);
    }

    public function test_professor_inscreve_em_nome_de_alunos_do_proprio_plantel(): void
    {
        Notification::fake();

        Feira::factory()->publicada()->create();
        $professor = $this->criarProfessor();
        $aluno1 = Aluno::factory()->create(['professor_id' => $professor->id, 'turma' => '9C']);
        $aluno2 = Aluno::factory()->create(['professor_id' => $professor->id, 'turma' => '9C']);
        $alunoDeOutroProfessor = Aluno::factory()->create();

        $response = $this->actingAs($professor)->post('/professor/inscricoes', [
            'tipo_participante' => 'aluno',
            'alunos' => [$aluno1->id, $aluno2->id],
            'telefone' => '841234567',
            'email' => 'professor@teste.local',
            'tipo_atividade' => 'poesia',
            'numero_participantes' => 2,
        ]);

        $response->assertRedirect('/professor/inscricoes');
        $inscricao = Inscricao::firstOrFail();
        $this->assertSame('9C', $inscricao->turma);
        $this->assertSame([$aluno1->id, $aluno2->id], $inscricao->alunos->pluck('id')->sort()->values()->all());

        // Não pode escolher um aluno de outro professor.
        $bloqueado = $this->actingAs($professor)->post('/professor/inscricoes', [
            'tipo_participante' => 'aluno',
            'alunos' => [$alunoDeOutroProfessor->id],
            'telefone' => '841234567',
            'email' => 'professor@teste.local',
            'tipo_atividade' => 'poesia',
            'numero_participantes' => 1,
        ]);
        $bloqueado->assertSessionHasErrors('alunos.0');
    }

    public function test_inscricao_de_gastronomia_exige_turma_produto_e_preco(): void
    {
        $feira = Feira::factory()->publicada()->create();
        $professor = $this->criarProfessor();

        $semDados = $this->actingAs($professor)->post('/professor/inscricoes', [
            'tipo_participante' => 'professor',
            'telefone' => '841234567',
            'email' => 'professor@teste.local',
            'tipo_atividade' => 'gastronomia',
            'numero_participantes' => 2,
        ]);
        $semDados->assertSessionHasErrors(['turma', 'produto_nome', 'produto_preco']);
        $this->assertDatabaseCount('inscricoes', 0);

        $comDados = $this->actingAs($professor)->post('/professor/inscricoes', [
            'tipo_participante' => 'professor',
            'turma' => '9C',
            'telefone' => '841234567',
            'email' => 'professor@teste.local',
            'tipo_atividade' => 'gastronomia',
            'produto_nome' => 'Bolo de coco',
            'produto_preco' => 80,
            'numero_participantes' => 2,
        ]);
        $comDados->assertRedirect('/professor/inscricoes');
        $this->assertDatabaseHas('inscricoes', [
            'feira_id' => $feira->id,
            'produto_nome' => 'Bolo de coco',
            'produto_preco' => 80,
        ]);
    }

    public function test_foto_do_produto_e_guardada_e_chega_ao_gastronomia_item_na_aprovacao(): void
    {
        Storage::fake('public');
        Notification::fake();

        $feira = Feira::factory()->publicada()->create();
        $professor = $this->criarProfessor();
        $comissao = $this->criarComissao();
        $stand = Stand::factory()->create(['feira_id' => $feira->id]);

        $this->actingAs($professor)->post('/professor/inscricoes', [
            'tipo_participante' => 'professor',
            'turma' => '9C',
            'telefone' => '841234567',
            'email' => 'professor@teste.local',
            'tipo_atividade' => 'gastronomia',
            'produto_nome' => 'Bolo de coco',
            'produto_preco' => 80,
            'produto_foto' => UploadedFile::fake()->image('bolo.jpg'),
            'numero_participantes' => 2,
        ]);

        $inscricao = Inscricao::where('produto_nome', 'Bolo de coco')->firstOrFail();
        $this->assertNotNull($inscricao->produto_foto_path);
        Storage::disk('public')->assertExists($inscricao->produto_foto_path);

        $this->actingAs($comissao)->post("/painel/inscricoes/{$inscricao->id}/aprovar", [
            'stand_id' => $stand->id,
            'produto_nome' => 'Bolo de coco',
            'produto_preco' => 80,
        ]);

        $this->assertDatabaseHas('gastronomia_itens', [
            'nome' => 'Bolo de coco',
            'foto_path' => $inscricao->produto_foto_path,
        ]);
    }

    public function test_professor_nao_consegue_submeter_sem_feira_ativa(): void
    {
        $professor = $this->criarProfessor();

        $response = $this->actingAs($professor)->post('/professor/inscricoes', [
            'tipo_participante' => 'professor',
            'telefone' => '841234567',
            'email' => 'professor@teste.local',
            'tipo_atividade' => 'danca',
            'numero_participantes' => 15,
        ]);

        $response->assertRedirect('/professor/inscricoes');
        $response->assertSessionHas('erro');
        $this->assertDatabaseCount('inscricoes', 0);
    }

    public function test_comissao_aprova_com_agendamento_e_professor_e_notificado(): void
    {
        Notification::fake();

        $feira = Feira::factory()->publicada()->create();
        $professor = $this->criarProfessor();
        $comissao = $this->criarComissao();
        $inscricao = Inscricao::factory()->create([
            'feira_id' => $feira->id,
            'professor_id' => $professor->id,
        ]);

        $response = $this->actingAs($comissao)->post("/painel/inscricoes/{$inscricao->id}/aprovar", [
            'titulo' => 'Marrabenta: Raízes em Movimento',
            'data' => $feira->data_inicio->format('Y-m-d'),
            'hora_inicio' => '09:30',
            'hora_fim' => '10:15',
            'local' => 'Pátio Central',
        ]);

        $response->assertRedirect('/painel/inscricoes');
        $this->assertSame('aprovada', $inscricao->fresh()->estado);
        $this->assertDatabaseHas('atividades', ['inscricao_id' => $inscricao->id]);

        Notification::assertSentTo($professor, InscricaoAvaliada::class);
    }

    public function test_comissao_aprova_gastronomia_atribuindo_stand(): void
    {
        Notification::fake();

        $feira = Feira::factory()->publicada()->create();
        $professor = $this->criarProfessor();
        $comissao = $this->criarComissao();
        $stand = Stand::factory()->create(['feira_id' => $feira->id]);
        $inscricao = Inscricao::factory()->create([
            'feira_id' => $feira->id,
            'professor_id' => $professor->id,
            'tipo_atividade' => 'gastronomia',
            'turma' => '9C',
        ]);

        $response = $this->actingAs($comissao)->post("/painel/inscricoes/{$inscricao->id}/aprovar", [
            'stand_id' => $stand->id,
            'produto_nome' => 'Matapa com Camarão',
            'produto_preco' => 150,
        ]);

        $response->assertRedirect('/painel/inscricoes');
        $this->assertSame('aprovada', $inscricao->fresh()->estado);
        $this->assertDatabaseHas('expositores', [
            'inscricao_id' => $inscricao->id,
            'stand_id' => $stand->id,
            'turma' => '9C',
        ]);
        $this->assertDatabaseHas('gastronomia_itens', [
            'feira_id' => $feira->id,
            'nome' => 'Matapa com Camarão',
            'preco' => 150,
        ]);

        Notification::assertSentTo($professor, InscricaoAvaliada::class);
    }

    public function test_comissao_rejeita_com_comentario_obrigatorio(): void
    {
        $comissao = $this->criarComissao();
        $inscricao = Inscricao::factory()->create();

        $semComentario = $this->actingAs($comissao)->post("/painel/inscricoes/{$inscricao->id}/rejeitar", []);
        $semComentario->assertSessionHasErrors('comentario_avaliacao');
        $this->assertSame('pendente', $inscricao->fresh()->estado);

        $comComentario = $this->actingAs($comissao)->post("/painel/inscricoes/{$inscricao->id}/rejeitar", [
            'comentario_avaliacao' => 'Falta espaço disponível este ano.',
        ]);
        $comComentario->assertRedirect('/painel/inscricoes');
        $this->assertSame('rejeitada', $inscricao->fresh()->estado);
        $this->assertSame('Falta espaço disponível este ano.', $inscricao->fresh()->comentario_avaliacao);
    }

    public function test_rc01_professor_nao_edita_inscricao_ja_avaliada(): void
    {
        $professor = $this->criarProfessor();
        $inscricao = Inscricao::factory()->aprovada()->create(['professor_id' => $professor->id]);

        $response = $this->actingAs($professor)->get("/professor/inscricoes/{$inscricao->id}/edit");

        $response->assertRedirect('/professor/inscricoes');
        $response->assertSessionHas('erro');
    }

    public function test_professor_nao_acede_a_inscricao_de_outro_professor(): void
    {
        $professor = $this->criarProfessor();
        $inscricaoDeOutro = Inscricao::factory()->create();

        $this->actingAs($professor)
            ->get("/professor/inscricoes/{$inscricaoDeOutro->id}/edit")
            ->assertForbidden();
    }
}
