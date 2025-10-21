   <?php
// processa_cadastro.php - versão final integrada à tabela 'usuarios' com redirecionamento para index.php

require_once 'db.php'; // Inclui a conexão com o banco

// --- MAPA DE DOMÍNIOS PARA DETECÇÃO AUTOMÁTICA DE PERFIL ---
const DOMINIOS = [
    'diretor.org'     => 'diretor',
    'gerente.org'     => 'gerente',
    'veterinario.org' => 'veterinario',
    'voluntario.org'  => 'voluntario',
    'secretaria.org'  => 'secretaria'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- COLETA E TRATA OS DADOS DO FORMULÁRIO ---
    $nome           = mysqli_real_escape_string($conexao, $_POST['nome'] ?? '');
    $email          = mysqli_real_escape_string($conexao, strtolower($_POST['email'] ?? ''));
    $endereco       = mysqli_real_escape_string($conexao, $_POST['endereco'] ?? '');
    $telefone       = mysqli_real_escape_string($conexao, $_POST['telefone'] ?? '');
    $data_nascimento= mysqli_real_escape_string($conexao, $_POST['data_nascimento'] ?? '');
    $senha          = mysqli_real_escape_string($conexao, $_POST['senha'] ?? '');

    if (empty($nome) || empty($email) || empty($senha)) {
        die("Erro: Nome, e-mail e senha são obrigatórios.");
    }

    // --- DETECTA TIPO DE USUÁRIO PELO DOMÍNIO ---
    $dominio_email = substr(strrchr($email, "@"), 1);
    $tipo_usuario = DOMINIOS[$dominio_email] ?? null;

    if ($tipo_usuario === null) {
        // Redireciona para index.php se o domínio não for válido
        header("Location: index.php");
        exit();
    }

    // --- HASH DA SENHA ---
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // --- VERIFICA SE O E-MAIL JÁ ESTÁ CADASTRADO ---
    $sql_verifica = "SELECT id FROM usuarios WHERE email = ?";
    $stmt_verifica = $conexao->prepare($sql_verifica);
    $stmt_verifica->bind_param("s", $email);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        echo "Erro: Este e-mail já está cadastrado. <a href='../cadastro.php'>Tente novamente</a>.";
    } else {
        // --- INSERE NOVO USUÁRIO ---
        $sql_insere = "INSERT INTO usuarios (nome, email, endereco, telefone, data_nascimento, senha, tipo_usuario)
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insere = $conexao->prepare($sql_insere);
        $stmt_insere->bind_param(
            "sssssss",
            $nome,
            $email,
            $endereco,
            $telefone,
            $data_nascimento,
            $senha_hash,
            $tipo_usuario
        );

        if ($stmt_insere->execute()) {
            echo "Cadastro realizado com sucesso! <a href='../login.php'>Faça o login agora</a>.";
        } else {
            echo "Erro ao cadastrar: " . $stmt_insere->error;
        }

        $stmt_insere->close();
    }

    $stmt_verifica->close();
    $conexao->close();

} else {
    header("Location: cadastro.php");
    exit();
}
?>

   
   <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login de Usuário</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f2f2; }
        .container { max-width: 400px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, button { width: 100%; padding: 10px; margin: 5px 0; border-radius: 4px; border: 1px solid #ccc; }
        button { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="E-mail institucional" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
        <p style="text-align:center;">Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
    </div>
</body>
</html>
