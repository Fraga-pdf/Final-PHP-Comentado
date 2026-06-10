<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Sobrevivência do Semestre</title>
</head>
<body>
    <h2>Fazer Login</h2>

    <form action="?p=fazer-login" method="POST">
        
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        Usuário: <input type="text" name="usuario" required><br><br>

        Senha: <input type="password" name="senha" required><br><br>

        <input type="submit" value="Entrar">
    </form>

    <br>
    <hr>
    
    <a href="?p=cadastro">Ainda não tem conta? Registe-se</a>
    <br>
    
    <a href="?p=recuperar">Esqueceu a senha?</a>

</body>
</html>