<?php
// ====================================================================================
// ARQUIVO: Model/Disciplina.php
// ARQUITETURA: Camada Model (Regras de Banco de Dados)
// OBJETIVO: Executar o CRUD (Criar, Ler, Atualizar, Deletar) das disciplinas.
// ====================================================================================

// IMPACTO DE ARQUITETURA: 
// O Model precisa da conexão com o banco para funcionar. O __DIR__ garante que 
// ele ache o arquivo de configuração não importa de onde essa classe seja chamada.
require_once __DIR__ . "/../config/Banco.php";

class Disciplina {

    // ================================================================================
    // C - CREATE (CRIAR / INSERIR)
    // ================================================================================
    
    // IMPACTO NA REGRA DE NEGÓCIO: 
    // Recebe os dados do formulário e o ID do usuário logado. 
    // É vital salvar o id_usuario junto para sabermos a quem essa matéria pertence.
    public static function cadastrar($nome, $carga_horaria, $id_usuario) {
        // Prepara o SQL de inserção. As três interrogações (?) evitam SQL Injection.
        $sql = "INSERT INTO disciplinas (nome, carga_horaria, id_usuario) VALUES (?, ?, ?)";
        $stmt = Banco::getConn()->prepare($sql);
        
        // Executa a query substituindo as interrogações na ordem exata.
        // Retorna TRUE se o banco salvar com sucesso, ou FALSE se der erro.
        return $stmt->execute([$nome, $carga_horaria, $id_usuario]);
    }

    // ================================================================================
    // R - READ (LER / BUSCAR)
    // ================================================================================

    // IMPACTO NA SEGURANÇA E UX:
    // Lista TODAS as matérias, mas com um filtro rigoroso (WHERE id_usuario = ?).
    // Assim, o aluno só vê a própria grade do semestre, e não a de todos os alunos do sistema.
    public static function listarTodas($id_usuario) {
        $sql = "SELECT * FROM disciplinas WHERE id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        $stmt->execute([$id_usuario]);
        
        // O fetchAll(PDO::FETCH_OBJ) puxa VÁRIAS linhas do banco de uma vez só e as 
        // transforma em um array de objetos. Usaremos isso para montar a tabela no HTML.
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // IMPACTO NA EDIÇÃO/EXCLUSÃO:
    // Busca uma matéria específica pelo ID dela, mas conferindo se ela pertence ao usuário logado.
    // Isso impede que um hacker tente apagar a matéria de outro aluno mudando o ID na URL.
    public static function buscarPorId($id_disciplina, $id_usuario) {
        $sql = "SELECT * FROM disciplinas WHERE id = ? AND id_usuario = ? LIMIT 1";
        $stmt = Banco::getConn()->prepare($sql);
        $stmt->execute([$id_disciplina, $id_usuario]);
        
        // Retorna apenas UM objeto (a matéria encontrada) ou false se não achar.
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // ================================================================================
    // U - UPDATE (ATUALIZAR / EDITAR)
    // ================================================================================

    // IMPACTO NO FLUXO DE DADOS:
    // Permite que o aluno corrija o nome ou a carga horária se tiver digitado errado.
    // Novamente, exigimos o id_usuario no WHERE para garantir que ele só altere o que é dele.
    public static function atualizar($id_disciplina, $nome, $carga_horaria, $id_usuario) {
        $sql = "UPDATE disciplinas SET nome = ?, carga_horaria = ? WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        
        // A ordem do array DEVE bater perfeitamente com a ordem das interrogações no SQL.
        return $stmt->execute([$nome, $carga_horaria, $id_disciplina, $id_usuario]);
    }

    // ================================================================================
    // D - DELETE (DELETAR / EXCLUIR)
    // ================================================================================

    // IMPACTO NO BANCO:
    // Remove a linha da tabela permanentemente. A trava dupla (id da disciplina + id do usuario)
    // é a maior defesa que você pode apresentar para provar que seu CRUD é seguro.
    public static function deletar($id_disciplina, $id_usuario) {
        $sql = "DELETE FROM disciplinas WHERE id = ? AND id_usuario = ?";
        $stmt = Banco::getConn()->prepare($sql);
        return $stmt->execute([$id_disciplina, $id_usuario]);
    }
}
?>