<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Atividade - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h2>Cadastrar Nova Atividade</h2>
    
    <a href="?p=atividades">Voltar para a Lista de Atividades</a>

    <hr>

    <form action="?p=atividade-salvar" method="POST">
        
       <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

        <label for="titulo">Título da Atividade:</label><br>
        <input type="text" name="titulo" id="titulo" required><br><br>

        <label for="descricao">Descrição / Detalhes:</label><br>
        <textarea name="descricao" id="descricao" rows="4" cols="40"></textarea><br><br>

        <label for="data">Data de Entrega:</label><br>
        <input type="date" name="data_entrega" id="data" required><br><br>

        <label for="disciplina">Disciplina Associada:</label><br>
        <select name="id_disciplina" id="disciplina" required>
            <option value="">Selecione uma matéria...</option>
            <?php
            // IMPACTO DE RELACIONAMENTO DE TABELAS (CRÍTICO):
            // O controlador enviou a variável $listaDisciplinas para cá.
            // O foreach percorre todas as matérias que o aluno já cadastrou no sistema.
            if (!empty($listaDisciplinas)) {
                foreach ($listaDisciplinas as $disciplina) {
                    // O 'value' guarda o número do ID (que vai pro banco de dados), 
                    // mas o texto que aparece na tela para o aluno clicar é o nome da matéria.
                    echo "<option value='{$disciplina->id}'>{$disciplina->nome}</option>";
                }
            }
            ?>
        </select><br><br>

        <input type="submit" value="Salvar Atividade">
    </form>

</body>
</html>