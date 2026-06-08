<?php
// ====================================================================================
// ARQUIVO: Controllers/HomeController.php
// ARQUITETURA: Camada Controller (Controlador de Regras de Negócio e Fluxo de Telas)
// OBJETIVO: Gerenciar a lógica de autenticação, criação de contas e recuperação de acesso.
// ====================================================================================

// IMPORTAÇÃO DA CAMADA MODEL:
// O controlador precisa conversar com a base de dados, mas ele não faz isso sozinho por causa do padrão MVC.
// Ele delega os comandos para a classe Usuario que está dentro da pasta Model.
require_once __DIR__ . "/../Model/Usuario.php";

class HomeController {

    // ================================================================================
    // BLOCO 1: CONTROLE DE FLUXO INICIAL E REQUISITOS DE LOGIN
    // ================================================================================

    // POR QUE A FUNÇÃO É 'PUBLIC STATIC'?
    // 'public' significa que qualquer arquivo externo (como o index.php) pode enxergar essa função.
    // 'static' permite que a função seja chamada diretamente pela classe (HomeController::index()) 
    // sem a necessidade de criar um objeto na memória usando o comando 'new'. Isso economiza desempenho.
    public static function index() {
        // VERIFICAÇÃO DE SESSÃO:
        // O isset() checa se a chave 'id_usuario' existe dentro do array global $_SESSION.
        // IMPACTO NA DEFESA: Se o aluno já estiver logado, não faz sentido mostrar o login de novo.
        // O sistema pula direto para a área logada (?p=feed). Caso contrário, força o login.
        if (isset($_SESSION['id_usuario'])) {
            header("Location: ?p=feed");
            exit; // O exit interrompe o processamento do arquivo para garantir o redirecionamento.
        }
        header("Location: ?p=login");
        exit;
    }

    // CARREGAMENTO DA VIEW DE LOGIN:
    // Puxa o arquivo físico HTML/PHP que contém o formulário visual onde o usuário digita os dados.
    public static function login() {
        // Proteção extra: se o aluno já estiver com login ativo, barra a tela de login e joga pro feed.
        if (isset($_SESSION['id_usuario'])) {
            header("Location: ?p=feed");
            exit;
        }
        // require faz a junção das camadas: cola o visual do formulário de login aqui na tela.
        require __DIR__ . "/../View/login.php";
    }

    // PROCESSAMENTO CRÍTICO DE AUTENTICAÇÃO (LOGIN):
    // Rota acionada quando o formulário envia o usuário e a senha via método POST seguro.
    public static function autenticar() {
        // trim() limpa espaços vazios acidentais que o aluno digita no começo ou no fim do nome de usuário.
        $usuario = trim($_POST['usuario'] ?? '');
        $senha = $_POST['senha'] ?? '';

        // --- EXIGÊNCIA OBRIGATÓRIA DO PROJETO: USO DO PDO E PREPARED STATEMENTS ---
        // POR QUE USAR O INTERROGAÇÃO '?' NO LUGAR DA VARIÁVEL DIRETA?
        // Se colocássemos a variável direto na string (usuario = '$usuario'), o sistema ficaria vulnerável a SQL Injection
        // (onde um hacker digita comandos SQL no campo de texto para apagar o seu banco).
        // O caractere '?' cria um parâmetro coringa seguro.
        $sql = "SELECT * FROM usuarios WHERE usuario = ? LIMIT 1";
        
        // Banco::getConn() chama a nossa conexão PDO configurada. O prepare() envia a estrutura do SQL primeiro para o MySQL.
        $stmt = Banco::getConn()->prepare($sql);
        
        // O execute() envia o dado real isolado dentro de um array, substituindo a interrogação. 
        // O banco limpa o dado antes de rodar, neutralizando qualquer ataque.
        $stmt->execute([$usuario]);
        
        // fetch(PDO::FETCH_OBJ) captura a linha encontrada e a transforma em um Objeto PHP nativo.
        // IMPACTO NA DEFESA: Permite acessar as colunas do banco usando a setinha (exemplo: $user->senha, $user->id),
        // mantendo a exata assinatura visual do código que você usou na sua prova.
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        // --- EXIGÊNCIA OBRIGATÓRIA DO PROJETO: VERIFICAÇÃO CRIPTOGRÁFICA ---
        // Como as senhas são salvas em forma de código embaralhado (hash) por segurança, a gente NÃO PODE comparar usando '=='.
        // A função password_verify($senha_digitada, $senha_criptografada_do_banco) faz o cálculo matemático reversível.
        // Se o usuário existir e a senha bater com o hash do banco de dados...
        if ($user && password_verify($senha, $user->senha)) {
            // CRIAÇÃO DA CREDENCIAL NA MEMÓRIA DO SERVIDOR:
            // Guardamos os dados essenciais nas variáveis de sessão para que o site inteiro saiba quem está navegando.
            $_SESSION['id_usuario'] = $user->id;
            $_SESSION['nome'] = $user->nome;
            $_SESSION['usuario'] = $user->usuario;
            
            // Sucesso total! Redireciona o aluno para o painel principal (feed).
            header("Location: ?p=feed");
            exit;
        } else {
            // Falha na autenticação: Usuário não existe ou a senha está incorreta.
            // IMPACTO DE SEGURANÇA: Redireciona de volta para a tela de login limpa, impedindo o acesso.
            header("Location: ?p=login");
            exit;
        }
    }

    // FINALIZAÇÃO DA SESSÃO (LOGOUT):
    // Função limpa as variáveis e expulsa o usuário da área restrita de forma segura.
    public static function logout() {
        // session_destroy() apaga o arquivo temporário de sessão que estava rodando no servidor XAMPP.
        session_destroy();
        // Manda o navegador de volta para a tela de login deslogada.
        header("Location: ?p=login");
        exit;
    }


    // ================================================================================
    // BLOCO 2: CADASTRO DE NOVOS USUÁRIOS (ALUNOS)
    // ================================================================================

    // CARREGAMENTO DA VIEW DE CADASTRO:
    // Puxa o formulário visual com os campos necessários para criar uma conta nova no Jogo.
    public static function cadastro() {
        require __DIR__ . "/../View/cadastro.php";
    }

    // PROCESSAMENTO DO CADASTRO NO BANCO:
    public static function fazerCadastro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Captura e sanitiza todos os dados vindos do formulário HTML.
            $nome = trim($_POST['nome'] ?? '');
            $usuario = trim($_POST['usuario'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $cpf = trim($_POST['cpf'] ?? '');
            $data_nascimento = $_POST['data_nascimento'] ?? '';

            // VALIDAÇÃO EM CAMADA DE CONTROLADOR:
            // O comando empty() checa se o aluno deixou algum campo obrigatório em branco.
            if (empty($nome) || empty($usuario) || empty($senha) || empty($cpf) || empty($data_nascimento)) {
                // Se houver campos vazios, interrompe a gravação e recarrega a tela de cadastro para evitar erros.
                header("Location: ?p=cadastro");
                exit;
            }

            // ACIONAMENTO DA CAMADA MODEL:
            // Envia os dados limpos para a função cadastrar() do Model Usuario. É o Model que sabe como dar o INSERT.
            $sucesso = Usuario::cadastrar($nome, $usuario, $senha, $cpf, $data_nascimento);

            // CONTROLE DE RETORNO DA OPERAÇÃO:
            if ($sucesso) {
                // Se salvou perfeitamente no banco de dados, redireciona o novo aluno para a tela de login.
                header("Location: ?p=login");
                exit;
            } else {
                // Se deu erro na inserção (Ex: banco fora do ar), devolve para a tela de cadastro.
                header("Location: ?p=cadastro");
                exit;
            }
        }
    }


    // ================================================================================
    // BLOCO 3: RECUPERAÇÃO DE ACESSO EXIGIDA PELO PROJETO
    // ================================================================================

    // CARREGAMENTO DA VIEW DE RECUPERAÇÃO:
    // Exibe a tela com os campos de validação de dados pessoais obrigatórios.
    public static function recuperar() {
        require __DIR__ . "/../View/recuperar.php";
    }

    // PROCESSAMENTO E VALIDAÇÃO DOS DOIS FATORES DE SEGURANÇA:
    // Esta lógica atende diretamente ao requisito de negócio exigido pelo Diogo de recuperar 
    // a senha validando as informações