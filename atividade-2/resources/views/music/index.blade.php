<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Músicas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        a, button {
            text-decoration: none;
            color: #0066cc;
            margin-right: 8px;
        }
        button {
            background-color: transparent;
            border: none;
            color: red;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1>Lista de Músicas</h1>

    <form action="{{ route('music.create') }}" method="get" style="display:inline;">
        <button type="submit" style="color: #0066cc; background: none; border: 1px solid #0066cc; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
             Nova Música
        </button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Artista</th>
            <th>Álbum</th>
            <th>Duração</th>
            <th>Gênero</th>
            <th>Ações</th>
        </tr>

        @forelse($musics as $music)
        <tr>
            <td>{{ $music->id }}</td>
            <td>{{ $music->title }}</td>
            <td>{{ $music->artist }}</td>
            <td>{{ $music->album }}</td>
            <td>{{ $music->duration }}</td>
            <td>{{ $music->genre }}</td>

            <td>
                <a href="{{ route('music.show', $music) }}">Visualizar</a>
                <a href="{{ route('music.edit', $music) }}">Editar</a>

                <form action="{{ route('music.destroy', $music) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button onclick="return confirm('Deseja realmente excluir esta música?')">Excluir</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;">Nenhuma música encontrada.</td>
        </tr>
        @endforelse
    </table>
</body>
</html>
