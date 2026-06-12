<?php
// ====================================================================================
// ARQUIVO: Model/Atividade.php
// ARQUITETURA: Camada Model adaptada 100% para PDO (Requisito Obrigatório)
// OBJETIVO: Gerenciar o CRUD de tarefas/atividades e o seu relacionamento com as disciplinas.
// ====================================================================================

require_once __DIR__ . "/../Config/Banco.php";

class Atividade {

    // ================================================================================
    // C - CREATE (INSERIR NOVA TAREFA)
    // ================================================================================
    public static function cadastrar($titulo, $descricao, $data_entrega, $id_disciplina, $id_usuario) {
        $sql = "INSERT INTO atividades (titulo, descricao, data_entrega, id_disciplina, id_usuario) VALUES (?, ?, ?, ?, ?)";
        $stmt = Banco::getConn()->prepare($sql);
        
        return $stmt->execute([$titulo, $descricao, $data_entrega, $id_disciplina, $id_usuario]);
    }

    // ================================================================================
    // R - READ (LISTAR TAREFAS DO ALUNO COM NOME DA DISCIPLINA)
    // ================================================================================
    public static function listarTodas($id_usuario) {
        $sql = "
            SELECT a.*, d.nome AS disciplina_nome 
            FROM atividades a 
            LEFT JOIN disciplinas d ON a.id_disciplina = d.id 
            WHERE a.id_usuario = ?
            ORDER BY a.data_entrega ASC
        ";
        
        $stmt = Banco::getConn()->prepare($sql);
        $stmt->execute([$id_usuario]);
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function buscarPorId($id_atividade, $id_usuario) {
        $sql = "SELECT * FROM atividades WHERE id = ? AND id_usuario = ? LIMIT 1";
        $stmt = Banco::getConn()->prepare($sql);
        
        $stmt->execute([$id_atividade, $id_usuario]);
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // ================================================================================
    // U - UPDATE (SALVAR ALTERAÇÕES DA TAREFA)
    // ================================================================================
    public static function atualizar($id_atividade, $titulo, $descricao, $data_entrega, $id_disciplina, $id_usuario) {
        $sql = "UPDATE atividades SET titulo = ?, descricao = ?, data_entrega = ?, id_disciplina = ? WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        return $stmt->execute([$titulo, $descricao, $data_entrega, $id_disciplina, $id_atividade, $id_usuario]);
    }

    // ================================================================================
    // D - DELETE (EXCLUIR TAREFA)
    // ================================================================================
    public static function deletar($id_atividade, $id_usuario) {
        $sql = "DELETE FROM atividades WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        return $stmt->execute([$id_atividade, $id_usuario]);
    }
}
?>