<?php

namespace Tests\Concerns;

use App\Models\Role;
use App\Models\User;

/**
 * Evita repetir "criar utilizador + criar papel + associar" em cada teste
 * que precise de um Administrador/Comissão/Professor autenticado.
 */
trait CriaUtilizadoresComPapel
{
    protected function criarAdministrador(array $atributos = []): User
    {
        return $this->criarComPapel('administrador', $atributos);
    }

    protected function criarComissao(array $atributos = []): User
    {
        return $this->criarComPapel('comissao', $atributos);
    }

    protected function criarProfessor(array $atributos = []): User
    {
        return $this->criarComPapel('professor', $atributos);
    }

    protected function criarAluno(array $atributos = []): User
    {
        return $this->criarComPapel('aluno', $atributos);
    }

    private function criarComPapel(string $slug, array $atributos): User
    {
        $user = User::factory()->create($atributos);

        $papel = Role::firstOrCreate(['slug' => $slug], ['nome' => ucfirst($slug)]);
        $user->roles()->attach($papel->id);

        return $user->refresh();
    }
}
