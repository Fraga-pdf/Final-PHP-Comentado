<?php
// ====================================================================================
// ARQUIVO: Model/Atividade.php
// ARQUITETURA: Camada Model (Regras de Banco de Dados)
// OBJETIVO: Executar as operações de banco de dados (CRUD) para as atividades/tarefas.
// ====================================================================================

// IMPACTO DE ARQUITETURA:
// O Model não consegue conversar com o banco sozinho. Precisamos herdar a conexão ativa
// através do arquivo Banco.php. O __DIR__ garante o caminho absoluto correto no servidor.
require_once __DIR__ . "/../config/Banco.php";

class Atividade {

    // ================================================================================
    // C - CREATE (CRIAR / INSERIR NOVA ATIVIDADE)
    // ================================================================================
    
    // IMPACTO NA REGRA DE NEGÓCIO:
    // Essa função recebe os dados digitados no formulário de criação de atividades.
    // Passamos o $id_disciplina para vincular a tarefa a uma matéria específica (Chave Estrangeira)
    // e o $id_usuario para garantir que essa atividade apareça apenas para o aluno dono dela.
    public static function cadastrar($titulo, $descricao, $data_entrega, $id_disciplina, $id_usuario) {
        
        // Monta o comando INSERT de forma segura. As interrogações (?) são os parâmetros do PDO
        // que barram qualquer tentativa de ataque por injeção de SQL (SQL Injection).
        $sql = "INSERT INTO atividades (titulo, descricao, data_entrega, id_disciplina, id_usuario) VALUES (?, ?, ?, ?, ?)";
        
        // Envia a estrutura do comando para o MySQL preparar a inserção.
        $stmt = Banco::getConn()->prepare($sql);
        
        // Executa o comando trocando as interrogações pelas variáveis na ordem exata do array.
        // Retorna verdadeiro (true) se salvou ou falso (false) se o banco rejeitou.
        return $stmt->execute([$titulo, $descricao, $data_entrega, $id_disciplina, $id_usuario]);
    }

    // ================================================================================
    // R - READ (LER / LISTAR AS ATIVIDADES)
    // ================================================================================

    // IMPACTO VISUAL E DE SEGURANÇA:
    // Esta função busca as atividades no banco para exibir na tabela da tela do aluno.
    // O comando SQL usa um INNER JOIN. Isso serve para juntar a tabela de atividades com a 
    // tabela de disciplinas, permitindo que a gente mostre o NOME da matéria na tela, 
    // em vez de mostrar apenas o número do ID da disciplina.
    public static function listarTodas($id_usuario) {
        
        // Seleciona todas as colunas da atividade (a.*) e traz a coluna nome da disciplina (d.nome)
        // fazendo a filtragem para que o aluno só veja as suas próprias tarefas (WHERE a.id_usuario = ?).
        $sql = "SELECT a.*, d.nome AS disciplina_nome 
                FROM atividades a 
                INNER JOIN disciplinas d ON a.id_disciplina = d.id 
                WHERE a.id_usuario = ? 
                ORDER BY a.data_entrega ASC";
                
        $stmt = Banco::getConn()->prepare($sql);
        $stmt->execute([$id_usuario]);
        
        // fetchAll(PDO::FETCH_OBJ) transforma todas as linhas encontradas em um array de objetos.
        // Isso permite acessar os dados na View usando a setinha (ex: $atividade->titulo).
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // IMPACTO NA EDIÇÃO E EXCLUSÃO:
    // Busca uma única atividade no banco com base no ID dela. É usada para carregar os dados
    // antigos dentro do formulário de edição ou para validar se a tarefa realmente existe.
    public static function buscarPorId($id_atividade, $id_usuario) {
        
        // A trava de segurança (id_usuario = ?) é fundamental aqui para impedir que um aluno
        // malicioso mude o ID na URL do navegador e consiga visualizar a atividade de outra pessoa.
        $sql = "SELECT * FROM atividades WHERE id = ? AND id_usuario = ? LIMIT 1";
        
        $stmt = Banco::getConn()->prepare($sql);
        $stmt->execute([$id_atividade, $id_usuario]);
        
        // Retorna apenas um objeto com os dados daquela linha do banco, ou false se não achar nada.
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // ================================================================================
    // U - UPDATE (ATUALIZAR / EDITAR DADOS)
    // ================================================================================

    // IMPACTO NO FLUXO DE CORREÇÃO:
    // Roda o comando UPDATE para modificar os dados de uma tarefa que o aluno já tinha criado.
    public static function atualizar($id_atividade, $titulo, $descricao, $data_entrega, $id_disciplina, $id_usuario) {
        
        // Localiza a linha correta pelo ID da atividade e confere o ID do usuário antes de aplicar as mudanças.
        $sql = "UPDATE atividades SET titulo = ?, descricao = ?, data_entrega = ?, id_disciplina = ? 
                WHERE id = ? AND id_usuario = ?";
                
        $stmt = Banco::getConn()->prepare($sql);
        
        // O array mapeia cada variável para a sua respectiva interrogação na ordem de leitura do SQL.
        return $stmt->execute([$titulo, $descricao, $data_entrega, $id_disciplina, $id_atividade, $id_usuario]);
    }

    // ================================================================================
    // D - DELETE (DELETAR / APAGAR REGISTRO)
    // ================================================================================

    // IMPACTO NA LIMPEZA DO BANCO:
    // Exclui fisicamente a linha correspondente à atividade de dentro da tabela do MySQL.
    public static function deletar($id_atividade, $id_usuario) {
        
        // Trava dupla obrigatória: remove o registro se o ID bater e se pertencer ao usuário logado.
        $sql = "DELETE FROM atividades WHERE id = ? AND id_usuario = ?";
        
        $stmt = Banco::getConn()->prepare($sql);
        return $stmt->execute([$id_atividade, $id_usuario]);
    }
}
?>