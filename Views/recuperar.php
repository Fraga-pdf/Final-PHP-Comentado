<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - Sobrevivência do Semestre</title>
</head>
<body>
    <h2>Recuperar Senha</h2>
    <p>Preencha os dados abaixo para cadastrar uma nova senha.</p>

    <form action="?p=fazer-recuperar" method="POST">
        
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        CPF: <input type="text" name="cpf" placeholder="000.000.000-00" required><br><br>

        Data de Nascimento: <input type="date" name="data_nascimento" required><br><br>

        Nova Senha: <input type="password" name="nova_senha" required><br><br>

        <input type="submit" value="Atualizar Senha">
    </form>

    <br>
    <hr>
    
    <a href="?p=login">Voltar para o Login</a>

</body>
</html>