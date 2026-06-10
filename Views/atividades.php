<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Atividades - Sobrevivência do Semestre</title>
</head>
<body>

    <header>
        <h2>Gerenciar Atividades / Tarefas</h2>
        
        <a href="?p=feed">Voltar para o Feed</a> | 
        
        <a href="?p=atividade-criar" style="font-weight: bold; color: green;">+ Nova Atividades</a>
    </header>

    <hr>

    <main>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: left;">
            
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título da Atividade</th>
                    <th>Descrição / Detalhes</th>
                    <th>Data de Entrega</th>
                    <th>Disciplina Associada</th>
                    <th>Ações (Editar / Excluir)</th>
                </tr>
            </thead>
            
            <tbody>
                <?php
                // IMPACTO DE INTEGRAÇÃO MVC:
                // A variável $listaAtividades foi criada e preenchida lá no AtividadeController::index().
                // O comando 'empty' confere se o array veio vazio do banco (se o aluno não tem tarefas).
                if (empty($listaAtividades)) {
                    // Se estiver limpo, cria uma linha cobrindo todas as 6 colunas (colspan="6") com um aviso.
                    echo "<tr><td colspan='6'>Nenhuma atividade agendada por enquanto. Aproveite o descanso!</td></tr>";
                } else {
                    // ESTRUTURA DE REPETIÇÃO DA AULA (FOREACH):
                    // Se houver registros, o PHP inicia um loop. Ele abre a gaveta de $listaAtividades,
                    // retira uma linha por vez e joga temporariamente na variável unitária $atividade.
                    foreach ($listaAtividades as $atividade) {
                ?>
                        <tr>
                            <td><?= $atividade->id; ?></td>
                            <td><strong><?= $atividade->titulo; ?></strong></td>
                            <td><?= $atividade->descricao; ?></td>
                            
                            <td><?= $atividade->data_entrega; ?></td>
                            
                            <td><?= $atividade->disciplina_nome; ?></td>
                            
                            <td>
                                <a href="?p=atividade-editar&id=<?= $atividade->id; ?>">Editar</a> | 

                                <a href="?p=atividade-excluir&id=<?= $atividade->id; ?>" 
                                   style="color: red;"
                                   onclick="return confirm('Tem certeza que deseja apagar esta atividade permanentemente?');">
                                   Excluir
                                </a>
                            </td>
                        </tr>
                <?php
                    } // Fecha o bloco do laço foreach
                } // Fecha o bloco do filtro if/else
                ?>
            </tbody>
        </table>
    </main>

</body>
</html>