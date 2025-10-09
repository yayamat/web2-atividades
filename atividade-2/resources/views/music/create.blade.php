<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Música</title>
</head>
<body>

    <h1>Cadastrar Música</h1>

    <form action="{{ route('music.store') }}" method="POST">
        @csrf

        <div>
            <label for="title">Título:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div>
            <label for="artist">Artista:</label>
            <input type="text" id="artist" name="artist" required>
        </div>

        <div>
            <label for="album">Álbum:</label>
            <input type="text" id="album" name="album">
        </div>

        <div>
            <label for="duration">Duração:</label>
            <input type="time" id="duration" name="duration" required>
        </div>

        <div>
            <label for="year">Ano:</label>
            <input type="number" id="year" name="year" min="1900" max="2099" required>
        </div>

        <div>
            <label for="genre">Gênero:</label>
            <select name="genre" id="genre" required>
                <option value="Pop">Pop</option>
                <option value="Rock">Rock</option>
                <option value="Sertanejo">Sertanejo</option>
                <option value="MPB">MPB</option>
                <option value="Forro">Forro</option>
                <option value="Brega">Brega</option>
                <option value="Outro">Outro</option>
            </select>
        </div>

        

        <button type="submit">Salvar</button>
    </form>

    <br>
    <form action="{{ route('music.index') }}" method="get" style="display:inline;">
        <button type="submit">Voltar para a lista</button>
    </form>

</body>
</html>
