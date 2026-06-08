<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Sobrevivência do Semestre</title>
</head>
<body>
    <h2>Criar Conta</h2>

    <form action="?p=fazer-cadastro" method="POST">
        
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        Nome Completo: <input type="text" name="nome" required><br><br>

        Usuário: <input type="text" name="usuario" required><br><br>

        Senha: <input type="password" name="senha" required><br><br>

        CPF: <input type="text" name="cpf" placeholder="000.000.000-00" required><br><br>

        Data de Nascimento: <input type="date" name="data_nascimento" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>

    <br>
    <hr>
    
    <a href="?p=login">Já tem uma conta? Faça o Login</a>
</body>
</html>