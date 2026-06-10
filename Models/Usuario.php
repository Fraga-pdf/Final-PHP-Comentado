<?php

// Faz a inclusão da classe Banco utilizando o caminho absoluto.
// IMPACTO: Garante que este ficheiro consegue utilizar o Banco::getConn() para falar com o MySQL.
require_once __DIR__ . "/../config/Banco.php";

// Cria a classe Usuario.
// IMPACTO: Centraliza todas as operações na base de dados que envolvem os utilizadores do sistema.
class Usuario {

    // Função que chamámos no simularLogin() do HomeController.
    // IMPACTO: Vai à base de dados procurar a linha exata do utilizador através do seu número de ID.
    public static function buscarPorId($id) {
        // Monta a consulta de forma segura com o '?' do PDO.
        $sql = "SELECT * FROM usuarios WHERE id = ? LIMIT 1";
        
        // Pede a ligação ativa à nossa classe Banco e prepara a consulta.
        $stmt = Banco::getConn()->prepare($sql);
        
        // Executa a consulta, trocando o '?' pela variável $id.
        $stmt->execute([$id]);
        
        // Devolve o resultado formatado como um objeto.
        // IMPACTO: Permite que o Controller utilize os dados de forma simples, como $user->nome ou $user->usuario.
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // --- REQUISITOS OBRIGATÓRIOS DO TRABALHO ---
    // Função para registar um novo aluno, recebendo todos os dados do formulário de registo.
    // IMPACTO: Grava uma nova linha na tabela 'usuarios', aplicando o hash na palavra-passe para garantir a segurança.
    public static function cadastrar($nome, $usuario, $senha, $cpf, $data_nascimento) {
        
        // A função password_hash encripta a palavra-passe original (ex: '123') num código complexo.
        // IMPACTO: Cumpre o requisito de segurança do trabalho. Nem quem gere a base de dados consegue ver a palavra-passe real.
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Prepara o INSERT com 5 campos e 5 interrogações.
        $sql = "INSERT INTO usuarios (nome, usuario, senha, cpf, data_nascimento) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = Banco::getConn()->prepare($sql);
        
        // Executa a