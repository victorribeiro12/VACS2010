<?php
session_start();

// Se o usuário não estiver logado, redireciona para o login
if (!isset($_SESSION['usuario_nome'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Clínica Veterinária Solidária</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f8;
            color: #333;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        header h1 {
            margin: 0;
            font-size: 22px;
        }
        .perfil {
            display: flex;
            align-items: center;
        }
        .perfil img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .perfil span {
            font-weight: bold;
            font-size: 16px;
        }
        .logout {
            margin-left: 15px;
            text-decoration: none;
            background-color: #e74c3c;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 14px;
        }
        .logout:hover {
            background-color: #c0392b;
        }

        main {
            max-width: 900px;
            margin: 40px auto;
            padding: 25px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #2c3e50;
            text-align: center;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            text-align: justify;
        }

        footer {
            text-align: center;
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            margin-top: 40px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card h3 {
            color: #4CAF50;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<header>
    <h1>🐾 Clínica Veterinária Solidária</h1>
    <div class="perfil">
        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Perfil">
        <span><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
        <a class="logout" href="logout.php">Sair</a>
    </div>
</header>

<main>
    <h2>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</h2>
    <p>
        Nossa missão é oferecer atendimento veterinário solidário, priorizando o bem-estar animal e o cuidado responsável.
        Aqui você pode participar de campanhas, acompanhar consultas, registrar animais e contribuir com a clínica.
    </p>

    <div class="cards">
        <div class="card">
            <h3>🐶 Consultas Solidárias</h3>
            <p>Agende e acompanhe consultas realizadas por nossos veterinários voluntários.</p>
        </div>
        <div class="card">
            <h3>🐱 Adoção Responsável</h3>
            <p>Ajude animais a encontrarem um novo lar amoroso e seguro.</p>
        </div>
        <div class="card">
            <h3>🩺 Equipe Veterinária</h3>
            <p>Profissionais dedicados e apaixonados pelo cuidado animal.</p>
        </div>
        <div class="card">
            <h3>💚 Voluntariado</h3>
            <p>Contribua com nosso projeto e ajude a transformar vidas!</p>
        </div>
    </div>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Clínica Veterinária Solidária | Desenvolvido com ❤️
</footer>

</body>
</html>
