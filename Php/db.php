<?php
// db.php - Conexão com o banco de dados

// Usar constantes para credenciais é uma boa prática.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vacs');

/**
 * Cria e retorna uma nova conexão com o banco de dados.
 * @return mysqli
 * @throws mysqli_sql_exception Em caso de falha na conexão.
 */
function getConexao() {
    // Habilita o report de erros do MySQLi como exceções
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $conexao->set_charset("utf8mb4");
        return $conexao;
    } catch (mysqli_sql_exception $e) {
        // Em um ambiente de produção, logue o erro em vez de exibi-lo.
        error_log("Erro de conexão com o DB: " . $e->getMessage());
        // Lança a exceção novamente ou morre com uma mensagem genérica.
        die("Erro crítico: Não foi possível conectar ao banco de dados.");
    }
}