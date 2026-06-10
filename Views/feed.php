<?php
// ====================================================================================
// ARQUIVO: View/feed.php
// OBJETIVO: Ser a tela principal (Dashboard) do aluno após o login bem-sucedido.
// ====================================================================================

// IMPACTO DE SEGURANÇA E SESSÃO:
// Como esta é a primeira página que o usuário vê após logar, precisamos garantir que o PHP
// tenha acesso às variáveis de sessão que criamos lá no HomeController::autenticar().
// Se a sessão não for iniciada aqui (ou no index que chama esta view), o PHP não vai saber 
// quem é o usuário e a variável $_SESSION ficará invisível, causando erros na tela.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Feed - Sobrevivência do Semestre</title>
</head>
<body>

    <header>
        <h2>Olá, <?php echo $_SESSION['nome'] ?? 'Usuário'; ?>! Bem-vindo ao seu semestre.</h2>
        
        <p>
            <strong>Seu Nickname:</strong> <?php echo $_SESSION['usuario'] ?? 'N/A'; ?> | 
            <strong>Seu ID no Banco:</strong> <?php echo $_SESSION['id_usuario'] ?? 'N/A'; ?>
        </p>
    </header>

    <hr>

    <nav>
        <h3>Menu Rápido</h3>
        <ul>
            <li><a href="?p=feed">Atualizar Feed</a></li>
            
            <li><a href="?p=disciplinas">Gerenciar Disciplinas (CRUD 1)</a></li>
            
            <li><a href="?p=sair" style="color: red;"><strong>Sair do Sistema</strong></a></li>
        </ul>
    </nav>

    <hr>

    <main>
        <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
            <h3>Suas Próximas Atividades</h3>
            <p><em>(O CRUD de Atividades será listado aqui. Nenhuma atividade cadastrada ainda.)</em></p>
        </div>

        <div style="border: 1px solid #ccc; padding: 15px;">
            <h3>Status do Semestre</h3>
            <ul>
                <li><strong>Faltas acumuladas:</strong> 0</li>
                <li><strong>Nível de Estresse:</strong> Tranquilo (Por enquanto...)</li>
            </ul>
        </div>
    </main>

</body>
</html>