<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaUtilizadoresComPapel;
use Tests\TestCase;

/**
 * Etapa 4 (matriz de permissões) — cada área só deve responder aos papéis
 * a que pertence, e bloquear (403) todos os outros.
 */
class AcessoPorPapelTest extends TestCase
{
    use CriaUtilizadoresComPapel;
    use RefreshDatabase;

    public function test_admin_exclusivo_bloqueia_comissao_e_professor(): void
    {
        $this->actingAs($this->criarComissao())->get('/admin/feiras')->assertForbidden();
        $this->actingAs($this->criarProfessor())->get('/admin/feiras')->assertForbidden();
        $this->actingAs($this->criarAdministrador())->get('/admin/feiras')->assertOk();
    }

    public function test_painel_partilhado_aceita_administrador_e_comissao_mas_nao_professor(): void
    {
        $this->actingAs($this->criarAdministrador())->get('/painel')->assertOk();
        $this->actingAs($this->criarComissao())->get('/painel')->assertOk();
        $this->actingAs($this->criarProfessor())->get('/painel')->assertForbidden();
    }

    public function test_area_de_inscricoes_aceita_professor_e_aluno_mas_bloqueia_administrador_e_comissao(): void
    {
        $this->actingAs($this->criarProfessor())->get('/professor/inscricoes')->assertOk();
        $this->actingAs($this->criarAluno())->get('/professor/inscricoes')->assertOk();
        $this->actingAs($this->criarAdministrador())->get('/professor/inscricoes')->assertForbidden();
        $this->actingAs($this->criarComissao())->get('/professor/inscricoes')->assertForbidden();
    }

    public function test_auditoria_e_exclusiva_do_administrador(): void
    {
        $this->actingAs($this->criarComissao())->get('/admin/auditoria')->assertForbidden();
        $this->actingAs($this->criarAdministrador())->get('/admin/auditoria')->assertOk();
    }
}
