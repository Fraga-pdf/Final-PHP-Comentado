<?php
// IMPACTO DE ARQUITETURA: Como o roteador da prova chama a tela diretamente, 
// forçamos a busca dos dados no banco aqui no topo para o painel não nascer "cego".
require_once __DIR__ . "/../Model/Atividade.php";
require_once __DIR__ . "/../Model/Humor.php";

$id_logado = $_SESSION['id_usuario'] ?? 0;

// Busca os dados reais do aluno logado
$listaAtividades = Atividade::listarTodas($id_logado);
$listaHumor      = Humor::listarTodos($id_logado);
$humorAtual      = !empty($listaHumor) ? $listaHumor[0] : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Painel - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <main>
        <header style="box-shadow: none; padding: 0; margin-bottom: 20px;">
            <h2>Olá, <?php echo $_SESSION['nome'] ?? 'Aluno'; ?>! Bem-vindo ao seu semestre.</h2>
            <p style="color: #657786;">Seu Nickname: <?php echo $_SESSION['usuario'] ?? ''; ?> | Seu ID: <?php echo $id_logado; ?></p>
        </header>

        <nav style="margin-bottom: 30px; background-color: #f5f8fa; padding: 15px; border-radius: 15px;">
            <strong>Acessos Rápidos:</strong><br><br>
            <a href="?p=feed">🔄 Atualizar Feed</a> | 
            <a href="?p=disciplinas">📚 Gerenciar Disciplinas</a> | 
            <a href="?p=atividades">📝 Minhas Atividades</a> | 
            <a href="?p=humor">🧠 Meu Diário de Estresse</a> | 
            <a href="?p=logout" style="color: #e0245e;">❌ Sair do Sistema</a>
        </nav>

        <section style="margin-bottom: 30px;">
            <h3>🧠 Como você está agora?</h3>
            <?php if (!empty($humorAtual)): ?>
                <p>
                    Status atual: <strong><?php echo $humorAtual->nivel_estresse; ?></strong> 
                    <em>(Registrado em <?php echo date('d/m/Y', strtotime($humorAtual->data_registro)); ?>)</em>
                </p>
            <?php else: ?>
                <p>Você ainda não registrou seu nível de estresse. <a href="?p=humor-criar" style="color: #1da1f2;">Registre agora!</a></p>
            <?php endif; ?>
        </section>

        <section>
            <h3>📝 Suas Próximas Atividades</h3>
            <table>
                <thead>
                    <tr>
                        <th>Disciplina</th>
                        <th>O que precisa ser feito?</th>
                        <th>Prazo Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($listaAtividades)) {
                        echo "<tr><td colspan='3'>Tudo limpo! Nenhuma atividade pendente.</td></tr>";
                    } else {
                        $contador = 0;
                        foreach ($listaAtividades as $atividade) {
                            if ($contador >= 5) break; 
                    ?>
                            <tr>
                                <td><strong><?php echo $atividade->disciplina_nome ?? 'Sem disciplina'; ?></strong></td>
                                <td><?php echo $atividade->titulo; ?></td>
                                <td style="color: #e0245e; font-weight: bold;">
                                    <?php echo date('d/m/Y', strtotime($atividade->data_entrega)); ?>
                                </td>
                            </tr>
                    <?php
                            $contador++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

</body>
</html>