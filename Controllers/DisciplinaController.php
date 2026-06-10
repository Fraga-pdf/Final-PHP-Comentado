<?php
// ====================================================================================
// ARQUIVO: Controllers/DisciplinaController.php
// ARQUITETURA: Camada Controller (Controlador de Fluxo do CRUD de Disciplinas)
// OBJETIVO: Fazer a ponte entre as ações do usuário (clicar em salvar, excluir),
// as regras do banco de dados (Model) e as telas HTML (View).
// ====================================================================================

// IMPORTAÇÃO DA CAMADA MODEL:
// O controlador precisa conhecer a classe Disciplina para conseguir mandar ela salvar 
// ou buscar os dados no banco usando o PDO que configuramos.
require_once __DIR__ . "/../Model/Disciplina.php";

class DisciplinaController {

    // ================================================================================
    // R - READ (LISTAR AS DISCIPLINAS NA TELA)
    // ================================================================================

    // IMPACTO DE FLUXO: Esta é a função principal chamada quando o aluno clica no link
    // "Gerenciar Disciplinas" lá no menu do feed.
    public static function index() {
        // BLINDAGEM DE ROTA (SEGURANÇA):
        // Verifica se a sessão do usuário existe. Se um intruso digitar "?p=disciplinas" 
        // na URL sem ter feito login, o código para aqui mesmo e chuta ele pro login.
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }

        // COMUNICAÇÃO COM O MODEL:
        // Pega o ID do aluno que está salvo na sessão e pede pro Model: 
        // "Vá ao banco e traga apenas as matérias que pertencem a este aluno."
        $id_usuario = $_SESSION['id_usuario'];
        $listaDisciplinas = Disciplina::listarTodas($id_usuario);

        // CARREGAMENTO DA VIEW:
        // Puxa o HTML que vai desenhar a tabela na tela.
        // IMPACTO NA DEFESA: A variável $listaDisciplinas criada acima é "injetada" 
        // automaticamente dentro desse arquivo HTML, permitindo que a View faça um loop (foreach)
        // para imprimir cada matéria em uma linha da tabela.
        require __DIR__ . "/../View/disciplinas.php";
    }

    // ================================================================================
    // C - CREATE (EXIBIR O FORMULÁRIO E SALVAR NO BANCO)
    // ================================================================================

    // IMPACTO DE INTERFACE: Apenas carrega a tela com os campos de digitar a matéria nova.
    public static function criar() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }
        // Repare que mantivemos o padrão sem subpastas para facilitar sua vida.
        require __DIR__ . "/../View/disciplina_criar.php";
    }

    // IMPACTO DE PROCESSAMENTO: Recebe os dados quando o botão "Salvar" do formulário é clicado.
    public static function salvar() {
        // Trava de segurança da sessão.
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }

        // Garante que os dados vieram de forma oculta e segura pelo POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Captura e limpa os dados preenchidos na tela de criação.
            $nome = trim($_POST['nome'] ?? '');
            $carga_horaria = trim($_POST['carga_horaria'] ?? '');
            $id_usuario = $_SESSION['id_usuario']; // Puxa o dono da matéria direto da sessão.

            // VALIDAÇÃO DE NEGÓCIO: Impede que o aluno crie uma matéria fantasma sem nome.
            if (empty($nome) || empty($carga_horaria)) {
                // Se faltar algo, devolve ele para o formulário.
                header("Location: ?p=disciplina-criar");
                exit;
            }

            // Manda o Model executar o INSERT no MySQL.
            Disciplina::cadastrar($nome, $carga_horaria, $id_usuario);

            // IMPACTO DE UX (Experiência do Usuário):
            // Após salvar no banco, o sistema redireciona o aluno de volta para a tabela principal.
            // Assim, ele tem o feedback visual imediato de que a matéria apareceu na lista.
            header("Location: ?p=disciplinas");
            exit;
        }
    }

    // ================================================================================
    // U - UPDATE (EXIBIR TELA DE EDIÇÃO E ATUALIZAR)
    // ================================================================================

    // IMPACTO DE FLUXO: Busca os dados antigos da matéria para preencher os campos na tela de edição.
    public static function editar() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }

        // Captura o ID da matéria que veio pela URL (Ex: ?p=disciplina-editar&id=5).
        $id_disciplina = $_GET['id'] ?? null;
        $id_usuario = $_SESSION['id_usuario'];

        if ($id_disciplina) {
            // Pede ao Model para buscar os detalhes EXATOS dessa matéria no banco.
            $disciplinaAtual = Disciplina::buscarPorId($id_disciplina, $id_usuario);
            
            // Se encontrou a matéria (e ela pertence a este aluno), carrega a tela de edição.
            if ($disciplinaAtual) {
                require __DIR__ . "/../View/disciplina_editar.php";
                return; // O return interrompe a função aqui e exibe a tela.
            }
        }
        
        // Se a matéria não existe ou o aluno tentou editar a matéria de outro colega, 
        // a segurança atua e chuta ele de volta pra lista.
        header("Location: ?p=disciplinas");
        exit;
    }

    // IMPACTO DE PROCESSAMENTO: Rota que recebe os dados corrigidos do formulário e faz o UPDATE.
    public static function atualizar() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_disciplina = $_POST['id'] ?? null;
            $nome = trim($_POST['nome'] ?? '');
            $carga_horaria = trim($_POST['carga_horaria'] ?? '');
            $id_usuario = $_SESSION['id_usuario'];

            // Se os dados estiverem certinhos, aciona o Model para rodar o comando SQL de UPDATE.
            if (!empty($id_disciplina) && !empty($nome) && !empty($carga_horaria)) {
                Disciplina::atualizar($id_disciplina, $nome, $carga_horaria, $id_usuario);
            }

            // Volta para a lista de disciplinas atualizada.
            header("Location: ?p=disciplinas");
            exit;
        }
    }

    // ================================================================================
    // D - DELETE (EXCLUIR DO BANCO DE DADOS)
    // ================================================================================

    // IMPACTO DE BANCO: Rota chamada quando o aluno clica no botão "Excluir" na tabela.
    public static function excluir() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: ?p=login");
            exit;
        }

        // Pega o número de identificação da matéria na URL.
        $id_disciplina = $_GET['id'] ?? null;
        $id_usuario = $_SESSION['id_usuario'];

        // Se o ID foi passado na URL, manda o Model rodar o DELETE usando a trava de segurança dupla.
        if ($id_disciplina) {
            Disciplina::deletar($id_disciplina, $id_usuario);
        }

        // O redirecionamento faz a página piscar e a matéria simplesmente "desaparece" da tela.
        header("Location: ?p=disciplinas");
        exit;
    }
}
?>