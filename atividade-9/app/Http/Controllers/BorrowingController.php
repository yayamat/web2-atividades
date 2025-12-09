<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrowing;
use App\Models\Book;
use App\Models\User;

class BorrowingController extends Controller
{
    /**
     * Lista os empréstimos do usuário (histórico).
     */
    public function userBorrowings(User $user)
    {
        // pega empréstimos (histórico) com o relacionamento de book
        $borrowings = Borrowing::where('user_id', $user->id)
            ->with('book')
            ->orderByDesc('borrowed_at')
            ->get();

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

        // autorização por regra de negócio
        if (!($auth->isAdmin() || $auth->isBibliotecario() || $auth->id === $targetUserId)) {
            abort(403, 'Acesso negado — você não tem permissão para registrar empréstimo para esse usuário.');
        }

        // NÃO permitir emprestar enquanto existir um empréstimo ativo para este livro
        $jaEmprestado = Borrowing::where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if ($jaEmprestado) {
            return redirect()->route('books.show', $book)
                ->with('error', 'Este livro já está emprestado e não está disponível no momento.');
        }

        // prevenção: evitar duplicata para o mesmo usuário (caso queira reforçar)
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
     * Marca devolução (returned_at).
     * Apenas bibliotecário e admin podem registrar devolução (conforme sua regra).
     */
    public function returnBook(Borrowing $borrowing)
    {
        $auth = Auth::user();

        // autorização: apenas bibliotecario ou admin podem processar devolução
        if (!($auth->isAdmin() || $auth->isBibliotecario())) {
            abort(403, 'Acesso negado — somente bibliotecário ou admin podem registrar devoluções.');
        }

        // se já foi devolvido
        if (!is_null($borrowing->returned_at)) {
            return redirect()->back()->with('error', 'Este empréstimo já foi devolvido.');
        }

        $borrowing->update([
            'returned_at' => now(),
        ]);

        // redireciona para a página do livro (mais útil para reusar a view de livro)
        return redirect()->route('books.show', $borrowing->book_id)
            ->with('success', 'Devolução registrada com sucesso.');
    }
}
