<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Disciplinas - Sobrevivência do Semestre</title>
</head>
<body>

    <header>
        <h2>Gerenciar Disciplinas</h2>
        
        <a href="?p=feed">Voltar para o Feed</a> | 
        
        <a href="?p=disciplina-criar" style="font-weight: bold; color: green;">+ Nova Disciplina</a>
    </header>

    <hr>

    <main>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: left;">
            
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome da Disciplina</th>
                    <th>Carga Horária</th>
                    <th>Ações (Editar / Excluir)</th>
                </tr>
            </thead>
            
            <tbody>
                <?php
                // IMPACTO DE REGRA DE VISUALIZAÇÃO:
                // A variável $listaDisciplinas NÃO foi criada neste arquivo. Ela foi criada
                // lá no DisciplinaController::index() e "injetada" aqui quando fizemos o require.
                // O empty() verifica se a lista está vazia (ou seja, se o aluno ainda não cadastrou nada).
                if (empty($listaDisciplinas)) {
                    // Se não tiver matérias, desenha uma linha única (colspan="4") avisando o aluno.
                    echo "<tr><td colspan='4'>Nenhuma disciplina cadastrada ainda. Comece adicionando uma!</td></tr>";
                } else {
                    // ESTRUTURA DE REPETIÇÃO OBRIGATÓRIA (FOREACH):
                    // Se a lista tiver itens, o foreach pega o array $listaDisciplinas e 
                    // extrai uma matéria por vez, chamando-a temporariamente de $disciplina.
                    foreach ($listaDisciplinas as $disciplina) {
                ?>
                        <tr>
                            <td><?php echo $disciplina->id; ?></td>
                            <td><?php echo $disciplina->nome; ?></td>
                            <td><?php echo $disciplina->carga_horaria; ?>h</td>
                            
                            <td>
                                <a href="?p=disciplina-editar&id=<?php echo $disciplina->id; ?>">Editar</a> | 

                                <a href="?p=disciplina-excluir&id=<?php echo $disciplina->id; ?>" 
                                   style="color: red;"
                                   onclick="return confirm('Tem certeza que deseja excluir esta disciplina?');">
                                   Excluir
                                </a>
                            </td>
                        </tr>
                <?php
                    } // Fim do foreach
                } // Fim do if/else
                ?>
            </tbody>
        </table>
    </main>

</body>
</html>