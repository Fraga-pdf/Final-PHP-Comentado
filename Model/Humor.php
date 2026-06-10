<?php
// ====================================================================================
// ARQUIVO: Model/Humor.php
// ARQUITETURA: Camada Model adaptada 100% para MySQLi (Padrão de Aula)
// OBJETIVO: Gerenciar o CRUD do diário de estresse no banco de dados.
// ====================================================================================

require_once __DIR__ . "/../config/Banco.php";

class Humor {

    // ================================================================================
    // C - CREATE (SALVAR REGISTRO DE HUMOR)
    // ================================================================================
    public static function cadastrar($nivel_estresse, $data_registro, $id_usuario) {
        $sql = "INSERT INTO humor (nivel_estresse, data_registro, id_usuario) VALUES (?, ?, ?)";
        $stmt = Banco::getConn()->prepare($sql);
        
        // IMPACTO DO MYSQLI (TIPAGEM): 
        // "ssi" -> String (Nível de estresse: 'Tranquilo', 'Estressado'), String (Data), Inteiro (ID do usuário).
        $stmt->bind_param("ssi", $nivel_estresse, $data_registro, $id_usuario);
        
        return $stmt->execute();
    }

    // ================================================================================
    // R - READ (LISTAR HISTÓRICO DE HUMOR)
    // ================================================================================
    public static function listarTodos($id_usuario) {
        // IMPACTO DE TELA (CORREÇÃO): O 'ORDER BY id DESC' é a bala de prata aqui. 
        // Ele força o banco a devolver a lista de trás para frente baseada no ID.
        // O registro que você acabou de criar (Maior ID) será obrigatoriamente o primeiro a aparecer no Feed.
        $sql = "SELECT * FROM humor WHERE id_usuario = ? ORDER BY id DESC";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "i" -> O ID do usuário logado é um Inteiro.
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        
        // IMPACTO DO MYSQLI: Pega o pacote de dados devolvido pelo MySQL.
        $result = $stmt->get_result();
        
        $lista = [];
        // Desempacota linha por linha em formato de Objeto.
        while ($row = $result->fetch_object()) {
            $lista[] = $row;
        }
        
        return $lista;
    }
    // IMPACTO DE FLUXO: Busca um registro específico para preencher a tela de Edição.
    public static function buscarPorId($id_humor, $id_usuario) {
        $sql = "SELECT * FROM humor WHERE id = ? AND id_usuario = ? LIMIT 1";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "ii" -> Inteiro (id do registro) e Inteiro (id do usuário logado).
        $stmt->bind_param("ii", $id_humor, $id_usuario);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_object();
    }

    // ================================================================================
    // U - UPDATE (CORRIGIR UM REGISTRO)
    // ================================================================================
    public static function atualizar($id_humor, $nivel_estresse, $data_registro, $id_usuario) {
        $sql = "UPDATE humor SET nivel_estresse = ?, data_registro = ? WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "ssii" -> String (nível), String (data), Inteiro (id_humor), Inteiro (id_usuario).
        $stmt->bind_param("ssii", $nivel_estresse, $data_registro, $id_humor, $id_usuario);
        
        return $stmt->execute();
    }

    // ================================================================================
    // D - DELETE (APAGAR UM REGISTRO)
    // ================================================================================
    public static function deletar($id_humor, $id_usuario) {
        $sql = "DELETE FROM humor WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        // "ii" -> Inteiro, Inteiro.
        $stmt->bind_param("ii", $id_humor, $id_usuario);
        
        return $stmt->execute();
    }
}
?>