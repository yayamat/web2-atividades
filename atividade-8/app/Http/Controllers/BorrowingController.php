<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrowing;
use App\Models\Book;
use App\Models\User;

class BorrowingController extends Controller
{
    public function userBorrowings(User $user)
    {
        // lista livros emprestados ao usuário (pivot)
        $borrowings = $user->books()->withPivot('id', 'borrowed_at', 'returned_at')->get();

        return view('users.borrowings', compact('user', 'borrowings'));
    }

    /**
     * Registra um empréstimo para um livro e usuário.
     * Permissões:
     *  - admin e bibliotecario podem emprestar para qualquer usuário
     *  - cliente (usuário comum) só pode emprestar para si mesmo
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $auth = Auth::user();
        $targetUserId = (int) $request->input('user_id');

        // Autorização por regra de negócio (não depender apenas de middleware de rota)
        if (!($auth->isAdmin() || $auth->isBibliotecario() || $auth->id === $targetUserId)) {
            abort(403, 'Acesso negado — você não tem permissão para registrar empréstimo para esse usuário.');
        }

        // prevenção simples: evitar duplicata (mesmo book emprestado e não devolvido para esse user)
        $existing = Borrowing::where('book_id', $book->id)
            ->where('user_id', $targetUserId)
            ->whereNull('returned_at')
            ->first();

        if ($existing) {
            return redirect()->route('books.show', $book)
                ->with('error', 'Este usuário já tem esse livro emprestado e não o devolveu.');
        }

        Borrowing::create([
            'user_id' => $targetUserId,
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        return redirect()->route('books.show', $book)->with('success', 'Empréstimo registrado com sucesso.');
    }

    /**
     * Marca devolução (returned_at) e redireciona para histórico do usuário.
     * Apenas bibliotecário e admin podem registrar devolução (ou você pode ajustar conforme regra).
     */
    public function returnBook(Borrowing $borrowing)
    {
        $auth = Auth::user();

        // autorização: apenas bibliotecario ou admin podem processar devolução
        if (!($auth->isAdmin() || $auth->isBibliotecario())) {
            abort(403, 'Acesso negado — somente bibliotecário ou admin podem registrar devoluções.');
        }

        $borrowing->update([
            'returned_at' => now(),
        ]);

        // redireciona para histórico do usuário dono do empréstimo
        return redirect()->route('users.borrowings', $borrowing->user_id)
            ->with('success', 'Devolução registrada com sucesso.');
    }
}
