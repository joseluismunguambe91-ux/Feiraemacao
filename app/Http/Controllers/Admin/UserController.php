<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $utilizadores = User::with('roles')->orderBy('name')->paginate(15);

        return view('admin.utilizadores.index', compact('utilizadores'));
    }

    public function create(): View
    {
        return view('admin.utilizadores.form', [
            'utilizador' => new User(),
            'papeis' => Role::orderBy('nome')->get(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $dados = $request->safe()->except(['roles']);
        $dados['password'] = Hash::make($request->string('password'));
        $dados['ativo'] = $request->boolean('ativo', true);

        $utilizador = User::create($dados);
        $utilizador->roles()->sync($request->input('roles'));

        return redirect()->route('admin.utilizadores.index')->with('sucesso', 'Utilizador criado.');
    }

    public function edit(User $utilizador): View
    {
        $utilizador->load('roles');

        return view('admin.utilizadores.form', [
            'utilizador' => $utilizador,
            'papeis' => Role::orderBy('nome')->get(),
        ]);
    }

    public function update(UserRequest $request, User $utilizador): RedirectResponse
    {
        $dados = $request->safe()->except(['roles', 'password']);
        $dados['ativo'] = $request->boolean('ativo', true);

        if ($request->filled('password')) {
            $dados['password'] = Hash::make($request->string('password'));
        }

        $utilizador->update($dados);
        $utilizador->roles()->sync($request->input('roles'));

        return redirect()->route('admin.utilizadores.index')->with('sucesso', 'Utilizador atualizado.');
    }

    public function destroy(User $utilizador): RedirectResponse
    {
        abort_if($utilizador->id === auth()->id(), 403, 'Não podes eliminar a tua própria conta.');

        $utilizador->delete();

        return redirect()->route('admin.utilizadores.index')->with('sucesso', 'Utilizador eliminado.');
    }
}
