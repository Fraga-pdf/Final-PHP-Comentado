<?php
// Cria a classe Banco que vai guardar a nossa conexão.
// IMPACTO: Centraliza o acesso ao banco de dados para não precisarmos ficar repetindo linhas de conexão em cada arquivo do projeto.
class Banco {

    // Cria uma propriedade privada e estática chamada $instancia.
    // IMPACTO: Ela serve para segurar a conexão ativa por baixo dos panos, evitando que o PHP abra várias conexões ao mesmo tempo e pese o servidor.
    private static $instancia = null;

    // Cria a função pública e estática getConn() (igualzinho ao modelo usado na sua prova).
    // IMPACTO: É a função que os outros arquivos vão chamar (Banco::getConn()) toda vez que precisarem rodar um comando SQL.
    public static function getConn() {
        
        // Verifica se a $instancia ainda está vazia (ou seja, se é a primeira vez que o site tenta falar com o banco).
        if (self::$instancia === null) {
            
            // O bloco try tenta rodar a conexão. Se der erro, joga pro catch.
            try {
                // Configura as credenciais diretamente dentro do construtor do PDO.
                // 'mysql:host=localhost:3307;dbname=sistema_universitario;charset=utf8' diz onde está o MySQL, qual a porta e o nome do banco.
                // 'root' é o usuário padrão do XAMPP e '' (vazio) é a senha padrão.
                // IMPACTO: Liga o PHP ao MySQL usando o PDO (exigência do trabalho) e força o uso de UTF-8 para não bugar acentos e cedilhas.
                self::$instancia = new PDO('mysql:host=localhost:3307;dbname=sistema_universitario;charset=utf8', 'root', '');
                
                // Ativa o modo de erros do PDO para lançar exceções caso o SQL tenha erros.
                // IMPACTO: Se você errar o nome de uma coluna no banco, o PHP vai te avisar na tela exatamente onde está o erro, facilitando o estudo.
                self::$instancia->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            } 
            // Caso a conexão falhe (XAMPP desligado, por exemplo), o catch captura o erro.
            catch (PDOException $e) {
                // O die encerra o sistema e mostra a mensagem.
                // IMPACTO: Impede que o sistema continue rodando com erros visíveis e quebrando a lógica se o banco estiver fora do ar.
                die("Erro na conexão com o banco de dados: " . $e->getMessage());
            }
        }

        // Retorna a conexão configurada e ativa.
        // IMPACTO: Entrega a ponte pronta para o Model fazer os SELECTs e INSERTs.
        return self::$instancia;
    }
}
?>