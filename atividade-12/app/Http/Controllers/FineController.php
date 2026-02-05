<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FineController extends Controller
{
    public function index()
    {
        $auth = Auth::user();

        if (!($auth->isAdmin() || $auth->isBibliotecario())) {
            abort(403, 'Acesso negado.');
        }

        $users = User::where('debit', '>', 0)
            ->orderByDesc('debit')
            ->get();

        return view('fines.index', compact('users'));
    }

    public function clear(User $user)
    {
        $auth = Auth::user();

        if (!($auth->isAdmin() || $auth->isBibliotecario())) {
            abort(403, 'Acesso negado.');
        }

        $user->update(['debit' => 0]);

        return back()->with('success', 'Multa quitada com sucesso.');
    }
}
