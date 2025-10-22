// DOM element references
const tabelaFuncionarios = document.getElementById('tabelaFuncionarios');

const inputBusca = document.getElementById('inputBusca');
const btnBuscar = document.getElementById('btnBuscar');
const btnAdd = document.getElementById('btnAdd'); // Assumindo que este botão abre um modal de adição

// Referências para o novo Modal de Mensagem (Assumindo a estrutura do modal do seu HTML anterior)
const modalMessage = document.getElementById('modalMessage');
const modalTitle = document.getElementById('modalTitle');
const modalBody = document.getElementById('modalBody');

// URL do script PHP da API
const API_URL = "/Diretor/diretor.php";

// Adiciona esta linha para garantir que o valor padrão seja 5 se COLSPAN_COUNT não estiver definido
const numFuncionarios = 5;


const COLSPAN_COUNT = 5;

/**
 * Função de segurança simples para prevenir XSS.
 * @param {string} str - String to sanitize.
 * Substitui <, >, & por suas entidades HTML.
 */
function sanitize(str) {
    if (!str) return 'N/A'; // Retorna 'N/A' se for null, undefined ou string vazia
    return str.toString()
             .replace(/&/g, '&amp;')
             .replace(/</g, '&lt;')
             .replace(/>/g, '&gt;');
}

function closeModal() {
    modalMessage.classList.add('hidden');
}


/**
 * Exibe uma mensagem usando o Modal HTML (substitui o 'alert' original).
 */
function showMessage(title, message, isError = true) {
    console.log(`${isError ? 'ERRO' : 'INFO'}: ${title} - ${message}`);
    
    // Verifica se o modal existe no DOM
    if (modalMessage && modalTitle && modalBody) {
        modalTitle.textContent = title;
        modalBody.textContent = message;

        modalMessage.classList.remove('hidden');
    } else {
        // Fallback para alert caso o HTML não tenha o modal
        alert(`${title}\n\n${message}`);
    }
}

/**
 * Monta as linhas da tabela no HTML com os dados.
 * Nota: As colunas aqui (4 de dados + 1 de ação) devem coincidir com o TH do HTML.
 */
function renderizarTabela(funcionarios) {
    let html = '';

    if (!funcionarios || funcionarios.length === 0) {
        html = `<tr><td colspan="${COLSPAN_COUNT}" class="px-6 py-4 text-center text-gray-500">Nenhum funcionário encontrado.</td></tr>`;
    } else {
        funcionarios.forEach(func => {
            // As propriedades func.id, func.nome, func.funcao, etc. DEVEM vir do PHP.
            html += `
                <tr data-id="${func.id}">
                    <td>${sanitize(func.nome)}</td>
                    <td>${sanitize(func.funcao)}</td>
                    <td>${sanitize(func.setor)}</td>
                    <td>${sanitize(func.telefone)}</td>
                    <td class="whitespace-nowrap">
                        <button data-action="view" data-id="${func.id}" class="action-button view">Ver</button>
                        <button data-action="delete" data-id="${func.id}" class="action-button delete">Excluir</button>
                    </td>
                </tr>
            `;
        });
    }
    tabelaFuncionarios.innerHTML = html;
}

/**
 * Carrega os dados iniciais. Se DADOS_INICIAIS_DO_PHP existir, usa, senão busca.
 * (A busca na API é mais comum para garantir dados frescos).
 */
function carregarDadosIniciais() {
    // Embora o código original sugerisse carregar dados injetados,
    // a prática mais robusta para painéis de gerenciamento é buscar direto na API.
    buscarDados(); 
}

/**
 * Mostra uma mensagem de "carregando" na tabela.
 */
function setTabelaCarregando() {
    // NOTA: Colspan ajustado para 5
    tabelaFuncionarios.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center">Carregando...</td></tr>';
}

/**
 * Função para buscar dados dinamicamente usando fetch.
 */
async function buscarDados() {
    setTabelaCarregando();
    const termoBusca = inputBusca.value.trim();
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
        showMessage("Erro de Comunicação", `Não foi possível carregar os dados. Por favor, tente novamente mais tarde.`, true);
        tabelaFuncionarios.innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Falha ao buscar dados.</td></tr>`;
    }
}

/**
 * Função para adicionar um novo funcionário.
 */
async function adicionarFuncionarioPHP(dadosFormulario) {
    setTabelaCarregando(); // Feedback visual
    try {
        const response = await fetch(`${API_URL}?action=adicionar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dadosFormulario) 
        });

        const resultado = await response.json();

        if (resultado.success) {
            showMessage("Sucesso!", resultado.message, false);
            inputBusca.value = ''; 
            buscarDados(); 
        } else {
            showMessage("Erro no Servidor", resultado.message || 'Erro desconhecido.', true);
        }

    } catch (error) {
        showMessage("Erro de Comunicação", `Não foi possível conectar ao script. Detalhes: ${error.message}`, true);
    }
}

/**
 * Função para visualizar. Busca os dados completos do funcionário na API pelo ID.
 */
async function visualizarFuncionario(id) {
    console.log(`Buscando dados para visualizar ID: ${id}`);
    
    try {
        // Mantido o envio do ID, que é o padrão da tabela e da maioria das APIs CRUD.
        const response = await fetch(`${API_URL}?action=visualizar&id=${id}`); 
        const dados = await response.json();

        if (dados.success === false) { 
            showMessage("Erro", dados.message, true);
        } else {
            // AQUI você preencheria seu modal/formulário de visualização
            console.log("Dados recebidos:", dados);
            showMessage("Visualizar Funcionário", 
                `ID: ${dados.id}\nNome: ${dados.nome}\nFunção: ${dados.funcao}\n(Mais detalhes no console.)`, 
                false
            );
        }
    }   catch (error) {
        showMessage("Erro de Comunicação", `Não foi possível buscar os dados do funcionário. Detalhes: ${error.message}`, true);
    }
}


function gerarFuncionariosAleatorios(numFuncionarios) {
    const nomes = ["Belinha", "Doguinha", "Bethoven", "Rex", "Lessie"];
    const funcoes = ["Veterinário", "Auxiliar", "Recepcionista", "Tosador", "Faxineiro"];
    const setores = ["Clínica", "Cirurgia", "Recepção", "Estética", "Limpeza"];

    const funcionarios = [];
    for (let i = 0; i < numFuncionarios; i++) {
        const nome = nomes[Math.floor(Math.random() * nomes.length)] + " " + (i + 1);
        const funcao = funcoes[Math.floor(Math.random() * funcoes.length)];
        const setor = setores[Math.floor(Math.random() * setores.length)];
        const email = nome.replace(" ", ".").toLowerCase() + "@clinicavet.com";
        const telefone = "(11) 9" + Math.floor(Math.random() * 900000000 + 100000000);

        funcionarios.push({
            id: i + 1,
            nome: nome,
            funcao: funcao,
            setor: setor,
            email: email,
            telefone: telefone
        });
    }
    return funcionarios;
}

/**
 * Carrega os dados iniciais. Se DADOS_INICIAIS_DO_PHP existir, usa, senão busca.
 * (A busca na API é mais comum para garantir dados frescos).
 */

function carregarDadosIniciais() {
    // Aqui, em vez de buscar dados da API, geramos dados aleatórios.
    const funcionariosAleatorios = gerarFuncionariosAleatorios(numFuncionarios);
    renderizarTabela(funcionariosAleatorios);
}

/**
 * Função real para excluir.
 */
async function excluirFuncionario(id) {
    // Pede confirmação
    if (!confirm(`Tem certeza que deseja excluir o funcionário ID ${id}? Esta ação não pode ser desfeita.`)) {
        return; // Usuário cancelou
    }

    try {
        const response = await fetch(`${API_URL}?action=excluir`, {
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id }) // Envia o ID no corpo da requisição
        });

        const resultado = await response.json();

        if (resultado.success) {
            showMessage("Sucesso!", resultado.message, false);
            buscarDados(); // Recarrega a tabela para remover o funcionário
        } else {
            showMessage("Erro no Servidor", resultado.message || 'Erro desconhecido.', true);
        }

    } catch (error) {
        showMessage("Erro de Comunicação", `Não foi possível excluir o funcionário. Detalhes: ${error.message}`, true);
    }
}


// --- Event Listeners ---

// Ação para o botão de busca
btnBuscar.addEventListener('click', buscarDados);

// Permite buscar pressionando "Enter" no campo de busca
inputBusca.addEventListener('keyup', (event) => {
    if (event.key === 'Enter') {
        buscarDados();
    }
});

// Ação para o botão de adicionar (simulação)
btnAdd.addEventListener('click', () => {
    // Simulação: cria dados de teste e chama a função de adicionar
    const dadosDeTeste = {
        nome: `Pessoa Teste ${Date.now()}`.substring(0,25),
        funcao: "Testador",
        setor: "Qualidade",
        telefone: "(11) 98888-7777"
    };
    
    // Chama a função que realmente fala com a API
    adicionarFuncionarioPHP(dadosDeTeste);
});


// Event Delegation: Um único listener na tabela para gerenciar todos os cliques nos botões
tabelaFuncionarios.addEventListener('click', (event) => {
    const target = event.target; // O elemento exato que foi clicado

    // Verifica se o clique foi em um botão com data-action
    if (target.tagName === 'BUTTON' && target.dataset.action) {
        const action = target.dataset.action;
        const id = target.dataset.id;

        if (action === 'view') {
            visualizarFuncionario(id);
        } else if (action === 'delete') {
            excluirFuncionario(id);
        }
    }
});

// Inicia a renderização quando a página é carregada
window.onload = carregarDadosIniciais;

modalMessage.addEventListener('click', (event) => {
    closeModal();
});

inputBusca.addEventListener('input', function() {
    buscarDados();
});