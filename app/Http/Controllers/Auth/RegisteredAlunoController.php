<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistoAlunoRequest;
use App\Models\Role;
use App\Models\User;
use App\Support\RedirecionadorPorPapel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Registo público exclusivo para Alunos — pedido pós-Etapa 10 para não
 * depender do Administrador criar cada conta uma a uma. Professor, Comissão
 * e Administrador continuam só a ser criados em Utilizadores (RF03).
 */
class RegisteredAlunoController extends Controller
{
    public function create(): View
    {
        return view('auth.registar');
    }

    public function store(RegistoAlunoRequest $request): RedirectResponse
    {
        $utilizador = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'password' => Hash::make($request->string('password')),
            'ativo' => true,
        ]);

        $papelAluno = Role::where('slug', 'aluno')->firstOrFail();
        $utilizador->roles()->attach($papelAluno->id);

        Auth::login($utilizador);
        $request->session()->regenerate();

        return redirect(RedirecionadorPorPapel::destino($utilizador))
            ->with('sucesso', 'Conta criada com sucesso! Já podes submeter a tua inscrição.');
    }
}
