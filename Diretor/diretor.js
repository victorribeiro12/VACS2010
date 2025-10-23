// URL do script PHP da API
const API_URL = "diretor.php";

// Referências aos elementos do DOM
const tabelaFuncionarios = document.getElementById('tabelaFuncionarios');
const inputBusca = document.getElementById('inputBusca');
const btnBuscar = document.getElementById('btnBuscar');
const btnAdd = document.getElementById('btnAdd');

// Modal de Mensagem
const modalMessage = document.getElementById('modalMessage');
const modalTitle = document.getElementById('modalTitle');
const modalBody = document.getElementById('modalBody');

// Modal de Formulário (Adicionar/Editar)
const modalForm = document.getElementById('modalForm');
const modalFormTitle = document.getElementById('modalFormTitle');
const formFuncionario = document.getElementById('formFuncionario');
const btnCancel = document.getElementById('btnCancel');
const btnSave = document.getElementById('btnSave');

// Campos do formulário
const formFields = {
    id: document.getElementById('funcionarioId'),
    nome: document.getElementById('nome'),
    funcao: document.getElementById('funcao'),
    setor: document.getElementById('setor'),
    telefone: document.getElementById('telefone')
};

/**
 * Função de segurança simples para prevenir XSS.
 * Substitui <, >, & por suas entidades HTML.
 */
function sanitize(str) {
    if (str === null || str === undefined) return 'N/A';
    return str.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

/**
 * Exibe uma mensagem usando o Modal de Mensagem.
 */
function showMessage(title, message) {
    if (modalMessage && modalTitle && modalBody) {
        modalTitle.textContent = title;
        modalBody.textContent = message;
        modalMessage.classList.remove('hidden');
    } else {
        alert(`${title}\n\n${message}`);
    }
}

/**
 * Abre ou fecha o modal de formulário.
 */
function toggleFormModal(show = false) {
    if (show) {
        modalForm.classList.remove('hidden');
    } else {
        modalForm.classList.add('hidden');
    }
}

/**
 * Monta as linhas da tabela com os dados dos funcionários.
 */
function renderizarTabela(funcionarios) {
    let html = '';
    const COLSPAN_COUNT = 5; // ID, Nome, Função, Setor, Ações

    if (!funcionarios || funcionarios.length === 0) {
        html = `<tr><td colspan="${COLSPAN_COUNT}" class="px-6 py-4 text-center text-gray-500">Nenhum funcionário encontrado.</td></tr>`;
    } else {
        funcionarios.forEach(func => {
            // As propriedades (func.id, func.nome, etc.) vêm do PHP.
            html += `
                <tr data-id="${func.id}">
                    <td>${func.id}</td>
                    <td>${sanitize(func.nome)}</td>
                    <td>${sanitize(func.funcao)}</td>
                    <td>${sanitize(func.setor)}</td>
                    <td class="whitespace-nowrap">
                        <button data-action="edit" data-id="${func.id}" class="action-button edit">Editar</button>
                        <button data-action="delete" data-id="${func.id}" class="action-button delete">Excluir</button>
                    </td>
                </tr>
            `;
        });
    }
    tabelaFuncionarios.innerHTML = html;
}

/**
 * Mostra uma mensagem de "carregando" na tabela.
 */
function setTabelaCarregando() {
    tabelaFuncionarios.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Carregando...</td></tr>';
}

/**
 * Busca dados na API com base no termo de busca.
 */
async function buscarDados() {
    setTabelaCarregando();
    const termoBusca = inputBusca.value.trim();
    const urlBusca = `${API_URL}?action=buscar&busca=${encodeURIComponent(termoBusca)}`;

    try {
        const response = await fetch(urlBusca);
        if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);

        const dados = await response.json();
        renderizarTabela(dados);

    } catch (error) {
        console.error("Erro na busca de dados:", error);
        showMessage("Erro de Comunicação", `Não foi possível carregar os dados. Detalhes: ${error.message}`);
        tabelaFuncionarios.innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Falha ao buscar dados.</td></tr>`;
    }
}

/**
 * Salva um funcionário (adiciona um novo ou atualiza um existente).
 */
async function salvarFuncionario(dadosFormulario) {
    const id = dadosFormulario.get('id');
    const isEditing = id && id > 0;
    const action = isEditing ? 'atualizar' : 'adicionar';
    const url = `${API_URL}?action=${action}`;

    // Converte FormData para um objeto simples
    const dadosObjeto = Object.fromEntries(dadosFormulario.entries());

    // Feedback visual no botão Salvar
    btnSave.disabled = true;
    btnSave.textContent = 'Salvando...';

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dadosObjeto)
        });

        const resultado = await response.json();

        if (resultado.success) {
            showMessage("Sucesso!", resultado.message);
            toggleFormModal(false); // Fecha o modal
            buscarDados(); // Atualiza a tabela
        } else {
            showMessage("Erro no Servidor", resultado.message || 'Ocorreu um erro desconhecido.');
        }

    } catch (error) {
        console.error("Erro ao salvar:", error);
        showMessage("Erro de Comunicação", `Não foi possível salvar os dados. Detalhes: ${error.message}`);
    } finally {
        // Reativa o botão e restaura o texto, ocorrendo sucesso ou falha
        btnSave.disabled = false;
        btnSave.textContent = 'Salvar';
    }
}

/**
 * Prepara o modal para edição buscando os dados do funcionário.
 */
async function editarFuncionario(id) {
    try {
        const response = await fetch(`${API_URL}?action=visualizar&id=${id}`);
        const dados = await response.json();

        if (dados && dados.id) {
            // Preenche o formulário
            modalFormTitle.textContent = "Editar Funcionário";
            formFields.id.value = dados.id;
            formFields.nome.value = dados.nome;
            formFields.funcao.value = dados.funcao;
            formFields.setor.value = dados.setor;
            formFields.telefone.value = dados.telefone;
            toggleFormModal(true);
        } else {
            showMessage("Erro", dados.message || "Funcionário não encontrado.");
        }
    } catch (error) {
        console.error("Erro ao buscar para editar:", error);
        showMessage("Erro de Comunicação", `Não foi possível carregar os dados para edição. Detalhes: ${error.message}`);
    }
}

/**
 * Exclui um funcionário após confirmação.
 */
async function excluirFuncionario(id) {
    if (!confirm(`Tem certeza que deseja excluir o funcionário ID ${id}? Esta ação não pode ser desfeita.`)) {
        return;
    }

    try {
        const response = await fetch(`${API_URL}?action=excluir`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });

        const resultado = await response.json();

        if (resultado.success) {
            showMessage("Sucesso!", resultado.message);
            buscarDados(); // Atualiza a tabela
        } else {
            showMessage("Erro no Servidor", resultado.message || 'Ocorreu um erro desconhecido.');
        }

    } catch (error) {
        console.error("Erro ao excluir:", error);
        showMessage("Erro de Comunicação", `Não foi possível excluir o funcionário. Detalhes: ${error.message}`);
    }
}

// --- Event Listeners ---

// Busca ao clicar no botão ou pressionar Enter
btnBuscar.addEventListener('click', buscarDados);
inputBusca.addEventListener('keyup', (event) => {
    if (event.key === 'Enter') {
        buscarDados();
    }
});

// Abrir modal para adicionar novo funcionário
btnAdd.addEventListener('click', () => {
    modalFormTitle.textContent = "Adicionar Novo Funcionário";
    formFuncionario.reset(); // Limpa o formulário
    formFields.id.value = ''; // Garante que o ID esteja vazio
    toggleFormModal(true);
});

// Ações do formulário (Salvar)
formFuncionario.addEventListener('submit', (event) => {
    event.preventDefault();
    const formData = new FormData(formFuncionario);
    salvarFuncionario(formData);
});

// Fechar modais
btnCancel.addEventListener('click', () => toggleFormModal(false));
document.querySelector('#modalMessage .modal-close-btn').addEventListener('click', () => {
    modalMessage.classList.add('hidden');
});

// Delegação de eventos na tabela para os botões de editar e excluir
tabelaFuncionarios.addEventListener('click', (event) => {
    const target = event.target;
    if (target.tagName === 'BUTTON' && target.dataset.action) {
        const action = target.dataset.action;
        const id = target.closest('tr').dataset.id;

        if (action === 'edit') {
            editarFuncionario(id);
        } else if (action === 'delete') {
            excluirFuncionario(id);
        }
    }
});

// Carrega os dados iniciais ao carregar a página
document.addEventListener('DOMContentLoaded', buscarDados);