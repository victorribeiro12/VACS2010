// URL do script PHP atualizado para refletir a nova estrutura 
const API_URL = "../Php/diretor.php"; 

// Referências aos elementos do DOM
const tabelaFuncionarios = document.getElementById('tabelaFuncionarios');
const inputBusca = document.getElementById('inputBusca');
const btnBuscar = document.getElementById('btnBuscar');
const btnAdd = document.getElementById('btnAdd'); 

function showMessage(title, message, isError = true) {
    // ... (seu código original do modal aqui) ...
    console.log(`${isError ? 'ERRO' : 'INFO'}: ${title} - ${message}`); // fallback para console
}

/**
 * ATUALIZADO: Monta as linhas da tabela no HTML com os novos dados.
 * @param {Array} funcionarios 
 */
function renderizarTabela(funcionarios) {
    let html = '';
    
    // O colspan continua 5, pois temos 4 colunas de dados + 1 de ações.
    if (!funcionarios || funcionarios.length === 0) {
        html = `<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum funcionário encontrado.</td></tr>`;
    } else {
        funcionarios.forEach(func => {
            // O 'id' ainda é necessário para os botões, mesmo que não seja mais exibido na tabela.
            html += `
                <tr>
                    <td>${func.nome || 'N/A'}</td>
                    <td>${func.funcao || 'N/A'}</td>
                    <td>${func.setor || 'N/A'}</td>
                    <td>${func.telefone || 'N/A'}</td>
                    <td class="whitespace-nowrap">
                        <button onclick="visualizarFuncionario(${func.id})" class="action-button view">Ver</button>
                        <button onclick="excluirFuncionario(${func.id})" class="action-button delete">Excluir</button>
                    </td>
                </tr>
            `;
        });
    }
    tabelaFuncionarios.innerHTML = html;
}

/**
 * Função para carregar os dados iniciais que o PHP já forneceu.
 * (Esta função não precisa de alterações)
 */
function carregarDadosIniciais() {
    if (typeof DADOS_INICIAIS_DO_PHP !== 'undefined' && DADOS_INICIAIS_DO_PHP.length > 0) {
        renderizarTabela(DADOS_INICIAIS_DO_PHP);
    } else {
        tabelaFuncionarios.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum dado inicial para mostrar.</td></tr>';
    }
}

/**
 * Função para buscar dados dinamicamente usando fetch.
 * ATUALIZADO para usar a nova API_URL.
 */
async function buscarDados() {
    tabelaFuncionarios.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center">Buscando...</td></tr>';
    const termoBusca = inputBusca.value.trim();
    
    // Constrói a URL de busca apontando para diretor.php com os parâmetros corretos
    const urlBusca = `${API_URL}?action=buscar&busca=${encodeURIComponent(termoBusca)}`;

    try {
        const response = await fetch(urlBusca);
        if (!response.ok) {
            throw new Error(`Erro HTTP! Status: ${response.status}`);
        }
        
        const dados = await response.json();
        renderizarTabela(dados);

    } catch (error) {
        console.error("Erro na busca de dados:", error);
        showMessage("Erro de Comunicação", `Não foi possível carregar os dados. Detalhes: ${error.message}`, true);
    }
}

/**
 * Função para adicionar um novo funcionário.
 * ATUALIZADO para usar a nova API_URL e enviar os dados corretos.
 */
async function adicionarFuncionarioPHP() {
    // Dados de exemplo com a nova estrutura
    const novoFuncionario = {
        nome: `Nova Pessoa ${Date.now()}`.substring(0,25),
        funcao: "Assistente",
        setor: "Administrativo",
        telefone: "(31) 99999-0000"
    };

    try {
        const response = await fetch(`${API_URL}?action=adicionar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(novoFuncionario) 
        });

        const resultado = await response.json();

        if (resultado.success) {
            showMessage("Sucesso!", resultado.message, false);
            buscarDados(); // Recarrega a tabela para mostrar o novo funcionário
        } else {
            showMessage("Erro no Servidor", resultado.message || 'Erro desconhecido.', true);
        }

    } catch (error) {
        showMessage("Erro de Comunicação", `Não foi possível conectar ao script. Detalhes: ${error.message}`, true);
    }
}

// Funções de Ação (Apenas para demonstração)
function visualizarFuncionario(id) {
    showMessage("Visualizar", `Funcionalidade para visualizar o Funcionário ID ${id}.`, false);
}
function excluirFuncionario(id) {
    showMessage("Exclusão", `Funcionalidade para excluir o Funcionário ID ${id}.`, false);
}

// Associa os Event Listeners aos botões
btnBuscar.addEventListener('click', buscarDados);
btnAdd.addEventListener('click', adicionarFuncionarioPHP);

// Inicia a renderização com os dados que o PHP já injetou na página
window.onload = carregarDadosIniciais;