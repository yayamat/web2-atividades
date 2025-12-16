@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Lista de Usuários</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Papel</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>
                        {{-- Botão visualizar só aparece se o usuário tiver permissão --}}
                        @can('view', $user)
                            <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Visualizar
                            </a>
                        @endcan

                        {{-- Botão editar só aparece se o usuário tiver permissão --}}
                        @can('update', $user)
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        @endcan

                        {{-- Botão deletar só aparece se o usuário tiver permissão --}}
                        @can('delete', $user)
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Tem certeza que deseja deletar este usuário?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Deletar
                                </button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>
@endsection
