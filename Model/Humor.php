<?php
// ====================================================================================
// ARQUIVO: Model/Humor.php
// ARQUITETURA: Camada Model adaptada 100% para PDO (Requisito Obrigatório)
// OBJETIVO: Gerenciar o CRUD do diário de estresse no banco de dados.
// ====================================================================================

require_once __DIR__ . "/../Config/Banco.php";

class Humor {

    // ================================================================================
    // C - CREATE (SALVAR REGISTRO DE HUMOR)
    // ================================================================================
    public static function cadastrar($nivel_estresse, $data_registro, $id_usuario) {
        $sql = "INSERT INTO humor (nivel_estresse, data_registro, id_usuario) VALUES (?, ?, ?)";
        $stmt = Banco::getConn()->prepare($sql);
        
        // Fim da tipagem "ssi". Dados diretos para a base de dados.
        return $stmt->execute([$nivel_estresse, $data_registro, $id_usuario]);
    }

    // ================================================================================
    // R - READ (LISTAR HISTÓRICO DE HUMOR)
    // ================================================================================
    public static function listarTodos($id_usuario) {
        // IMPACTO DE TELA: O ORDER BY id DESC mantém-se intocável para organizar o seu Feed
        $sql = "SELECT * FROM humor WHERE id_usuario = ? ORDER BY id DESC";
        $stmt = Banco::getConn()->prepare($sql);
        
        $stmt->execute([$id_usuario]);
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function buscarPorId($id_humor, $id_usuario) {
        $sql = "SELECT * FROM humor WHERE id = ? AND id_usuario = ? LIMIT 1";
        $stmt = Banco::getConn()->prepare($sql);
        
        $stmt->execute([$id_humor, $id_usuario]);
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // ================================================================================
    // U - UPDATE (CORRIGIR UM REGISTRO)
    // ================================================================================
    public static function atualizar($id_humor, $nivel_estresse, $data_registro, $id_usuario) {
        $sql = "UPDATE humor SET nivel_estresse = ?, data_registro = ? WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        return $stmt->execute([$nivel_estresse, $data_registro, $id_humor, $id_usuario]);
    }

    // ================================================================================
    // D - DELETE (APAGAR UM REGISTRO)
    // ================================================================================
    public static function deletar($id_humor, $id_usuario) {
        $sql = "DELETE FROM humor WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        return $stmt->execute([$id_humor, $id_usuario]);
    }
}
?>