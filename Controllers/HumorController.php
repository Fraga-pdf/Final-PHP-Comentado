<?php
// ====================================================================================
// ARQUIVO: Controllers/HumorController.php
// OBJETIVO: Gerenciar a exibição e criação dos registros de estresse do aluno.
// ====================================================================================

require_once __DIR__ . "/../Model/Humor.php";

class HumorController {

    // IMPACTO DE ROTA: Abre a tela principal listando todo o histórico do diário (Meu Diário de Estresse)
    public static function index() {
        if (!isset($_SESSION['id_usuario'])) { header("Location: ?p=login"); exit; }
        
        $listaHumor = Humor::listarTodos($_SESSION['id_usuario']);
        require __DIR__ . "/../View/humor.php";
    }

    // IMPACTO DE ROTA: Abre a tela de formulário e salva o novo registro no banco (Registre Agora)
    public static function criar() {
        if (!isset($_SESSION['id_usuario'])) { header("Location: ?p=login"); exit; }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nivel_estresse = $_POST['nivel_estresse'] ?? '';
            $data_registro  = $_POST['data_registro'] ?? date('Y-m-d');
            
            if (!empty($nivel_estresse)) {
                Humor::cadastrar($nivel_estresse, $data_registro, $_SESSION['id_usuario']);
                // Após salvar, joga o aluno de volta pro painel para ele ver o status atualizado!
                header("Location: ?p=feed"); 
                exit;
            }
        }
        
        require __DIR__ . "/../View/humor-criar.php";
    }
}
?>