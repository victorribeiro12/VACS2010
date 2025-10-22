// DOM element references
const tabelaFuncionarios = document.getElementById('tabelaFuncionarios');

/**
 * Função de segurança simples para prevenir XSS.
 */
function sanitize(str) {
    if (!str) return 'N/A';
    return str.toString()
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;');
}

/**
 * Monta as linhas da tabela no HTML com os dados.
 */
function renderizarTabela(funcionarios) {
    let html = '';

    if (!funcionarios || funcionarios.length === 0) {
        html = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum funcionário encontrado.</td></tr>';
    } else {
        funcionarios.forEach(func => {
            html += `
                <tr>
                    <td>${sanitize(func.nome)}</td>
                    <td>${sanitize(func.email)}</td>
                    <td>${sanitize(func.telefone)}</td>
                    <td>${sanitize(func.setor)}</td>
                </tr>
            `;
        });
    }
    tabelaFuncionarios.innerHTML = html;
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

function popularTabela() {
    const funcionariosAleatorios = gerarFuncionariosAleatorios(5);
    renderizarTabela(funcionariosAleatorios);
}

// Inicia a renderização quando a página é carregada
window.onload = popularTabela;

tabelaFuncionarios.insertAdjacentHTML('beforebegin', `
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Setor</th>
        </tr>
    </thead>
`);

const styleSheet = document.createElement("style")
styleSheet.innerText = `
  #tabelaFuncionarios > thead > tr > th {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }
  
  #tabelaFuncionarios > tbody > tr > td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }
`
document.head.appendChild(styleSheet)