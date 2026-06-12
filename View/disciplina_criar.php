<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Disciplina - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h2>Cadastrar Nova Disciplina</h2>
    <a href="?p=disciplinas">Voltar para a Lista</a>

    <hr>

    <form action="?p=disciplina-salvar" method="POST">
        
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <label for="nome">Nome da Disciplina:</label><br>
        <input type="text" name="nome" id="nome" required><br><br>

        <label for="carga">Carga Horária (horas):</label><br>
        <input type="number" name="carga_horaria" id="carga" required><br><br>

        <input type="submit" value="Salvar Disciplina">
    </form>

</body>
</html>