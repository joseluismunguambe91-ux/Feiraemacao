<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaUtilizadoresComPapel;
use Tests\TestCase;

/**
 * Etapa 7, item 1: mensagens genéricas, conta inativa bloqueada,
 * redirecionamento por papel.
 */
class LoginTest extends TestCase
{
    use CriaUtilizadoresComPapel;
    use RefreshDatabase;

    public function test_administrador_autentica_e_e_redirecionado_para_painel(): void
    {
        $admin = $this->criarAdministrador(['password' => bcrypt('senha1234')]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'senha1234',
        ]);

        $response->assertRedirect('/painel');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_professor_e_redirecionado_para_as_proprias_inscricoes(): void
    {
        $professor = $this->criarProfessor(['password' => bcrypt('senha1234')]);

        $response = $this->post('/login', [
            'email' => $professor->email,
            'password' => 'senha1234',
        ]);

        $response->assertRedirect('/professor/inscricoes');
    }

    public function test_aluno_e_redirecionado_para_as_proprias_inscricoes(): void
    {
        $aluno = $this->criarAluno(['password' => bcrypt('senha1234')]);

        $response = $this->post('/login', [
            'email' => $aluno->email,
            'password' => 'senha1234',
        ]);

        $response->assertRedirect('/professor/inscricoes');
    }

    public function test_credenciais_invalidas_dao_mensagem_generica(): void
    {
        $user = User::factory()->create(['password' => bcrypt('senha1234')]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'errada',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_conta_inativa_nao_consegue_entrar(): void
    {
        $user = User::factory()->inativo()->create(['password' => bcrypt('senha1234')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'senha1234',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_termina_a_sessao(): void
    {
        $admin = $this->criarAdministrador();

        $this->actingAs($admin)->post('/logout');

        $this->assertGuest();
    }

    public function test_paginas_protegidas_redirecionam_visitante_para_login(): void
    {
        $response = $this->get('/painel');

        $response->assertRedirect('/login');
    }
}
