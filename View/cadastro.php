<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <main style="max-width: 400px; margin-top: 50px; text-align: center;">
        <h2>📝 Crie sua Conta</h2>
        <p style="color: #657786;">Cadastre-se para gerenciar suas obrigações acadêmicas.</p>

        <form action="?p=cadastro" method="POST" style="box-shadow: none; padding: 0;">
            
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

            <div style="text-align: left;">
                <label for="nome">Nome Completo:</label>
                <input type="text" name="nome" id="nome" placeholder="Seu nome" required>
            </div>

            <div style="text-align: left;">
                <label for="usuario">Usuário de Acesso (Login):</label>
                <input type="text" name="usuario" id="usuario" placeholder="Ex: pipo.ads" required>
            </div>

            <div style="text-align: left;">
                <label for="senha">Senha Segura:</label>
                <input type="password" name="senha" id="senha" placeholder="Crie uma senha" required>
            </div>

            <input type="submit" value="Finalizar Cadastro">
        </form>

        <hr style="border: 1px solid #e6ecf0; margin: 20px 0; display: block;">

        <a href="?p=login" style="font-size: 14px;">Já tem uma conta? Faça Login</a>
    </main>

</body>
</html>