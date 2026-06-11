<?php
// IMPACTO: Cria a classe que vai gerenciar o painel do aluno. Tudo que acontece na área logada passa por aqui ou pelos outros controllers de CRUD.
class DashboardController {

    // IMPACTO: Função principal que carrega a tela do dashboard assim que o login dá certo.
    public static function index() {
        
        // --- PROTEÇÃO DE ROTA ---
        // Verifica se a variável 'usuario_id' NÃO existe dentro da sessão. 
        // Lembra que a gente só cria essa variável lá no AuthController DEPOIS que a senha bate?
        // IMPACTO: Se não tem ID na sessão, é porque o cara não fez login. Barra o acesso na hora.
        if (!isset($_SESSION['usuario_id'])) {
            
            // O header("Location: ...") manda o navegador mudar de página imediatamente.
            // IMPACTO: Chuta o intruso de volta para a tela de login.
            header("Location: index.php?p=login");
            
            // O exit encerra o script do PHP.
            // IMPACTO: Garante que o servidor não continue lendo o resto do código abaixo e acidentalmente mostre algo confidencial.
            exit;
        }

        // Se o código passou do 'if' acima, é porque o cara tá logado e é de casa.
        
        // Pega o nome do usuário que guardamos na sessão lá no AuthController.
        // IMPACTO: Guarda o nome numa variável mais simples ($nome_usuario) pra gente usar lá no HTML e mostrar um "Bem-vindo, Fulano!".
        $nome_usuario = $_SESSION['usuario_nome'];

        // Aqui no futuro a gente vai puxar as faltas, os pontos de XP e as próximas atividades pra jogar na tela.
        // Por enquanto, vamos manter simples pra não confundir.

        // Chama o arquivo visual do Dashboard.
        // IMPACTO: Pega o HTML do painel central e "cola" na tela para o usuário ver.
        require "./Views/dashboard/index.php";
    }
}
?>