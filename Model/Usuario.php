<?php
// ====================================================================================
// ARQUIVO: Model/Usuario.php
// OBJETIVO: Gerenciar o cadastro de alunos no banco de dados (Agora com PDO)
// ====================================================================================

require_once __DIR__ . "/../Config/Banco.php";

class Usuario {

    // ================================================================================
    // C - CREATE (CADASTRAR NOVO USUÁRIO COM CPF E DATA - VERSÃO PDO)
    // ================================================================================
    public static function cadastrar($nome, $usuario, $senha, $cpf, $data_nascimento) {
        // IMPACTO DE SEGURANÇA: Criptografa a senha com hash seguro antes de gravar
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (nome, usuario, senha, cpf, data_nascimento) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = Banco::getConn()->prepare($sql);
        
        // IMPACTO DO PDO: Array limpo e direto, sem precisar de contagem de "sssss"
        return $stmt->execute([$nome, $usuario, $hash, $cpf, $data_nascimento]);
    }

}
?>