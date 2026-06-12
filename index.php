<?php
// ====================================================================================
// ARQUIVO: index.php (RAIZ DO PROJETO)
// ARQUITETURA: Roteador Central (Front Controller)
// OBJETIVO: Capturar todas as requisições do usuário e decidir qual página carregar.
// ====================================================================================

// 1. INICIALIZAÇÃO DO AMBIENTE
// O comando session_start() avisa ao servidor que este site vai usar variáveis globais de sessão.
// POR QUE ESTÁ AQUI? O HTTP é "stateless" (não guarda memória entre as páginas). A sessão cria um cookie no navegador
// do aluno e um arquivo temporário no servidor XAMPP para lembrar quem está logado.
// IMPACTO NA DEFESA: Deve ser a primeira linha do arquivo principal. Sem isso, o sistema de login simplesmente não funciona.
session_start();

// IMPACTO DE SEGURANÇA: Garante que visitantes deslogados também tenham um token para enviar formulários
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 2. INCLUSÃO DE ARQUIVOS BASE DA ARQUITETURA
// O require_once garante que o arquivo seja importado apenas uma vez, evitando erros de "classe duplicada".
// POR QUE USAR __DIR__? O __DIR__ é uma constante mágica do PHP que retorna o caminho absoluto da pasta no seu HD.
// IMPACTO NA DEFESA: Evita problemas de caminhos relativos quando o projeto muda de pasta ou vai para outro computador.
require_once __DIR__ . "/config/Banco.php";
require_once __DIR__ . "/Controllers/HomeController.php";
require_once __DIR__ . "/Controllers/DisciplinaController.php";
// IMPACTO NA ARQUITETURA: Permite que o roteador central consiga enxergar e invocar as funções da classe AtividadeController.
require_once __DIR__ . "/Controllers/AtividadeController.php";
require_once __DIR__ . "/Controllers/HumorController.php";
// 3. CAPTURA DA VARIÁVEL DE NAVEGAÇÃO (ROTA)
// Captura a variável 'p' vinda da URL através do método GET (Exemplo: localhost/index.php?p=login).
// POR QUE USAR O OPERADOR '??'? O operador de coalescência nula (??) define um valor padrão. Se o usuário digitar apenas
// o endereço do site sem o '?p=', o PHP assume automaticamente que a rota padrão é 'home'.
// IMPACTO NA DEFESA: Evita que o PHP dispare um aviso visual de "Index undefined" na tela inicial.
$url = $_GET['p'] ?? 'home';


// ====================================================================================
// SYSTEMA DE SEGURANÇA: PROTEÇÃO CONTRA ATAQUES CSRF (Exigência do Trabalho)
// ====================================================================================

// O QUE É CSRF? É um ataque onde um site malicioso tenta enviar um formulário falso se aproveitando de um usuário que já está logado.
// COMO ESSA LÓGICA FUNCIONA? Toda vez que uma tela envia um formulário por POST, ela envia junto um código secreto (token).
// O index.php intercepta a requisição e confere se o código enviado bate com o que o servidor guardou na sessão.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$p = $_GET['p'] ?? 'home';

// Rotas públicas que não precisam da trava rigorosa do token (Login, Registo, etc.)
$rotasLivres = ['login', 'cadastro', 'recuperar', 'autenticar', 'home', 'sobre', 'dicas', 'contato'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !in_array($p, $rotasLivres)) {
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    if (empty($tokenEnviado) || $tokenEnviado !== $_SESSION['csrf_token']) {
        die("Erro de segurança grave: Token CSRF inválido ou ausente.");
    }
}

// ====================================================================================
// MATRIZ DE ROTEAMENTO: ESTRUTURA DE CONTROLE SWITCH
// ====================================================================================

// O switch analisa o conteúdo da variável $url e faz um salto direto para o bloco 'case' correspondente.
// POR QUE USAR SWITCH EM VEZ DE IF/ELSE? O switch limpa a redundância visual e deixa o código elegante, 
// além de cumprir o requisito de "estruturas de controle" exigido no projeto de forma profissional.
switch ($url) {
    
    // TELA DE LOGIN: Se a URL for ?p=login, o roteador chama o HomeController para desenhar o HTML do formulário.
    case 'login':
        HomeController::login();
        break; // O break é obrigatório. Ele avisa ao PHP para sair do switch e não executar os casos de baixo.
        
    // PROCESSAMENTO DO LOGIN: Rota acionada quando o botão "Entrar" envia o formulário POST.
    case 'fazer-login':
        HomeController::autenticar();
        break;
        
    // BOTÃO SAIR: Destrói as credenciais guardadas na memória e limpa o acesso.
    case 'sair':
        HomeController::logout();
        break;

    // TELA DE CADASTRO: Se a URL for ?p=cadastro, o roteador chama o HomeController para desenhar a tela de criação de conta.
    case 'cadastro':
        HomeController::cadastro();
        break;

    // PROCESSAMENTO DO CADASTRO: Rota que recebe os dados de criação da conta (Nome, Usuário, Senha, CPF, Data) e manda gravar.
    case 'fazer-cadastro':
        HomeController::fazerCadastro();
        break;


        
    // ÁREA RESTRITA: Página interna que o aluno acessa após passar com sucesso pela validação de senha.
    case 'feed':
        // IMPORTANTE: Aqui fazemos o require direto da View para manter a assinatura simples que você usou na prova.
        require_once __DIR__ . "/View/feed.php";
        break;
        
    // ROTA PADRÃO / SEGURANÇA: Se o usuário digitar algo que não existe na URL (Ex: ?p=batata) ou se a URL estiver vazia.

    // ================================================================================
    // --- ROTAS DO CRUD DE DISCIPLINAS ---
    // OBJETIVO: Mapear as URLs do sistema para as ações do Controlador de Disciplinas.
    // ================================================================================

    // IMPACTO DE NAVEGAÇÃO (READ): 
    // Rota chamada quando o aluno clica em "Gerenciar Disciplinas" no feed.
    // Ela vai ao banco, busca todas as matérias desse aluno e desenha a tabela na tela.
    case 'disciplinas':
        DisciplinaController::index();
        break;
        
    // IMPACTO VISUAL (CREATE - Formulário): 
    // Rota acionada quando o aluno clica no botão "Nova Disciplina".
    // Ela não salva nada no banco, apenas carrega a tela HTML com os campos em branco.
    case 'disciplina-criar':
        DisciplinaController::criar();
        break;
        
    // IMPACTO DE GRAVAÇÃO (CREATE - Processamento): 
    // Rota "cega" (o usuário não vê a tela). Acionada quando ele clica no botão "Salvar".
    // Recebe os dados do formulário via POST, manda o Controller gravar no banco e redireciona de volta para a lista.
    case 'disciplina-salvar':
        DisciplinaController::salvar();
        break;
        
    // IMPACTO DE UX E FLUXO (UPDATE - Formulário): 
    // Rota chamada quando o aluno clica no botão "Editar" de uma matéria na tabela.
    // O Controller pega o ID da URL, busca os dados antigos no banco e carrega a tela já preenchida.
    case 'disciplina-editar':
        DisciplinaController::editar();
        break;
        
    // IMPACTO DE ATUALIZAÇÃO (UPDATE - Processamento): 
    // Outra rota "cega". Recebe os dados corrigidos do formulário de edição via POST,
    // manda o Controller executar o comando de UPDATE no MySQL e redireciona para a lista.
    case 'disciplina-atualizar':
        DisciplinaController::atualizar();
        break;
        
    // IMPACTO DE EXCLUSÃO (DELETE): 
    // Rota acionada ao clicar em "Excluir". Ela pega o ID da matéria na URL, 
    // verifica se pertence mesmo ao aluno logado, deleta a linha no banco e recarrega a tabela.
    case 'disciplina-excluir':
        DisciplinaController::excluir();
        break;
    
        // ================================================================================
    // --- BLOCO DE ROTAS DO CRUD DE ATIVIDADES ---
    // OBJETIVO: Associar os parâmetros da URL com as ações do AtividadeController.
    // ================================================================================
    
    case 'atividades':
        // IMPACTO NAVEGACIONAIS (READ): Rota acionada ao acessar o painel de gerenciamento de tarefas.
        // O controlador busca as atividades cadastradas no MySQL e monta a visualização da tabela.
        AtividadeController::index();
        break;
        
    case 'atividade-criar':
        // IMPACTO VISUAL (CREATE): Abre a tela HTML contendo o formulário com campos limpos.
        // Também carrega as matérias no select para o aluno conseguir fazer a vinculação.
        AtividadeController::criar();
        break;
        
    case 'atividade-salvar':
        // IMPACTO OPERACIONAL (CREATE - Processamento): Rota invisível disparada pelo formulário POST.
        // Captura as informações digitadas na criação e delega o salvamento (INSERT) para o banco.
        AtividadeController::salvar();
        break;
        
    case 'atividade-editar':
        // IMPACTO VISUAL (UPDATE): Captura o ID via GET, localiza os registros antigos e 
        // carrega a tela de formulário com os dados já preenchidos para alteração pelo usuário.
        AtividadeController::editar();
        break;
        
    case 'atividade-atualizar':
        // IMPACTO OPERACIONAL (UPDATE - Processamento): Rota invisível disparada pelo formulário de edição.
        // Recebe os novos textos via POST e executa a query de atualização (UPDATE) no MySQL.
        AtividadeController::atualizar();
        break;
        
    case 'atividade-excluir':
        // IMPACTO OPERACIONAL (DELETE): Acionada ao clicar no link de exclusão da tabela.
        // Pega o ID passado por GET na URL, executa a remoção física (DELETE) e atualiza a tela.
        AtividadeController::excluir();
        break;
    
    // ================================================================================
    // --- BLOCO DE ROTAS DO CRUD DE HUMOR ---
    // OBJETIVO: Mapear as ações de gerenciamento de estresse no sistema.
    // ================================================================================
    case 'humor':
        // Carrega a listagem do histórico de estresse.
        HumorController::index();
        break;
        
    case 'humor-criar':
        // Abre o formulário limpo para adicionar um novo registro.
        HumorController::criar();
        break;
        
    case 'humor-salvar':
        // Recebe os dados e faz o INSERT no banco.
        HumorController::salvar();
        break;
        
    case 'humor-editar':
        // Abre o formulário preenchido para edição de um registro.
        HumorController::editar();
        break;
        
    case 'humor-atualizar':
        // Recebe os dados alterados via POST e faz o UPDATE.
        HumorController::atualizar();
        break;
        
    case 'humor-excluir':
        // Pega o ID via GET e deleta o registro.
        HumorController::excluir();
        break;

    case 'logout':
        HomeController::logout();
        break;
    
    case 'home':
        HomeController::home();
        break;
    case 'sobre':
        HomeController::sobre();
        break;
    case 'dicas':
        HomeController::dicas();
        break;
    case 'contato':
        HomeController::contato();
        break;

    case 'recuperar':
        HomeController::recuperar();
        break;

    default:
        // O switch cai aqui e o HomeController decide se joga o cara pro login ou direto pro feed se ele já estiver logado.
        HomeController::index();
        break;

}
?>