<?php
// ====================================================================================
// ARQUIVO: config/Banco.php
// ARQUITETURA: Classe de conexão com o banco de dados MySQLi.
// OBJETIVO: Centralizar a conexão para não precisarmos repetir código em cada arquivo.
// ====================================================================================

class Banco {
    
    // IMPACTO DE ARQUITETURA: Propriedade privada que guarda a conexão ativa.
    // Isso evita que o PHP abra várias conexões ao mesmo tempo, o que deixaria o sistema lento.
    private static $conn = null;

    // IMPACTO DE FLUXO: Função que os outros arquivos vão chamar (Banco::getConn())
    // toda vez que precisarem conversar com o banco de dados.
    public static function getConn() {
        
        // Verifica se é a primeira vez que o sistema tenta conectar.
        if (self::$conn === null) {
            
            // IMPACTO DE CONEXÃO: É aqui que a ligação real acontece usando a classe nativa do PHP.
            // Passamos o servidor (localhost), usuário (root), senha vazia, nome do banco e a porta (3306).
            self::$conn = new mysqli("localhost", "root", "", "sistema_universitario", 3306);

            // IMPACTO DE SEGURANÇA: Se o XAMPP estiver desligado ou o nome do banco estiver errado,
            // o 'die' encerra o sistema e avisa qual foi o erro, impedindo o carregamento de telas quebradas.
            if (self::$conn->connect_error) {
                die("Erro crítico na conexão com o banco de dados: " . self::$conn->connect_error);
            }

            // IMPACTO DE RENDERIZAÇÃO: Força o uso de caracteres UTF-8. 
            // Garante que acentos e cedilhas não fiquem desconfigurados ao buscar dados do banco.
            self::$conn->set_charset("utf8mb4");
        }

        // Retorna a ponte de conexão pronta para os Controladores e Models usarem.
        return self::$conn;
    }
}
?>