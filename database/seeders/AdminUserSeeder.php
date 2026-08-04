<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Conta de arranque do Administrador — sem ela, ninguém consegue entrar no
 * sistema para criar as restantes contas (RF03). Senha de desenvolvimento,
 * a trocar no primeiro acesso em qualquer ambiente que não seja local.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@feiraemacao.local'],
            [
                'name' => 'Administrador da Feira',
                'password' => Hash::make('MudarNo1Acesso!'),
                'ativo' => true,
                'email_verified_at' => now(),
            ]
        );

        $papel = Role::query()->where('slug', 'administrador')->first();

        if ($papel && ! $admin->roles->contains($papel->id)) {
            $admin->roles()->attach($papel->id);
        }
    }
}
