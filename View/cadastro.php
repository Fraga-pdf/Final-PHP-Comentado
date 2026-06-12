<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <main style="max-width: 400px; margin-top: 50px; text-align: center;">
        <h2>🎓 Criar Conta</h2>
        <p style="color: #657786;">Junte-se a nós e organize o seu semestre.</p>

        <form action="?p=fazer-cadastro" method="POST" style="box-shadow: none; padding: 0;">
            
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            
            <div style="text-align: left;">
                <label>Nome Completo:</label>
                <input type="text" name="nome" placeholder="Ex: João Williams" required>
            </div>

            <div style="text-align: left;">
                <label>Nickname (Usuário):</label>
                <input type="text" name="usuario" placeholder="Ex: pipo.ads" required>
            </div>

            <div style="text-align: left;">
                <label>CPF:</label>
                <input type="text" name="cpf" placeholder="Ex: 123.456.789-00" required>
            </div>

            <div style="text-align: left;">
                <label>Data de Nascimento:</label>
                <input type="date" name="data_nascimento" required>
            </div>

            <div style="text-align: left;">
                <label>Senha Segura:</label>
                <input type="password" name="senha" placeholder="Crie uma senha" required>
            </div>

            <input type="submit" value="Finalizar Cadastro">
        </form>

        <hr style="border: 1px solid #e6ecf0; margin: 20px 0; display: block;">

        <a href="?p=login" style="font-size: 14px;">⬅️ Já tenho conta. Voltar ao Login</a>
    </main>

</body>
</html>