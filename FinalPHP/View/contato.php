<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Contato - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav style="background-color: #fff; padding: 15px; border-bottom: 1px solid #e6ecf0; text-align: center; margin-bottom: 30px;">
        <a href="?p=home" style="margin: 0 10px;">🏠 Início</a> |
        <a href="?p=sobre" style="margin: 0 10px;">📖 Sobre</a> |
        <a href="?p=dicas" style="margin: 0 10px;">💡 Dicas de Estudo</a> |
        <a href="?p=contato" style="margin: 0 10px;">✉️ Contato</a> |
        <a href="?p=login" style="margin: 0 10px; color: #1da1f2; font-weight: bold;">Entrar no Sistema</a>
    </nav>

    <main style="max-width: 600px; text-align: center;">
        <h2>✉️ Fale Conosco</h2>
        <p style="color: #657786;">Encontrou algum bug? Mande uma mensagem para a nossa equipe.</p>
        
        <form action="?p=home" method="POST" style="box-shadow: none; padding: 0; text-align: left;">
            <label>Seu Nome:</label>
            <input type="text" placeholder="Como quer ser chamado?" required>
            
            <label>Sua Mensagem:</label>
            <textarea rows="4" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 15px;" placeholder="Digite aqui..." required></textarea>
            
            <input type="submit" value="Enviar Mensagem">
        </form>
    </main>
</body>
</html>