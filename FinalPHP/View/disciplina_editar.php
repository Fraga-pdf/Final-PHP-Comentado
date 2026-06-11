<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Disciplina - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h2>Editar Disciplina</h2>
    
    <a href="?p=disciplinas">Cancelar e voltar para a lista</a>

    <hr>

    <form action="?p=disciplina-atualizar" method="POST">
        
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <input type="hidden" name="id" value="<?php echo $disciplinaAtual->id; ?>">

        <label for="nome">Nome da Disciplina:</label><br>
        <input type="text" name="nome" id="nome" value="<?php echo $disciplinaAtual->nome; ?>" required><br><br>

        <label for="carga">Carga Horária (horas):</label><br>
        <input type="number" name="carga_horaria" id="carga" value="<?php echo $disciplinaAtual->carga_horaria; ?>" required><br><br>

        <input type="submit" value="Salvar Alterações">
    </form>

</body>
</html>