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

 public static function autenticar() {
        $usuario = trim($_POST['usuario'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $lembrar = isset($_POST['lembrar']);

        $sql = "SELECT * FROM usuarios WHERE usuario = ? LIMIT 1";
        $stmt = Banco::getConn()->prepare($sql);
        
        // PDO MÁGICO: Passamos as variáveis diretamente dentro do execute([])
        $stmt->execute([$usuario]);
        
        // O fetch() substitui o get_result() e o fetch_object() do MySQLi
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user->senha)) {
            $_SESSION['id_usuario'] = $user->id;
            $_SESSION['nome']       = $user->nome;
            $_SESSION['usuario']    = $user->usuario;
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            if ($lembrar) {
                setcookie('lembrar_usuario', $usuario, time() + (86400 * 30), "/");
            } else {
                setcookie('lembrar_usuario', '', time() - 3600, "/");
            }
            
            header("Location: ?p=feed");
            exit;
        } else {
            header("Location: ?p=login");
            exit;
        }
    }
    // ================================================================================
    // ROTA DE CADASTRO (CRIAR CONTA)
    // ================================================================================
    public static function cadastro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome            = trim($_POST['nome'] ?? '');
            $usuario         = trim($_POST['usuario'] ?? '');
            $senha           = $_POST['senha'] ?? '';
            $cpf             = trim($_POST['cpf'] ?? '');
            $data_nascimento = trim($_POST['data_nascimento'] ?? '');
            
            // Verifica se o aluno preencheu absolutamente todos os campos
            if (!empty($nome) && !empty($usuario) && !empty($senha) && !empty($cpf) && !empty($data_nascimento)) {
                
                require_once __DIR__ . "/../Model/Usuario.php";
                
                // Envia os 5 dados para o Model salvar no banco
                Usuario::cadastrar($nome, $usuario, $senha, $cpf, $data_nascimento);
                
                // Redireciona o novo aluno para a tela de login
                header("Location: ?p=login");
                exit;
            }
        }
        
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

  public static function recuperar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cpf = trim($_POST['cpf'] ?? '');
            $data_nascimento = trim($_POST['data_nascimento'] ?? '');
            $nova_senha = $_POST['nova_senha'] ?? '';

            if (!empty($cpf) && !empty($data_nascimento) && !empty($nova_senha)) {
                
                $sql = "SELECT id FROM usuarios WHERE cpf = ? AND data_nascimento = ?";
                $stmt = Banco::getConn()->prepare($sql);
                // Executamos passando os dois parâmetros diretamente
                $stmt->execute([$cpf, $data_nascimento]);
                $user = $stmt->fetch();

                if ($user) {
                    $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    
                    $sqlUpdate = "UPDATE usuarios SET senha = ? WHERE id = ?";
                    $stmtUpdate = Banco::getConn()->prepare($sqlUpdate);
                    $stmtUpdate->execute([$hash, $user->id]);

                    echo "<script>alert('Senha alterada com sucesso!'); window.location.href='?p=login';</script>";
                    exit;
                } else {
                    echo "<script>alert('Erro de validação: CPF ou Data de Nascimento não encontrados ou incorretos.');</script>";
                }
            }
        }
        
        require __DIR__ . "/../View/recuperar.php";
    }
    // ================================================================================
    // ROTAS PÚBLICAS (REQUISITO: 4 PÁGINAS ABERTAS)
    // ================================================================================
    public static function home()    { require __DIR__ . "/../View/home.php"; }
    public static function sobre()   { require __DIR__ . "/../View/sobre.php"; }
    public static function dicas()   { require __DIR__ . "/../View/dicas.php"; }
    public static function contato() { require __DIR__ . "/../View/contato.php"; }
}
?>