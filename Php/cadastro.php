<?php
// --- Inicia a sessão ---
session_start();

// --- Se o usuário já estiver logado, redireciona direto para o index ---
// ESSA LÓGICA MANTÉM O USUÁRIO LOGADO LONGE DA PÁGINA DE CADASTRO.
if (isset($_SESSION['usuario_nome'])) {
    header("Location: index.php");
    exit();
}

// O BLOCO DE REDIRECIONAMENTO DESNECESSÁRIO FOI REMOVIDO DAQUI.
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            background-color: #eaf6fa; /* Fundo azul claro do tema */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Garante que o corpo ocupe a tela toda */
            padding: 30px 0; /* Adiciona um padding vertical para rolar se necessário */
        }
        .register-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px; /* Um pouco maior para acomodar mais campos */
            text-align: center;
        }
        .register-container h2 {
            color: #385a64; /* Tom de azul escuro do tema */
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 24px;
        }
        .register-container input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .register-container input:focus {
            border-color: #9dd9eb; /* Cor do tema no foco */
            outline: none;
            box-shadow: 0 0 5px rgba(157, 217, 235, 0.5);
        }
        .register-container button {
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
        .register-container button:hover {
            background-color: #2c464f; /* Tom mais escuro no hover */
        }
        .login-link {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        .login-link a {
            color: #385a64; /* Azul escuro do tema */
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Cadastro de Usuário</h2>
        <form action="processa_cadastro.php" method="POST">
            <input type="text" name="nome" placeholder="Nome completo" required>
            <input type="email" name="email" placeholder="E-mail institucional" required>
            <input type="text" name="endereco" placeholder="Endereço">
            <input type="text" name="telefone" placeholder="Telefone">
            <input type="date" name="data_nascimento" placeholder="Data de Nascimento">
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Cadastrar</button>
        </form>
        <p class="login-link">Já tem uma conta? <a href="login.php">Faça login</a></p>
    </div>
</body>
</html>