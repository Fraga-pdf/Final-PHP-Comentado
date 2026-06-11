<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar Humor - Sobrevivência do Semestre</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <main>
        <h2>🧠 Como você está se sentindo?</h2>
        
        <form action="?p=humor-criar" method="POST">
            
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

            <label>Nível de Estresse Hoje:</label>
            <select name="nivel_estresse" required>
                <option value="Tranquilo">Tranquilo (Tudo sob controle)</option>
                <option value="Moderado">Moderado (Levemente preocupado)</option>
                <option value="Estressado">Estressado (Muitas coisas acumuladas)</option>
                <option value="Surtando">Surtando (Socorro!)</option>
            </select>

            <label>Data do Registro:</label>
            <input type="date" name="data_registro" value="<?php echo date('Y-m-d'); ?>" required>

            <input type="submit" value="Salvar Registro no Diário">
        </form>
        
        <br>
        <a href="?p=feed">⬅️ Voltar ao Painel</a>
    </main>

</body>
</html>