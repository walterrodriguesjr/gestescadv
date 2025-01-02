let tabelaClientes; // Variável para a instância do DataTables


function listarClientes(){
    if (!$.fn.DataTable.isDataTable("#tabelaClientes")) {
        // Inicializar DataTables
        tabelaClientes = $("#tabelaClientes").DataTable({
            ajax: {
                url: "/cliente",
                type: "GET",
                dataType: "json",
                dataSrc: "", // Array de dados simples
            },
            columns: [
                { data: "cliente_nome_completo", title: "Nome Completo" },
                { data: "cliente_cpf", title: "CPF" },
                {
                    data: null,
                    title: "Ações",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-info btn-sm" title="Visualizar Cliente">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-success btn-sm" title="Editar Cliente">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" title="Excluir Cliente">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    },
                },
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json",
            },
            responsive: true,
            searching: false,
            dom: `<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>
                  <"table-responsive"tr>
                  <"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>`,
        });
    } else {
        // Recarregar os dados se a tabela já estiver inicializada
        tabelaClientes.ajax.reload();
    }
}

// Vincular a função ao escopo global
window.listarClientes = listarClientes;

/* chama a funcao que lista os clientes */
listarClientes();
