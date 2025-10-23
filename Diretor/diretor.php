<?php
require_once '/* =================================
   ESTRUTURA BÁSICA (Layout Principal)
   ================================= */

db.php'; // Inclui o arquivo de conexão com o banco de dados
// Habilita o report de erros do MySQLi como exceções
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);



// --- ATENÇÃO: Seus Nomes de Tabela e Colunas ---
// Substitua pelos nomes reais para que as consultas funcionem.
$tabela_db = "funcionarios";
$col_id = "id";
$col_nome = "nome"; // Coluna que será usada para buscar
$col_funcao = "funcao";
$col_setor = "setor";
$col_telefone = "telefone";

/**
 * Função helper para enviar uma resposta JSON padronizada e sair.
 * @param mixed $dados Os dados para enviar.
 * @param int $statusCode O código de status HTTP (ex: 200 para OK, 500 para erro).
 */
function enviarResposta($dados, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados);
    exit; // Termina o script
}

/**
 * Função helper para conectar ao banco de dados.
 */
function getConexao($servidor, $usuario_db, $senha_db, $banco) {
    $conexao = new mysqli($servidor, $usuario_db, $senha_db, $banco);
    $conexao->set_charset("utf8mb4"); // Essencial para acentos
    return $conexao;
}

// --- Ponto de Entrada da API ---

// Determina a ação a ser tomada (buscar, adicionar, etc.)
// Usamos $_REQUEST para pegar 'action' vindo de GET ou POST
$action = $_REQUEST['action'] ?? null;

try {
    $conexao = getConexao($servidor, $usuario_db, $senha_db, $banco);

    switch ($action) {
        // --- AÇÃO: BUSCAR (Mantida) ---
        case 'buscar':
            $termoBusca = $_GET['busca'] ?? '';
            $termoLike = "%" . $termoBusca . "%";

            // Usamos Prepared Statements para prevenir SQL Injection
            $sql = "SELECT 
                        $col_id AS id, 
                        $col_nome AS nome, 
                        $col_funcao AS funcao, 
                        $col_setor AS setor, 
                        $col_telefone AS telefone
                    FROM $tabela_db
                    WHERE $col_nome LIKE ? OR $col_funcao LIKE ? OR $col_setor LIKE ?
                    ORDER BY nome ASC
                    LIMIT 50";
            
            $stmt = $conexao->prepare($sql);
            // "sss" = 3 parâmetros do tipo string
            $stmt->bind_param("sss", $termoLike, $termoLike, $termoLike);
            $stmt->execute();
            
            $resultado = $stmt->get_result();
            $dados = $resultado->fetch_all(MYSQLI_ASSOC);
            
            $stmt->close();
            enviarResposta($dados); // Envia os resultados da busca
            break;

        // --- AÇÃO: ADICIONAR (Mantida) ---
        case 'adicionar':
            // Pega os dados enviados via POST (em formato JSON)
            $dadosJSON = file_get_contents('php://input');
            $novoFuncionario = json_decode($dadosJSON, true);

            // Validação simples (em um app real, valide cada campo)
            if (empty($novoFuncionario) || empty($novoFuncionario['nome'])) {
                enviarResposta(['success' => false, 'message' => 'Dados inválidos ou nome ausente.'], 400);
            }

            // Prepared Statement para INSERT
            $sql = "INSERT INTO $tabela_db ($col_nome, $col_funcao, $col_setor, $col_telefone) 
                     VALUES (?, ?, ?, ?)";
            
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssss", 
                $novoFuncionario['nome'], 
                $novoFuncionario['funcao'], 
                $novoFuncionario['setor'], 
                $novoFuncionario['telefone']
            );
            
            $stmt->execute();
            
            // Verifica se o INSERT funcionou
            if ($stmt->affected_rows > 0) {
                enviarResposta(['success' => true, 'message' => 'Funcionário adicionado com sucesso!']);
            } else {
                enviarResposta(['success' => false, 'message' => 'Não foi possível adicionar o funcionário.'], 500);
            }
            
            $stmt->close();
            break;

        // --- AÇÃO: EXCLUIR (Mantida) ---
        case 'excluir':
            // Espera receber um JSON como: { "id": 123 }
            $dadosJSON = file_get_contents('php://input');
            $dados = json_decode($dadosJSON, true);
            $idParaExcluir = $dados['id'] ?? 0;

            if ($idParaExcluir <= 0) {
                enviarResposta(['success' => false, 'message' => 'ID inválido.'], 400);
            }

            $sql = "DELETE FROM $tabela_db WHERE $col_id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("i", $idParaExcluir); // "i" = parâmetro do tipo integer
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                enviarResposta(['success' => true, 'message' => 'Funcionário excluído com sucesso.']);
            } else {
                enviarResposta(['success' => false, 'message' => 'Funcionário não encontrado ou já excluído.'], 404);
            }
            
            $stmt->close();
            break;

        // --- AÇÃO: VISUALIZAR (CORRIGIDA PARA BUSCAR PELO ID) ---
        case 'visualizar':
            // Pega o parâmetro 'id' da URL, que é o correto.
            $idParaVer = $_GET['id'] ?? 0;

            if ($idParaVer <= 0) {
                enviarResposta(['success' => false, 'message' => 'ID do funcionário ausente ou inválido.'], 400);
            }

            // Seleciona todas as colunas, buscando pelo ID
            $sql = "SELECT * FROM $tabela_db WHERE $col_id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("i", $idParaVer); // "i" para integer
            $stmt->execute();

            $resultado = $stmt->get_result();
            $dados = $resultado->fetch_assoc();

            if ($dados) {
                enviarResposta($dados);
            } else {
                enviarResposta(['success' => false, 'message' => 'Funcionário não encontrado.'], 404);
            }

            $stmt->close();
            break;

        // --- AÇÃO: ATUALIZAR (NOVO) ---
        case 'atualizar':
            $dadosJSON = file_get_contents('php://input');
            $funcionario = json_decode($dadosJSON, true);

            $idParaAtualizar = $funcionario['id'] ?? 0;

            if ($idParaAtualizar <= 0 || empty($funcionario['nome'])) {
                enviarResposta(['success' => false, 'message' => 'Dados inválidos ou ID/nome ausente.'], 400);
            }

            $sql = "UPDATE $tabela_db 
                    SET $col_nome = ?, $col_funcao = ?, $col_setor = ?, $col_telefone = ?
                    WHERE $col_id = ?";
            
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssssi", 
                $funcionario['nome'], 
                $funcionario['funcao'], 
                $funcionario['setor'], 
                $funcionario['telefone'],
                $idParaAtualizar
            );
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                enviarResposta(['success' => true, 'message' => 'Funcionário atualizado com sucesso!']);
            } else {
                enviarResposta(['success' => false, 'message' => 'Nenhuma alteração foi feita ou funcionário não encontrado.'], 200); // 200 OK, mas sem alteração
            }

            $stmt->close();
            break;

        // --- AÇÃO INVÁLIDA (Mantida) ---
        default:
            enviarResposta(['success' => false, 'message' => 'Ação desconhecida.'], 400);
            break;
    }

    // Fecha a conexão em um único lugar, após a ação ser concluída.
    $conexao->close();

} catch (mysqli_sql_exception $e) {
    // Captura qualquer erro de banco de dados
    error_log("Erro de API: " . $e->getMessage()); // Loga o erro no servidor
    enviarResposta(['success' => false, 'message' => 'Erro interno no servidor.'], 500);
}
?>