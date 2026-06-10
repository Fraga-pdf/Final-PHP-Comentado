<?php
// ====================================================================================
// ARQUIVO: Controllers/AtividadeController.php
// ARQUITETURA: Camada Controller (Controlador do CRUD de Atividades)
// OBJETIVO: Gerenciar o fluxo de dados entre as telas de atividades e a camada Model.
// ====================================================================================

// IMPORTAÇÃO DOS MODELS ESSENCIAIS:
// O controlador precisa carregar a classe Atividade para realizar a manipulação das tarefas no banco.
// Ele também carrega o Model Disciplina para permitir vincular a atividade à matéria correta.
require_once __DIR__ . "/../Model/Atividade.php";
require_once __DIR__ . "/../Model/Disciplina.php";

class AtividadeController {

    // ================================================================================
    // R - READ: LISTAR AS ATIVIDADES DO ALUNO
    // ================================================================================
    public static function index() {
        // TRAVA DE SEGURANÇA OBRIGATÓRIA:
        // Se um usuário tentar forçar o acesso digitando '?p=atividades' na URL sem logar,
        // o 'isset' detecta a ausência da sessão, barra o acesso e chuta ele de volta pro login.
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit; // Interrompe o script para garantir o redirecionamento imediato.
        }

        // CAPTURA DO CONTEXTO DO USUÁRIO:
        // Puxa o ID único do aluno que está armazenado na memória da sessão do servidor.
        $id_usuario = $_SESSION['id_usuario'];
        
        // COMUNICAÇÃO COM O BANCO DE DADOS:
        // Aciona o Model para buscar a lista de tarefas vinculadas estritamente a este usuário logado.
        // IMPACTO: A variável $listaAtividades receberá um array de objetos vindos do MySQL.
        $listaAtividades = Atividade::listarTodas($id_usuario);

        // DIRECIONAMENTO DE TELA (VIEW):
        // Carrega o arquivo HTML isolado da listagem. A variável $listaAtividades estará disponível dentro dele.
        require __DIR__ . "/../View/atividades.php";
    }

    // ================================================================================
    // C - CREATE: FORMULÁRIO DE CRIAÇÃO E SALVAMENTO
    // ================================================================================
    
    // IMPACTO: Apenas carrega a interface visual do formulário com os campos vazios.
    public static function criar() {
        // Valida se o usuário está devidamente autenticado para acessar o formulário.
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }

        $id_usuario = $_SESSION['id_usuario'];
        
        // INTERCONEXÃO DE TABELAS (PULO DO GATO):
        // Puxa todas as disciplinas do aluno para injetar na caixinha de seleção (<select>) da tela.
        // Sem isso, o aluno não conseguiria escolher a qual matéria aquela atividade pertence.
        $listaDisciplinas = Disciplina::listarTodas($id_usuario);

        // Carrega o HTML do formulário limpo de criação de tarefas.
        require __DIR__ . "/../View/atividade_criar.php";
    }

    // IMPACTO: Processa os dados que o botão "Salvar" enviou por debaixo dos panos via POST.
    public static function salvar() {
        // Proteção para garantir que um usuário deslogado não consiga injetar dados via POST.
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }

        // Verifica se a requisição é do tipo POST para evitar acessos diretos inválidos.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CAPTURA E LIMPEZA DE DADOS:
            // O 'trim' remove espaços em branco acidentais digitados no início ou fim dos textos.
            $titulo = trim($_POST['titulo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $data_entrega = $_POST['data_entrega'] ?? '';
            $id_disciplina = $_POST['id_disciplina'] ?? '';
            $id_usuario = $_SESSION['id_usuario']; // Vincula o dono da tarefa baseado na sessão.

            // VALIDAÇÃO EM CONTROLADOR:
            // O 'empty' checa se algum dos parâmetros cruciais foi enviado totalmente em branco.
            if (empty($titulo) || empty($data_entrega) || empty($id_disciplina)) {
                // Se faltar informação, devolve o usuário para a tela de preenchimento.
                header("Location: ?p=atividade-criar");
                exit;
            }

            // ACIONAMENTO DO MODEL (INSERT):
            // Passa os dados empacotados para a função 'cadastrar' do Model executar a query do PDO.
            Atividade::cadastrar($titulo, $descricao, $data_entrega, $id_disciplina, $id_usuario);

            // REDIRECIONAMENTO UX:
            // Com o registro salvo no banco, envia o aluno de volta para a tabela principal de listagem.
            header("Location: ?p=atividades");
            exit;
        }
    }

    // ================================================================================
    // U - UPDATE: FORMULÁRIO DE EDIÇÃO E ATUALIZAÇÃO
    // ================================================================================
    
    // IMPACTO: Localiza o registro antigo no banco e joga os valores dentro das caixas de texto.
    public static function editar() {
        // Trava de segurança para impedir acessos não autorizados.
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }

        // Captura o ID da atividade que foi passado como parâmetro na URL (?p=atividade-editar&id=5).
        $id_atividade = $_GET['id'] ?? null;
        $id_usuario = $_SESSION['id_usuario'];

        // Se houver um ID válido na URL...
        if ($id_atividade) {
            // Pede ao Model para fazer uma busca rápida no MySQL utilizando a trava de segurança dupla.
            $atividadeAtual = Atividade::buscarPorId($id_atividade, $id_usuario);
            
            // Também busca a lista de disciplinas para preencher o componente de seleção da tela.
            $listaDisciplinas = Disciplina::listarTodas($id_usuario);

            // IMPACTO: Se a tarefa foi localizada com sucesso e pertence ao usuário autenticado...
            if ($atividadeAtual) {
                // Abre a tela visual de edição disponibilizando os dados antigos para alteração.
                require __DIR__ . "/../View/atividade_editar.php";
                return; // Interrompe a função aqui mesmo, finalizando o fluxo com sucesso.
            }
        }

        // Se o ID for inválido ou a tarefa pertencer a outro aluno, a segurança bloqueia e joga para a lista.
        header("Location: ?p=atividades");
        exit;
    }

    // IMPACTO: Recebe as correções dos dados via formulário POST e aplica as mudanças reais no MySQL.
    public static function atualizar() {
        // Trava de segurança para garantir integridade do login.
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }

        // Processa apenas se o formulário foi disparado via método POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Captura os dados novos e o ID oculto da tarefa que veio escondido no formulário.
            $id_atividade = $_POST['id'] ?? null;
            $titulo = trim($_POST['titulo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $data_entrega = $_POST['data_entrega'] ?? '';
            $id_disciplina = $_POST['id_disciplina'] ?? '';
            $id_usuario = $_SESSION['id_usuario'];

            // Validação de segurança: Só executa se as variáveis obrigatórias não estiverem vazias.
            if (!empty($id_atividade) && !empty($titulo) && !empty($data_entrega) && !empty($id_disciplina)) {
                // Aciona o Model para rodar a query de UPDATE controlada por parâmetros.
                Atividade::atualizar($id_atividade, $titulo, $descricao, $data_entrega, $id_disciplina, $id_usuario);
            }

            // Retorna o usuário para a tabela geral de atividades com os dados já atualizados.
            header("Location: ?p=atividades");
            exit;
        }
    }

    // ================================================================================
    // D - DELETE: EXCLUSÃO PERMANENTE
    // ================================================================================
    public static function excluir() {
        // Garante que intrusos não consigam disparar exclusões no banco.
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }

        // Captura o ID do registro que se deseja apagar diretamente a partir da URL.
        $id_atividade = $_GET['id'] ?? null;
        $id_usuario = $_SESSION['id_usuario'];

        // Se o parâmetro ID existir, dispara o comando de remoção segura no banco de dados.
        if ($id_atividade) {
            Atividade::deletar($id_atividade, $id_usuario);
        }

        // Redireciona para atualizar a tabela visual na tela e fazer a tarefa sumir.
        header("Location: ?p=atividades");
        exit;
    }
}
?>