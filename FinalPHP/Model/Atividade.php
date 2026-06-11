<?php
// ====================================================================================
// ARQUIVO: Model/Atividade.php
// ARQUITETURA: Camada Model adaptada 100% para MySQLi (Padrão de Aula)
// OBJETIVO: Gerenciar o CRUD de tarefas/atividades e seu relacionamento com as disciplinas.
// ====================================================================================

require_once __DIR__ . "/../config/Banco.php";

class Atividade {

    // ================================================================================
    // C - CREATE (INSERIR NOVA TAREFA)
    // ================================================================================
    public static function cadastrar($titulo, $descricao, $data_entrega, $id_disciplina, $id_usuario) {
        $sql = "INSERT INTO atividades (titulo, descricao, data_entrega, id_disciplina, id_usuario) VALUES (?, ?, ?, ?, ?)";
        $stmt = Banco::getConn()->prepare($sql);
        
        // IMPACTO DO MYSQLI (TIPAGEM DE DADOS):
        // "sssii" -> String (titulo), String (descricao), String (data_entrega), Inteiro (id_disciplina), Inteiro (id_usuario).
        // Nota: Datas no formato do MySQL (AAAA-MM-DD) são tratadas como Strings ('s') no bind_param.
        $stmt->bind_param("sssii", $titulo, $descricao, $data_entrega, $id_disciplina, $id_usuario);
        
        return $stmt->execute();
    }

    // ================================================================================
    // R - READ (LISTAR TAREFAS DO ALUNO COM NOME DA DISCIPLINA)
    // ================================================================================
    public static function listarTodas($id_usuario) {
        // IMPACTO DE BANCO (PONTO DE DEFESA): 
        // Usamos um LEFT JOIN para buscar o nome da disciplina associada lá na outra tabela.
        // O 'ORDER BY a.data_entrega ASC' ordena as tarefas mostrando as mais urgentes primeiro.
        $sql = "
            SELECT a.*, d.nome AS disciplina_nome 
            FROM atividades a 
            LEFT JOIN disciplinas d ON a.id_disciplina = d.id 
            WHERE a.id_usuario = ?
            ORDER BY a.data_entrega ASC
        ";
        
        $stmt = Banco::getConn()->prepare($sql);
        
        // "i" -> O ID do usuário logado é um número Inteiro.
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $lista = [];
        // Converte cada linha retornada em um Objeto e guarda no array.
        while ($row = $result->fetch_object()) {
            $lista[] = $row;
        }
        
        return $lista;
    }

    // IMPACTO DE FLUXO: Busca os dados de uma única tarefa para carregar na tela de Edição.
    public static function buscarPorId($id_atividade, $id_usuario) {
        $sql = "SELECT * FROM atividades WHERE id = ? AND id_usuario = ? LIMIT 1";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "ii" -> Inteiro (id da atividade) e Inteiro (id do usuário).
        $stmt->bind_param("ii", $id_atividade, $id_usuario);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_object();
    }

    // ================================================================================
    // U - UPDATE (SALVAR ALTERAÇÕES DA TAREFA)
    // ================================================================================
    public static function atualizar($id_atividade, $titulo, $descricao, $data_entrega, $id_disciplina, $id_usuario) {
        $sql = "UPDATE atividades SET titulo = ?, descricao = ?, data_entrega = ?, id_disciplina = ? WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "sssiii" -> String (titulo), String (desc), String (data), Inteiro (id_disciplina), Inteiro (id_atividade), Inteiro (id_usuario).
        $stmt->bind_param("sssiii", $titulo, $descricao, $data_entrega, $id_disciplina, $id_atividade, $id_usuario);
        
        return $stmt->execute();
    }

    // ================================================================================
    // D - DELETE (EXCLUIR TAREFA)
    // ================================================================================
    public static function deletar($id_atividade, $id_usuario) {
        $sql = "DELETE FROM atividades WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "ii" -> Inteiro (id_atividade) e Inteiro (id_usuario).
        $stmt->bind_param("ii", $id_atividade, $id_usuario);
        
        return $stmt->execute();
    }
}
?>