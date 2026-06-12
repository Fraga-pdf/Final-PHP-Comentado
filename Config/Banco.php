<?php
// ====================================================================================
// ARQUIVO: Config/Banco.php
// OBJETIVO: Conexão com o banco de dados utilizando PDO (Requisito Obrigatório)
// ====================================================================================

class Banco {
    private static $conn;

    public static function getConn() {
        if (!self::$conn) {
            $host = 'localhost';
            $db   = 'sistema_universitario'; // <-- CONFIRME SE ESTE É O NOME DA SUA BASE DE DADOS
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            // DSN (Data Source Name) - A string de conexão do PDO
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            
            $options = [
                // IMPACTO DE RASTREAMENTO: Faz com que o PDO mostre os erros reais de SQL no ecrã
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // IMPACTO DE ARQUITETURA: Força o PDO a devolver os dados sempre como Objetos (mantendo a compatibilidade com as nossas Views)
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$conn = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                // Se o banco falhar, o sistema emite um erro claro e para a execução
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$conn;
    }
}
?>