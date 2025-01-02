$(document).ready(function () {
    $("#limparCliente").click(function (e) { 
        e.preventDefault();
        $("#pesquisarCliente").val('');
        
        // Reseta a busca no DataTables
        if ($.fn.DataTable.isDataTable("#tabelaClientes")) {
            $("#tabelaClientes").DataTable().search('').draw(); // Limpa a pesquisa e redesenha
        }
    });
});