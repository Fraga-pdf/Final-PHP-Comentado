<?php
// ====================================================================================
// ARQUIVO: Controllers/HomeController.php
// ARQUITETURA: Controlador de Autenticação, Roteamento e Cadastro.
// OBJETIVO: Gerenciar o acesso do usuário, registro de novas contas e o painel principal.
// ====================================================================================

// IMPACTO DE DEPENDÊNCIAS: Requisita os Models necessários para as operações de banco.
require_once __DIR__ . "/../Model/Usuario.php";
require_once __DIR__ . "/../Model/Atividade.php"; 
require_once __DIR__ . "/../Model/Humor.php";     

class HomeController {

    // ================================================================================
    // PROCESSAMENTO DE LOGIN (BLINDADO CONTRA SQL INJECTION)
    // ================================================================================
    public static function autenticar() {
        // IMPACTO DE LIMPEZA: O 'trim' remove espaços em branco digitados sem querer no login.
        $usuario = trim($_POST['usuario'] ?? '');
        $senha = $_POST['senha'] ?? '';

        // IMPACTO DE SEGURANÇA: A interrogação (?) evita que códigos maliciosos sejam injetados (SQL Injection).
        $sql = "SELECT * FROM usuarios WHERE usuario = ? LIMIT 1";
        $stmt = Banco::getConn()->prepare($sql);
        
        // IMPACTO DO MYSQLI: O "s" tipa o parâmetro como String (texto).
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        
        // IMPACTO DE DADOS: Recupera o pacote de resultados e converte para objeto (fetch_object).
        $result = $stmt->get_result();
        $user = $result->fetch_object();

        // IMPACTO DE CRIPTOGRAFIA: Verifica se a senha digitada bate com a hash salva no banco de dados.
        if ($user && password_verify($senha, $user->senha)) {
            // IMPACTO DE SESSÃO: Guarda os dados do usuário na memória para ele navegar logado.
            $_SESSION['id_usuario'] = $user->id;
            $_SESSION['nome']       = $user->nome;
            $_SESSION['usuario']    = $user->usuario;
            
            // IMPACTO DE DEFESA CSRF: Gera um token único para proteger os formulários do sistema.
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            // IMPACTO DE FLUXO: Redireciona para o painel principal em caso de sucesso.
            header("Location: ?p=feed");
            exit;
        } else {
            // Se errar a senha ou usuário, é devolvido para a tela de login.
            header("Location: ?p=login");
            exit;
        }
    }

    // ================================================================================
    // TELA E PROCESSAMENTO DE CADASTRO
    // ================================================================================
    public static function cadastro() {
        // IMPACTO DE SEGURANÇA (CRÍTICO): Cria o token de segurança na sessão caso ele ainda não exista.
        // Isso impede o erro de 'Token ausente' quando o arquivo index.php for processar o formulário.
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // IMPACTO DE ROTA (POST): Se o usuário clicou no botão de enviar, processa a gravação.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // IMPACTO DE HIGIENIZAÇÃO: Captura e limpa os dados digitados no formulário.
            $nome    = trim($_POST['nome'] ?? '');
            $usuario = trim($_POST['usuario'] ?? '');
            $senha   = $_POST['senha'] ?? '';

            // IMPACTO DE VALIDAÇÃO: Garante que o usuário não enviou nenhum campo em branco.
            if (!empty($nome) && !empty($usuario) && !empty($senha)) {
                
                // IMPACTO DE BANCO: Chama o Model para gravar o novo usuário com senha criptografada.
                Usuario::cadastrar($nome, $usuario, $senha);
                
                // IMPACTO DE NAVEGAÇÃO: Envia o usuário recém-criado para a tela de login para ele entrar.
                header("Location: ?p=login");
                exit;
            }
        }
        
        // IMPACTO DE INTERFACE (GET): Se for apenas um acesso comum ao link, exibe a tela visual.
        require __DIR__ . "/../View/cadastro.php";
    }

    // ================================================================================
    // CARREGAMENTO DO FEED (DASHBOARD)
    // ================================================================================
    public static function feed() {
        // IMPACTO DE SEGURANÇA: Se não existir uma sessão activa, impede o acesso à URL direta.
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }

        // Pega o ID do aluno logado.
        $id_usuario = $_SESSION['id_usuario'];
        
        // IMPACTO DE INTEGRAÇÃO: Busca as tarefas pendentes e o histórico de estresse nas tabelas.
        $listaAtividades = Atividade::listarTodas($id_usuario);
        $listaHumor      = Humor::listarTodos($id_usuario);
        
        // IMPACTO DE LÓGICA: Pega estritamente o primeiro item do array (o humor mais recente) para exibir.
        $humorAtual      = !empty($listaHumor) ? $listaHumor[0] : null;

        // Injeta os dados colhidos na visualização do painel.
        require __DIR__ . "/../View/feed.php";
    }

    // ================================================================================
    // ROTAS DE NAVEGAÇÃO BÁSICAS E SIMULAÇÃO
    // ================================================================================
    public static function index() {
        // IMPACTO DE PONTO DE ENTRADA: Acessar a raiz joga para o feed (se logado) ou login (se vazio).
        if (isset($_SESSION['id_usuario'])) {
            header("Location: ?p=feed");
            exit;
        }
        header("Location: ?p=login");
        exit;
    }

    public static function login() {
        // IMPACTO DE FLUXO: Impede que um usuário logado acesse a tela de login novamente.
        if (isset($_SESSION['id_usuario'])) {
            header("Location: ?p=feed");
            exit;
        }
        
        // IMPACTO DE SEGURANÇA: Garante o token para a tela de login também.
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require __DIR__ . "/../View/login.php";
    }

    public static function simularLogin() {
        // IMPACTO DE TESTE RÁPIDO: Força o login do usuário 1 (Aluno Teste) ignorando a senha.
        $user = Usuario::buscarPorId(1);
        if ($user) {
            $_SESSION['id_usuario'] = $user->id;
            $_SESSION['nome']       = $user->nome;
            $_SESSION['usuario']    = $user->usuario;
            // Token de segurança necessário para os testes não quebrarem.
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        header("Location: ?p=feed");
        exit;
    }

    public static function logout() {
        // IMPACTO DE ENCERRAMENTO: Destrói completamente o arquivo de sessão e revoga o acesso.
        session_destroy();
        header("Location: ?p=login");
        exit;
    }

}
?>