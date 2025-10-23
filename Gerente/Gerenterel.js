document.addEventListener('DOMContentLoaded', function () {

    // --- LÓGICA PARA ADICIONAR RELATÓRIO MANUALMENTE ---

    const btnGerarNovo = document.querySelector('.report-controls .action-button.primary');
    const tabelaBody = document.getElementById('tabelaRelatorios');

    btnGerarNovo.addEventListener('click', function () {
        // 1. Pede os dados ao usuário usando prompts
        const nomeRelatorio = prompt("Digite o nome do novo relatório:");
        if (!nomeRelatorio) return; // Cancela se o usuário não digitar nada

        const tipoRelatorio = prompt("Digite o tipo do relatório (Ex: Financeiro, Operacional):");
        if (!tipoRelatorio) return; // Cancela se o usuário não digitar nada

        // 2. Prepara os dados para a nova linha
        const dataAtual = new Date().toLocaleDateString('pt-BR'); // Formato dd/mm/yyyy
        const geradoPor = "Gerente"; // Valor fixo

        // 3. Cria a nova linha (tr) da tabela
        const novaLinha = document.createElement('tr');

        // 4. Adiciona as células (td) com os dados coletados
        novaLinha.innerHTML = `
            <td data-label="Nome do Relatório">${nomeRelatorio}</td>
            <td data-label="Data de Geração">${dataAtual}</td>
            <td data-label="Tipo">${tipoRelatorio}</td>
            <td data-label="Gerado Por">${geradoPor}</td>
            <td data-label="Ação"><button class="edit-button">Ver</button></td>
        `;

        // 5. Adiciona a nova linha no início do corpo da tabela
        tabelaBody.prepend(novaLinha);
    });


    // --- LÓGICA PARA GERAR O GRÁFICO DE CONSULTAS ---

    const ctx = document.getElementById('consultasChart').getContext('2d');

    // Dados de exemplo para o gráfico
    const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out'];
    const quantidadeConsultas = [65, 59, 80, 81, 56, 55, 40, 78, 90, 102]; // Dados de exemplo

    const consultasChart = new Chart(ctx, {
        type: 'bar', // Tipo de gráfico: 'bar' para colunas
        data: {
            labels: meses,
            datasets: [{
                label: 'Nº de Consultas',
                data: quantidadeConsultas,
                backgroundColor: 'rgba(157, 217, 235, 0.6)', // Cor das barras (azul do seu tema)
                borderColor: 'rgba(157, 217, 235, 1)', // Cor da borda das barras
                borderWidth: 1,
                borderRadius: 5 // Deixa as pontas das barras arredondadas
            }]
        },
        options: {
            responsive: true, // O gráfico se ajusta ao tamanho do container
            maintainAspectRatio: false, // Permite que o gráfico preencha o card
            scales: {
                y: {
                    beginAtZero: true, // Eixo Y começa no zero
                    grid: {
                        color: '#f0f0f0' // Cor das linhas de grade mais suave
                    }
                },
                x: {
                    grid: {
                        display: false // Remove as linhas de grade do eixo X
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // Oculta a legenda "Nº de Consultas" no topo
                },
                tooltip: {
                    backgroundColor: '#333', // Fundo da dica ao passar o mouse
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            }
        }
    });
});