<?php
// ====================================================================================
// ARQUIVO: Model/Usuario.php
// ARQUITETURA: Camada Model adaptada 100% para MySQLi (Padrão de Aula)
// OBJETIVO: Gerenciar as buscas de dados de acesso e autenticação no banco.
// ====================================================================================

require_once __DIR__ . "/../config/Banco.php";

class Usuario {

    // ================================================================================
    // BUSCAR USUÁRIO POR ID (Essencial para a função simularLogin)
    // ================================================================================
    public static function buscarPorId($id) {
        // IMPACTO DE SEGURANÇA: Consulta preparada com LIMIT 1 para otimizar a busca.
        $sql = "SELECT * FROM usuarios WHERE id = ? LIMIT 1";
        $stmt = Banco::getConn()->prepare($sql);
        
        // IMPACTO DO MYSQLI (TIPAGEM): 
        // "i" -> O ID do usuário passado como parâmetro é um número Inteiro.
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        // IMPACTO DE RETORNO: Devolve a linha do banco formatada como Objeto.
        return $result->fetch_object();
    }

    // ================================================================================
    // BUSCAR USUÁRIO PELO LOGIN (Usado na autenticação segura)
    // ================================================================================
    public static function buscarPorUsuario($usuario) {
        $sql = "SELECT * FROM usuarios WHERE usuario = ? LIMIT 1";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "s" -> O login (usuario) digitado na tela é um texto (String).
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_object();
    }

    // ================================================================================
    // CADASTRAR NOVO USUÁRIO (Pronto para uso caso adicione uma tela de Registro)
    // ================================================================================
    public static function cadastrar($nome, $usuario, $senha) {
        // IMPACTO DE SEGURANÇA CRÍTICO: Nunca salvamos senhas em texto limpo.
        // O password_hash gera aquela "senha feia" e segura exigida nos projetos modernos.
        $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome, usuario, senha) VALUES (?, ?, ?)";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "sss" -> String (nome), String (usuario), String (senha_criptografada).
        $stmt->bind_param("sss", $nome, $usuario, $senha_criptografada);
        
        return $stmt->execute();
    }
}
?>