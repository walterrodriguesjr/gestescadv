$(document).ready(function () {
    // Evento de digitação no campo de pesquisa
    $("#pesquisarCliente").on("keyup", function () {
        const valorPesquisa = $(this).val(); // Obtém o valor digitado

        // Verifica se a tabela está inicializada
        if ($.fn.DataTable.isDataTable("#tabelaClientes")) {
            // Aplica o filtro global no DataTables
            $("#tabelaClientes").DataTable().search(valorPesquisa).draw();
        }
    });
});
