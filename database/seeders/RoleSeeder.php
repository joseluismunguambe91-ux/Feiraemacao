<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Os 4 papéis fixos do sistema (Visitante não tem conta — Etapa 4).
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['slug' => 'administrador', 'nome' => 'Administrador'],
            ['slug' => 'comissao', 'nome' => 'Comissão Organizadora'],
            ['slug' => 'professor', 'nome' => 'Professor'],
            ['slug' => 'aluno', 'nome' => 'Aluno'],
        ])->each(fn (array $role) => Role::query()->firstOrCreate(['slug' => $role['slug']], $role));
    }
}
