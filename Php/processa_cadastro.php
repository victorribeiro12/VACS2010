<?php
// processa_cadastro.php - versão final integrada à tabela 'usuarios' com redirecionamento para index.php
session_start();

require_once 'db.php'; // Inclui a conexão com o banco

// --- MAPA DE DOMÍNIOS PARA DETECÇÃO AUTOMÁTICA DE PERFIL ---
const DOMINIOS = [
    'diretor.org'     => 'diretor',
    'gerente.org'     => 'gerente',
    'veterinario.org' => 'veterinario',
    'voluntario.org'  => 'voluntario',
    'secretaria.org'  => 'secretaria'
];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cadastro.php");
    exit();
}

// --- COLETA E TRATA OS DADOS DO FORMULÁRIO ---
// Usar trim() é uma boa prática para remover espaços em branco.
// Não é mais necessário mysqli_real_escape_string, pois usaremos prepared statements.
$nome            = trim($_POST['nome'] ?? '');
$email           = strtolower(trim($_POST['email'] ?? ''));
$endereco        = trim($_POST['endereco'] ?? '');
$telefone        = trim($_POST['telefone'] ?? '');
$data_nascimento = trim($_POST['data_nascimento'] ?? '');
$senha           = trim($_POST['senha'] ?? '');

// Validação básica
if (empty($nome) || empty($email) || empty($senha) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Erro: Nome, e-mail válido e senha são obrigatórios.");
}

// --- DETECTA TIPO DE USUÁRIO PELO DOMÍNIO ---
$partes_email = explode('@', $email);
$dominio_email = end($partes_email);
$tipo_usuario = DOMINIOS[$dominio_email] ?? null;

if ($tipo_usuario === null) {
    die("Erro: O e-mail institucional não pertence a um domínio válido.");
}

$conexao = null; // Inicializa a variável
try {
    $conexao = getConexao(); // **CHAMA A FUNÇÃO PARA OBTER A CONEXÃO**

    // --- VERIFICA SE O E-MAIL JÁ ESTÁ CADASTRADO ---
    $sql_verifica = "SELECT id FROM usuarios WHERE email = ?";
    $stmt_verifica = $conexao->prepare($sql_verifica);
    $stmt_verifica->bind_param("s", $email);
    $stmt_verifica->execute();
    $resultado = $stmt_verifica->get_result();

    if ($resultado->num_rows > 0) {
        echo "Erro: Este e-mail já está cadastrado. <a href='cadastro.php'>Tente novamente</a>.";
    } else {
        // --- HASH DA SENHA ---
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // --- INSERE NOVO USUÁRIO ---
        $sql_insere = "INSERT INTO usuarios (nome, email, endereco, telefone, data_nascimento, senha, tipo_usuario)
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insere = $conexao->prepare($sql_insere);
        $stmt_insere->bind_param("sssssss", $nome, $email, $endereco, $telefone, $data_nascimento, $senha_hash, $tipo_usuario);

        if ($stmt_insere->execute() && $stmt_insere->affected_rows > 0) {
            // --- GERA A PÁGINA DE SUCESSO COM REDIRECIONAMENTO AUTOMÁTICO ---
            echo <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro Realizado com Sucesso</title>
    <meta http-equiv="refresh" content="5;url=login.php">
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            background-color: #eaf6fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .message-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .message-container h2 {
            color: #27ae60; /* Verde para sucesso */
            margin: 0 0 15px 0;
        }
        .message-container p {
            font-size: 16px; color: #333; line-height: 1.6; margin: 0;
        }
        .message-container .redirect-info {
            font-size: 14px; color: #777; margin-top: 25px;
        }
        .message-container a { color: #385a64; font-weight: 600; }
    </style>
</head>
<body>
    <div class="message-container">
        <h2>Cadastro Realizado!</h2>
        <p>Sua conta foi criada com sucesso. Agora você já pode fazer parte da nossa equipe.</p>
        <p class="redirect-info">Você será redirecionado para a página de login em 5 segundos. Se não for, <a href="login.php">clique aqui</a>.</p>
    </div>
</body>
</html>
HTML;
        } else {
            echo "Erro ao realizar o cadastro. Tente novamente.";
        }
        $stmt_insere->close();
    }
    $stmt_verifica->close();
} catch (mysqli_sql_exception $e) {
    error_log("Erro no cadastro: " . $e->getMessage());
    echo "Ocorreu um erro no servidor. Por favor, tente mais tarde.";
} finally {
    if ($conexao) {
        $conexao->close();
    }
}
?>
