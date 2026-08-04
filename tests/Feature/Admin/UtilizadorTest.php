<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaUtilizadoresComPapel;
use Tests\TestCase;

/**
 * RF03 (Etapa 1): só o Administrador cria/edita contas e atribui papéis.
 */
class UtilizadorTest extends TestCase
{
    use CriaUtilizadoresComPapel;
    use RefreshDatabase;

    public function test_administrador_cria_utilizador_com_papel(): void
    {
        $admin = $this->criarAdministrador();
        $papelComissao = Role::firstOrCreate(['slug' => 'comissao'], ['nome' => 'Comissão Organizadora']);

        $response = $this->actingAs($admin)->post('/admin/utilizadores', [
            'name' => 'Membro da Comissão',
            'email' => 'comissao@teste.local',
            'password' => 'senha1234',
            'ativo' => 1,
            'roles' => [$papelComissao->id],
        ]);

        $response->assertRedirect('/admin/utilizadores');

        $utilizador = User::where('email', 'comissao@teste.local')->first();
        $this->assertNotNull($utilizador);
        $this->assertTrue($utilizador->hasRole('comissao'));
    }

    public function test_novo_utilizador_consegue_entrar_com_o_papel_atribuido(): void
    {
        $admin = $this->criarAdministrador();
        $papel = Role::firstOrCreate(['slug' => 'comissao'], ['nome' => 'Comissão Organizadora']);

        $this->actingAs($admin)->post('/admin/utilizadores', [
            'name' => 'Membro da Comissão',
            'email' => 'comissao@teste.local',
            'password' => 'senha1234',
            'ativo' => 1,
            'roles' => [$papel->id],
        ]);

        $this->post('/logout');

        $this->post('/login', [
            'email' => 'comissao@teste.local',
            'password' => 'senha1234',
        ])->assertRedirect('/painel');
    }

    public function test_comissao_nao_pode_gerir_utilizadores(): void
    {
        $comissao = $this->criarComissao();

        $this->actingAs($comissao)->get('/admin/utilizadores')->assertForbidden();
    }

    public function test_administrador_nao_se_pode_eliminar_a_si_proprio(): void
    {
        $admin = $this->criarAdministrador();

        $response = $this->actingAs($admin)->delete("/admin/utilizadores/{$admin->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'deleted_at' => null]);
    }

    public function test_email_duplicado_e_rejeitado(): void
    {
        $admin = $this->criarAdministrador();
        $existente = User::factory()->create(['email' => 'ja-existe@teste.local']);

        $response = $this->actingAs($admin)->post('/admin/utilizadores', [
            'name' => 'Outro Nome',
            'email' => 'ja-existe@teste.local',
            'password' => 'senha1234',
            'roles' => [Role::firstOrCreate(['slug' => 'professor'], ['nome' => 'Professor'])->id],
        ]);

        $response->assertSessionHasErrors('email');
    }
}
