<?php
session_start();

// Se não estiver logado, redireciona para a página de login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Dados do usuário logado
$nome_usuario = $_SESSION['nome'] ?? 'Usuário';
$tipo_usuario = $_SESSION['tipo_usuario'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Clínica Veterinária Solidária</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .usuario {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .usuario img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        main {
            padding: 30px 20px;
            text-align: center;
        }
        button.logout {
            padding: 8px 15px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button.logout:hover {
            background-color: #e60000;
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
        }
    </style>
</head>
<body>

<header>
    <h2>Clínica Veterinária Solidária</h2>
    <div class="usuario">
        <!-- Boneco de perfil genérico -->
        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Perfil">
        <span><?php echo htmlspecialchars($nome_usuario); ?></span>
        <form action="logout.php" method="POST" style="display:inline;">
            <button type="submit" class="logout">Logout</button>
        </form>
    </div>
</header>

<main>
    <h1>Bem-vindo, <?php echo htmlspecialchars($nome_usuario); ?>!</h1>
    <p>Você está logado como <strong><?php echo htmlspecialchars($tipo_usuario); ?></strong>.</p>
    <p>Aqui você pode gerenciar atendimentos, cadastros de animais, voluntários e muito mais.</p>
</main>

</body>
</html>
