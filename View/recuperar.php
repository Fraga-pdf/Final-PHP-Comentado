<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <main style="max-width: 400px; margin-top: 50px; text-align: center;">
        <h2>🔒 Recuperar Senha</h2>
        <p style="color: #657786;">Valide os seus dados pessoais para criar uma nova senha de acesso.</p>

        <form action="?p=recuperar" method="POST" style="box-shadow: none; padding: 0;">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            
            <div style="text-align: left;">
                <label>O seu CPF:</label>
                <input type="text" name="cpf" placeholder="Ex: 123.456.789-00" required>
            </div>

            <div style="text-align: left;">
                <label>Data de Nascimento:</label>
                <input type="date" name="data_nascimento" required>
            </div>

            <div style="text-align: left;">
                <label>Nova Senha:</label>
                <input type="password" name="nova_senha" placeholder="Digite a nova senha" required>
            </div>

            <input type="submit" value="Redefinir Senha">
        </form>

        <hr style="border: 1px solid #e6ecf0; margin: 20px 0; display: block;">

        <a href="?p=login" style="font-size: 14px;">⬅️ Voltar ao Login</a>
    </main>

</body>
</html>