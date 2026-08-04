<?php

namespace Tests\Feature\Professor;

use App\Models\Aluno;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaUtilizadoresComPapel;
use Tests\TestCase;

/**
 * RF04 (Etapa 1): o professor gere o seu próprio plantel de alunos —
 * decisão tomada após a Etapa 10 para ligar as inscrições de Aluno a um
 * registo real em vez de texto livre (ver docs/10-documentacao.md).
 */
class AlunoTest extends TestCase
{
    use CriaUtilizadoresComPapel;
    use RefreshDatabase;

    public function test_professor_regista_um_aluno(): void
    {
        $professor = $this->criarProfessor();

        $response = $this->actingAs($professor)->post('/professor/alunos', [
            'nome' => 'Ana Cumbe',
            'turma' => '9C',
        ]);

        $response->assertRedirect('/professor/alunos');
        $this->assertDatabaseHas('alunos', [
            'nome' => 'Ana Cumbe',
            'turma' => '9C',
            'professor_id' => $professor->id,
        ]);
    }

    public function test_aluno_nao_pode_gerir_o_proprio_plantel(): void
    {
        $aluno = $this->criarAluno();

        $this->actingAs($aluno)->get('/professor/alunos')->assertForbidden();
    }

    public function test_professor_nao_edita_aluno_de_outro_professor(): void
    {
        $professor = $this->criarProfessor();
        $alunoDeOutro = Aluno::factory()->create();

        $this->actingAs($professor)
            ->get("/professor/alunos/{$alunoDeOutro->id}/edit")
            ->assertForbidden();
    }

    public function test_nao_pode_ligar_a_mesma_conta_a_dois_alunos(): void
    {
        $professor = $this->criarProfessor();
        $contaAluno = $this->criarAluno();
        Aluno::factory()->create(['professor_id' => $professor->id, 'user_id' => $contaAluno->id]);

        $response = $this->actingAs($professor)->post('/professor/alunos', [
            'nome' => 'Outro Nome',
            'turma' => '9C',
            'user_id' => $contaAluno->id,
        ]);

        $response->assertSessionHasErrors('user_id');
    }
}
