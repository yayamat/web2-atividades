@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Detalhes do Usuário</h1>

    <div class="card">
        <div class="card-header">Histórico de Empréstimos</div>
        <div class="card-body">
            @if($user->books->isEmpty())
                <p>Este usuário não possui empréstimos registrados.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Livro</th>
                            <th>Data de Empréstimo</th>
                            <th>Data de Devolução</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->books as $book)
                            <tr>
                                <td>
                                    <a href="{{ route('books.show', $book->id) }}">
                                        {{ $book->title }}
                                    </a>
                                </td>

                                <td>{{ $book->pivot->borrowed_at }}</td>
                                <td>{{ $book->pivot->returned_at ?? 'Em Aberto' }}</td>
                                <td>
                                    @php
                                        // tenta obter um identificador do "empréstimo" a partir do pivot
                                        $borrowingId = $book->pivot->id 
                                                     ?? $book->pivot->borrowing_id 
                                                     ?? $book->pivot->borrow_id 
                                                     ?? null;
                                    @endphp

                                    @if(is_null($book->pivot->returned_at) && !is_null($borrowingId))
                                        <form action="{{ route('borrowings.return', ['borrowing' => $borrowingId]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-warning btn-sm">Devolver</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            {{ $user->name }}
        </div>
        <div class="card-body">
            <p><strong>Email:</strong> {{ $user->email }}</p>
        </div>
    </div>

    <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection
