<?php
session_start();

// Se o usuário já está logado, redireciona para a página principal
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'db.php';

$erro_login = '';

// Mapa de redirecionamento por tipo de usuário
const PAGINAS_PERFIL = [
    'diretor'     => '../Diretor/diretorDa.html',
    'gerente'     => '../Gerente/gerente.html',
    'veterinario' => '../Veterinario/veterinario.html', // Ajuste este caminho se o arquivo tiver outro nome
    'secretaria'  => '../Secretaria/Secretaria.html',
    'voluntario'  => '../Voluntario/voluntario.html', // Ajuste este caminho se o arquivo tiver outro nome
    'default'     => 'index.php' // Página padrão
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = trim($_POST['senha'] ?? '');

    if (empty($email) || empty($senha)) {
        $erro_login = "E-mail e senha são obrigatórios.";
    } else {
        $conexao = getConexao();
        try {
            $sql = "SELECT id, nome, senha, tipo_usuario FROM usuarios WHERE email = ? LIMIT 1";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $usuario = $resultado->fetch_assoc();

            // Verifica se o usuário existe e se a senha está correta
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Login bem-sucedido: armazena dados na sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'];

                // Regenera o ID da sessão para prevenir session fixation
                session_regenerate_id(true);

                // Redireciona para a página do perfil correspondente
                $pagina_destino = PAGINAS_PERFIL[$usuario['tipo_usuario']] ?? PAGINAS_PERFIL['default'];
                header("Location: " . $pagina_destino);
                exit();
            } else {
                $erro_login = "E-mail ou senha inválidos.";
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            error_log("Erro de login: " . $e->getMessage());
            $erro_login = "Ocorreu um erro no servidor. Tente novamente.";
        } finally {
            if ($conexao) {
                $conexao->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login de Usuário</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            background-color: #eaf6fa; /* Um azul bem claro para o fundo */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
            color: #385a64; /* Tom de azul escuro do tema */
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 24px;
        }
        .login-container input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box; /* Garante que o padding não aumente a largura */
            font-size: 16px;
        }
        .login-container input:focus {
            border-color: #9dd9eb; /* Cor do tema no foco */
            outline: none;
            box-shadow: 0 0 5px rgba(157, 217, 235, 0.5);
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background-color: #385a64; /* Azul escuro do tema */
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .login-container button:hover {
            background-color: #2c464f; /* Tom mais escuro no hover */
        }
        .error-message {
            color: #d9534f; /* Vermelho para erros */
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }
        .register-link {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        .register-link a {
            color: #385a64; /* Azul escuro do tema */
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($erro_login)): ?>
            <p class="error-message"><?php echo htmlspecialchars($erro_login); ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="E-mail institucional" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
        <p class="register-link">Não tem uma conta? <a href="./cadastro.php">Cadastre-se</a></p>
    </div>
</body>
</html>
