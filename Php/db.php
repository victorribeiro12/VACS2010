<?php
// db.php - Conexão com o banco de dados

$DB_HOST = 'localhost';       // Servidor do banco
$DB_USER = 'root';         // Usuário criado no MySQL
$DB_PASS = '';   // Senha do usuário
$DB_NAME = 'vacs';            // Nome do banco de dados

// --- CRIA A CONEXÃO ---
$conexao = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// --- CHECA ERRO DE CONEXÃO ---
if ($conexao->connect_error) {
    // Em produção, não exibe detalhes do erro por segurança
    die("Erro de conexão com o banco de dados. Contate o administrador.");
}

// --- DEFINE CHARSET UTF-8 para evitar problemas de acentuação ---
$conexao->set_charset("utf8mb4");
?>