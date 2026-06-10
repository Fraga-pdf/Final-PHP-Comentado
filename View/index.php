<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <a href="?p=dashboard">Painel Inicial</a> | 
        <a href="?p=disciplinas">Disciplinas (CRUD 1)</a> | 
        <a href="?p=atividades">Atividades (CRUD 2)</a> | 
        <a href="?p=humor">Meu Humor (CRUD 3)</a> | 
        
        <a href="?p=sair">Sair</a>
    </nav>

    <hr>

    <h2>Bem-vindo(a), <?php echo $_SESSION['usuario_nome']; ?>!</h2>

    <div style="border: 1px solid black; padding: 10px; margin-top: 20px;">
        <h3>Resumo do seu Semestre:</h3>
        <p><strong>XP Atual:</strong> (em construção...)</p>
        <p><strong>Total de Faltas:</strong> (em construção...)</p>
        <p><strong>Último registro de estresse:</strong> (em construção...)</p>
    </div>

    <div style="border: 1px solid red; padding: 10px; margin-top: 20px;">
        <h3>Próximas Atividades</h3>
        <p>A lista de provas e trabalhos vai aparecer aqui logo mais.</p>
    </div>

</body>
</html>