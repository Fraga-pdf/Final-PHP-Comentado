<?php
// ====================================================================================
// ARQUIVO: Model/Disciplina.php
// ARQUITETURA: Camada Model adaptada 100% para MySQLi (Padrão de Aula)
// OBJETIVO: Gerenciar o CRUD de disciplinas no banco de dados.
// ====================================================================================

require_once __DIR__ . "/../config/Banco.php";

class Disciplina {

    // ================================================================================
    // C - CREATE (SALVAR NO BANCO)
    // ================================================================================
    public static function cadastrar($nome, $carga_horaria, $id_usuario) {
        // O SQL usa as interrogações (?) no lugar das variáveis para evitar Injeção de SQL
        $sql = "INSERT INTO disciplinas (nome, carga_horaria, id_usuario) VALUES (?, ?, ?)";
        $stmt = Banco::getConn()->prepare($sql);
        
        // IMPACTO DO MYSQLI: "sii" significa que estamos enviando: String, Inteiro, Inteiro.
        $stmt->bind_param("sii", $nome, $carga_horaria, $id_usuario);
        
        return $stmt->execute();
    }

    // ================================================================================
    // R - READ (LISTAR DADOS)
    // ================================================================================
    public static function listarTodas($id_usuario) {
        $sql = "SELECT * FROM disciplinas WHERE id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "i" significa que o parâmetro $id_usuario é um número Inteiro.
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        
        // IMPACTO DO MYSQLI: Pegamos o resultado bruto da execução
        $result = $stmt->get_result();
        
        // Criamos um array vazio e vamos preenchendo ele transformando cada linha do banco num Objeto
        $lista = [];
        while ($row = $result->fetch_object()) {
            $lista[] = $row;
        }
        
        return $lista; // Devolvemos a lista pronta para a View desenhar a tabela
    }

    public static function buscarPorId($id_disciplina, $id_usuario) {
        $sql = "SELECT * FROM disciplinas WHERE id = ? AND id_usuario = ? LIMIT 1";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "ii" = Inteiro (id da disciplina) e Inteiro (id do usuário)
        $stmt->bind_param("ii", $id_disciplina, $id_usuario);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        // Retorna apenas um objeto (uma única linha), pois estamos buscando por ID
        return $result->fetch_object();
    }

    // ================================================================================
    // U - UPDATE (ATUALIZAR DADOS)
    // ================================================================================
    public static function atualizar($id_disciplina, $nome, $carga_horaria, $id_usuario) {
        $sql = "UPDATE disciplinas SET nome = ?, carga_horaria = ? WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "siii" = String (nome), Inteiro (carga), Inteiro (id disciplina), Inteiro (id usuario)
        $stmt->bind_param("siii", $nome, $carga_horaria, $id_disciplina, $id_usuario);
        
        return $stmt->execute();
    }

    // ================================================================================
    // D - DELETE (EXCLUIR DADOS)
    // ================================================================================
    public static function deletar($id_disciplina, $id_usuario) {
        $sql = "DELETE FROM disciplinas WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "ii" = Inteiro, Inteiro
        $stmt->bind_param("ii", $id_disciplina, $id_usuario);
        
        return $stmt->execute();
    }
}
?>