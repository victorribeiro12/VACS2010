

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
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
        <p style="text-align:center;">Já tem uma conta? <a href="login.php">Faça login</a></p>
    </div>
</body>
</html>
