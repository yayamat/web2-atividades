<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
        $borrowings = Borrowing::where('user_id', $user->id)
            ->with('book')
            ->orderByDesc('borrowed_at')
            ->get();

        return view('users.borrowings', compact('user', 'borrowings'));
    }

    /**
     * Registra um empréstimo.
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $auth = Auth::user();
        $targetUserId = (int) $request->input('user_id');

        // autorização
        if (!($auth->isAdmin() || $auth->isBibliotecario() || $auth->id === $targetUserId)) {
            abort(403, 'Acesso negado.');
        }

        $targetUser = User::findOrFail($targetUserId);

        // ---------- BLOQUEIO POR DÉBITO ----------
        if ($targetUser->debit > 0) {
            return redirect()->route('books.show', $book)
                ->with('error', 'Usuário possui multas pendentes e não pode realizar novos empréstimos.');
        }

        // livro já emprestado?
        $jaEmprestado = Borrowing::where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if ($jaEmprestado) {
            return redirect()->route('books.show', $book)
                ->with('error', 'Este livro já está emprestado.');
        }

        // duplicata
        $existing = Borrowing::where('book_id', $book->id)
            ->where('user_id', $targetUserId)
            ->whereNull('returned_at')
            ->first();

        if ($existing) {
            return redirect()->route('books.show', $book)
                ->with('error', 'Este usuário já possui este livro emprestado.');
        }

        $MAX_ACTIVE_BORROWINGS = 5;

        try {
            DB::beginTransaction();

            $activeCount = Borrowing::where('user_id', $targetUserId)
                ->whereNull('returned_at')
                ->lockForUpdate()
                ->count();

            if ($activeCount >= $MAX_ACTIVE_BORROWINGS) {
                DB::rollBack();
                return redirect()->route('books.show', $book)
                    ->with('error', "Limite de {$MAX_ACTIVE_BORROWINGS} empréstimos ativos atingido.");
            }

            $bookAlreadyBorrowed = Borrowing::where('book_id', $book->id)
                ->whereNull('returned_at')
                ->exists();

            if ($bookAlreadyBorrowed) {
                DB::rollBack();
                return redirect()->route('books.show', $book)
                    ->with('error', 'Este livro já foi emprestado.');
            }

            Borrowing::create([
                'user_id'     => $targetUserId,
                'book_id'     => $book->id,
                'borrowed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('books.show', $book)
                ->with('success', 'Empréstimo registrado com sucesso.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Erro ao registrar empréstimo: '.$e->getMessage());

            return redirect()->route('books.show', $book)
                ->with('error', 'Erro ao registrar o empréstimo.');
        }
    }

    /**
     * Registra devolução + calcula multa.
     */
    public function returnBook(Borrowing $borrowing)
{
    $auth = Auth::user();

    if (!($auth->isAdmin() || $auth->isBibliotecario())) {
        abort(403, 'Acesso negado.');
    }

    if ($borrowing->returned_at) {
        return redirect()->back()
            ->with('error', 'Este empréstimo já foi devolvido.');
    }

    DB::transaction(function () use ($borrowing) {

        $PRAZO_DIAS = 15;
        $MULTA_POR_DIA = 0.50;

        $borrowedAt = $borrowing->borrowed_at;
        $returnedAt = now();

        $diasUsados = $borrowedAt->diffInDays($returnedAt);
        $diasAtraso = max(0, $diasUsados - $PRAZO_DIAS);

        $multa = $diasAtraso * $MULTA_POR_DIA;

        // registra devolução
        $borrowing->update([
            'returned_at' => $returnedAt,
        ]);

        // soma multa no usuário
        if ($multa > 0) {
            $user = $borrowing->user;
            $user->debit += $multa;
            $user->save();
        }
    });

    return redirect()->route('books.show', $borrowing->book_id)
        ->with('success', 'Devolução registrada com sucesso.');
}

}
