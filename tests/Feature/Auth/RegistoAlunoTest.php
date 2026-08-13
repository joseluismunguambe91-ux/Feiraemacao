<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Registo público exclusivo para Alunos, pedido pós-Etapa 10 — Professor,
 * Comissão e Administrador continuam a ser criados só pelo Administrador
 * (RF03), nunca por este caminho.
 */
class RegistoAlunoTest extends TestCase
{
    use RefreshDatabase;

    public function test_aluno_cria_a_propria_conta_e_entra_automaticamente(): void
    {
        Role::firstOrCreate(['slug' => 'aluno'], ['nome' => 'Aluno']);

        $response = $this->post('/registar', [
            'name' => 'Ana Cumbe',
            'email' => 'ana.cumbe@teste.local',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $response->assertRedirect('/professor/inscricoes');

        $utilizador = User::where('email', 'ana.cumbe@teste.local')->first();
        $this->assertNotNull($utilizador);
        $this->assertTrue($utilizador->hasRole('aluno'));
        $this->assertTrue($utilizador->ativo);
        $this->assertAuthenticatedAs($utilizador);
    }

    public function test_nao_deixa_criar_com_email_ja_existente(): void
    {
        Role::firstOrCreate(['slug' => 'aluno'], ['nome' => 'Aluno']);
        User::factory()->create(['email' => 'ja-existe@teste.local']);

        $response = $this->post('/registar', [
            'name' => 'Outro Nome',
            'email' => 'ja-existe@teste.local',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_exige_confirmacao_de_senha_igual(): void
    {
        Role::firstOrCreate(['slug' => 'aluno'], ['nome' => 'Aluno']);

        $response = $this->post('/registar', [
            'name' => 'Ana Cumbe',
            'email' => 'ana.cumbe@teste.local',
            'password' => 'senha1234',
            'password_confirmation' => 'diferente',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_utilizador_ja_autenticado_nao_acede_ao_registo(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/registar')->assertRedirect();
    }
}
