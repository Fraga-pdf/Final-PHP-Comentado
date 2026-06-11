<?php
// IMPACTO DE REQUISITO (COOKIES): Verifica se o navegador tem o cookie guardado.
$usuarioSalvo = $_COOKIE['lembrar_usuario'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <main style="max-width: 400px; margin-top: 50px; text-align: center;">
        <h2>🎓 Bem-vindo!</h2>
        <p style="color: #657786;">Faça o login para acessar seu Diário e Atividades.</p>

        <form action="?p=autenticar" method="POST" style="box-shadow: none; padding: 0;">
            
            <div style="text-align: left;">
                <label for="usuario">Usuário:</label>
                <input type="text" name="usuario" id="usuario" placeholder="Digite seu usuário" value="<?php echo htmlspecialchars($usuarioSalvo); ?>" required>
            </div>

            <div style="text-align: left;">
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" placeholder="Sua senha" required>
            </div>

            <div style="text-align: left; margin-bottom: 20px;">
                <label style="font-weight: normal; font-size: 14px; cursor: pointer;">
                    <input type="checkbox" name="lembrar" <?php echo $usuarioSalvo ? 'checked' : ''; ?>>
                    Lembrar meu usuário
                </label>
            </div>

            <input type="submit" value="Entrar no Sistema">
        </form>

        <hr style="border: 1px solid #e6ecf0; margin: 20px 0; display: block;">

        <div style="text-align: center; margin-top: 15px;">
            <a href="?p=cadastro" style="font-size: 14px; color: #17bf63; font-weight: bold; margin-right: 15px;">Criar uma conta</a>
            <a href="?p=recuperar" style="font-size: 14px; color: #1da1f2;">Esqueceu a senha?</a>
        </div>

    </main>

</body>
</html>