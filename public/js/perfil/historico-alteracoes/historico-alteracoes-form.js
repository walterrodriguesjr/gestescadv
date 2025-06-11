function atualizarHistoricoAlteracoes() {
    $.ajax({
        url: "/perfil/historico",
        type: "GET",
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        success: function (data) {
            let tableContent = '';

            // Verifica se há dados
            if (data.length > 0) {
                data.forEach(log => {
                    tableContent += `<tr>
                        <td>${log.campo}</td>
                        <td>${log.valor_anterior}</td>
                        <td>${log.valor_novo}</td>
                        <td>${new Date(log.alterado_em).toLocaleString()}</td>
                    </tr>`;
                });
            } else {
                // Adiciona uma linha vazia com colunas preenchidas corretamente
                tableContent = '<tr><td colspan="4" class="text-center">Nenhuma alteração registrada.</td></tr>';
            }

            // **Se a tabela já existe, destrua antes de recriar**
            if ($.fn.DataTable.isDataTable("#tabelaHistorico")) {
                $("#tabelaHistorico").DataTable().clear().destroy();
            }

            // **Atualiza o conteúdo da tabela**
            $("#historicoAlteracoes").html(tableContent);

            // **Reinicializa a DataTable garantindo que as colunas existem**
            $("#tabelaHistorico").DataTable({
                responsive: true,
                ordering: true,
                paging: true,
                searching: true,
                info: true,
                order: [[3, "desc"]], // Ordena pela coluna da data (4ª coluna)
                language: {
                    url: "/lang/datatables/pt-BR.json"
                },
                columnDefs: [
                    { targets: [0, 1, 2, 3], defaultContent: "-" } // Evita erro quando não há dados
                ]
            });

        },
        error: function () {
            toastr.error("Erro ao carregar histórico de alterações.");
        }
    });
}

// **Chamada inicial da função quando a página carrega**
$(document).ready(function () {
    atualizarHistoricoAlteracoes();
});
