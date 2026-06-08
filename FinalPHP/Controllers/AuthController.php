<?php
// Cria a classe AuthController.
// IMPACTO: Ela agrupa todas as funções de segurança e acesso num lugar só. Sem essa classe, o login não funciona e a gente zera no requisito de autenticação.
class AuthController {

    // Função que apenas puxa a tela de login para o usuário ver.
    // IMPACTO: Carrega o visual (HTML) do formulário para a pessoa digitar o email e a senha.
    public static function login() {
        require "./Views/auth/login.php";
    }

    // Função pesada que realmente processa os dados quando o usuário clica no botão "Entrar".
    // IMPACTO: Essa função bate no banco de dados, confere a senha e cria a sessão. Se a senha ou email estiverem errados, ela barra o acesso.
    public static function fazerLogin() {
        
        // Verifica se o formulário foi enviado pelo método POST (que esconde os dados na requisição, diferente do GET que mostra na URL).
        // IMPACTO: Protege o sistema para que ninguém tente fazer login passando a senha escancarada na barra do navegador.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Pega o que o usuário digitou nos campos. O '?? null' previne que o código quebre se o campo vier vazio.
            // IMPACTO: Guarda os dados do formulário nas variáveis do PHP para a gente poder comparar com o banco de dados.
            $emailForms = $_POST['email'] ?? null;
            $senhaForms = $_POST['senha'] ?? null;

            // Verifica se o cara deixou algum campo em branco.
            if (is_null($emailForms) || is_null($senhaForms)) {
                // IMPACTO: Mostra a tela de login de novo com uma mensagem de erro, impedindo o código de tentar buscar um usuário "vazio" no banco.
                echo "<h3>Erro: Preencha todos os campos!</h3>";
                require "./Views/auth/login.php";
            } 
            // Se os campos foram preenchidos, vamos para a verificação real.
            else {
                // Traz a variável global do nosso banco de dados.
                // IMPACTO: Permite usar a conexão que criamos no config/banco.php para disparar comandos MySql.
                require_once "./config/banco.php";
                global $banco; 
                
                // Prepara a consulta no banco. A gente usa o '?' para evitar ataques de injeção de SQL.
                // IMPACTO: Busca na tabela 'usuarios' se existe alguém com aquele email exato.
                $resp = $banco->prepare("SELECT * FROM usuarios WHERE email = ?");
                $resp->execute([$emailForms]);
                
                // Puxa o resultado no formato de objeto (parecido com o fetch_object() da aula 8).
                // IMPACTO: Converte a linha do banco de dados em uma variável que o PHP consegue ler, tipo $objUsuario->senha.
                $objUsuario = $resp->fetch(PDO::FETCH_OBJ);
                
                // Se o fetch não trouxer nada, significa que não achou o usuário.
                if (!$objUsuario) {
                     // IMPACTO: Trava o processo e avisa que o email não existe no sistema.
                     echo "<h3>Erro: Usuário não encontrado!</h3>";
                     require "./Views/auth/login.php";
                } 
                // Se achou o usuário no banco, agora a gente confere a senha.
                else {
                    // Confere a senha digitada com a senha criptografada que está salva no banco (Requisito do trabalho).
                    // IMPACTO: Garante a segurança pesada do sistema. Se a senha bater com o hash, ele libera a entrada.
                    if (password_verify($senhaForms, $objUsuario->senha)) {
                        
                        // Deu tudo certo! Salva os dados na sessão (a sessão já foi iniciada lá no index.php principal).
                        // IMPACTO: O sistema "lembra" quem está navegando. Se ele for pra página de Disciplinas depois, a gente sabe que é ele.
                        $_SESSION['usuario_id'] = $objUsuario->id;
                        $_SESSION['usuario_nome'] = $objUsuario->nome;
                        
                        // Redireciona o cara lá pro dashboard.
                        // IMPACTO: Tira o usuário da página de login e manda pra área logada do trabalho.
                        header("Location: index.php?p=dashboard");
                    } 
                    // Se a senha estiver errada (não bateu com o hash)...
                    else {
                        // IMPACTO: Barra a entrada e avisa do erro sem travar a tela.
                        echo "<h3>Erro: Senha incorreta!</h3>";
                        require "./Views/auth/login.php";
                    }
                }
            }
        }
    }

    // Função que apenas mostra a tela com o formulário de criar conta.
    // IMPACTO: Exibe o HTML da tela de registro para novos usuários se inscreverem no Sobrevivência do Semestre.
    public static function cadastro() {
        require "./Views/auth/cadastro.php";
    }
    
    // Aqui embaixo ficariam as funções públicas de fazerCadastro() e recuperarSenha() seguindo essa mesma lógica de validação redundante e passo a passo.

    // IMPACTO: Função de segurança que mata a sessão ativa e desloga o usuário. Sem isso, se a pessoa fechar a aba e outra pessoa abrir o site, a conta continuaria logada[cite: 401, 402, 403, 788].
    public static function sair() {
        // Puxa a sessão atual[cite: 401].
        session_start();
        
        // Destrói todas as informações que estavam salvas ali dentro (como o usuario_id e usuario_nome)[cite: 403, 788].
        session_destroy();
        
        // Redireciona o usuário para a tela inicial pública[cite: 789].
        // IMPACTO: Expulsa o cara da área logada de forma segura.
        header("Location: index.php?p=home");
    }
}
?>