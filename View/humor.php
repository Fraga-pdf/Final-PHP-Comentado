<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Diário - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <main>
        <h2>🧠 Histórico do Diário de Estresse</h2>
        
        <nav style="margin-bottom: 20px;">
            <a href="?p=feed">⬅️ Voltar para o Painel</a> | 
            <a href="?p=humor-criar" style="color: #1da1f2;">➕ Novo Registro</a>
        </nav>

        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Nível de Estresse</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // IMPACTO DE LÓGICA: Verifica se o array de humor (injetado pelo HumorController) está vazio.
                if (empty($listaHumor)): 
                ?>
                    <tr><td colspan="2">Nenhum registro encontrado. Comece a monitorar seu estresse!</td></tr>
                <?php else: ?>
                    <?php 
                    // IMPACTO DE REPETIÇÃO: Loop (foreach) que varre todos os registros trazidos do banco de dados.
                    foreach ($listaHumor as $h): 
                    ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($h->data_registro)); ?></td>
                            <td><strong><?php echo $h->nivel_estresse; ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

</body>
</html>