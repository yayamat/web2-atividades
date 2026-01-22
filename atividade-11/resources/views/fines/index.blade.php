{{-- resources/views/fines/index.blade.php --}}
<h1>Usuários com Multa</h1>

<table border="1" cellpadding="8">
    <tr>
        <th>Nome</th>
        <th>Email</th>
        <th>Débito (R$)</th>
        <th>Ação</th>
    </tr>

@foreach($users as $user)
    <tr>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>R$ {{ number_format($user->debit, 2, ',', '.') }}</td>
        <td>
            <form method="POST" action="{{ route('fines.clear', $user) }}">
                @csrf
                <button>Zerar Multa</button>
            </form>
        </td>
    </tr>
@endforeach
</table>
