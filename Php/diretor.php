<?php
$servidor = "localhost";
$usuario_db = "root";
$senha_db = "";
$banco = "vacs1"; // Nome do seu banco de dados.

$conexao = new mysqli($servidor, $usuario_db, $senha_db, $banco);

// Array que guardará os dados para o JavaScript
$funcionarios_iniciais = [];

// Verifica se a conexão foi bem-sucedida
if ($conexao->connect_error) {
    // Se a conexão falhar, o array continuará vazio.
    // Em um ambiente real, seria bom registrar este erro em um log.
    // die("Erro de conexão: " . $conexao->connect_error); // Descomente apenas para depurar
} else {
    // A conexão funcionou, vamos buscar os dados corretos.

    // <<< PONTO PRINCIPAL DA CORREÇÃO >>>
    // A consulta SQL foi ajustada para buscar os campos que o JavaScript precisa.
    // ATENÇÃO: Substitua 'sua_tabela_funcionarios' pelo nome real da sua tabela.
    // Use os aliases (AS) para garantir que os nomes das chaves no resultado
    // sejam exatamente 'id', 'nome', 'funcao', 'setor', 'telefone'.
    
    $sql = "SELECT 
                sua_coluna_id         AS id, 
                sua_coluna_nome       AS nome, 
                sua_coluna_funcao     AS funcao, 
                sua_coluna_setor      AS setor,
                sua_coluna_telefone   AS telefone
            FROM 
                sua_tabela_funcionarios 
            ORDER BY 
                nome ASC 
            LIMIT 50";

    $resultado = $conexao->query($sql);
    
    // Verifica se a consulta retornou resultados
    if ($resultado && $resultado->num_rows > 0) {
        // Itera sobre cada linha do resultado e adiciona ao nosso array
        while($linha = $resultado->fetch_assoc()) {
            $funcionarios_iniciais[] = $linha;
        }
    }
    
    // Fecha a conexão com o banco de dados
    $conexao->close();
}

// Ao final deste script, a variável $funcionarios_iniciais estará pronta
// para ser usada pelo arquivo que renderiza o HTML (ex: /Diretor/painel.php).
?>