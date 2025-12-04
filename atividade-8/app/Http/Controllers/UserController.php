<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(User::class, 'user');
    }

    public function index()
    {
        // Paginação: 15 por página
        $users = User::orderBy('name')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // autorização via policy (authorizeResource já faz, mas double check)
        $this->authorize('update', $user);

        // validação com regra de email único ignorando o próprio usuário
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            // role é opcional na validação; só será aplicado se o auth for admin
            'role' => 'nullable|string|in:admin,bibliotecario,cliente',
        ]);

        // Proteção: só admin pode alterar role
        if (! auth()->user()->isAdmin()) {
            unset($data['role']); // remove role se não for admin
        }

        $user->fill($data);
        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        // autorização via policy
        $this->authorize('delete', $user);

        // Proteção: não permitir deletar a própria conta logada
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Você não pode deletar sua própria conta.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuário removido com sucesso.');
    }
}
