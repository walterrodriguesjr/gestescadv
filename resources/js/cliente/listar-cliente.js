let tabelaClientes; // Variável para a instância do DataTables


function listarClientes() {
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
                            <button class="btn btn-info btn-sm" id="abrirModalVisualizarCliente" title="Visualizar Cliente" data-id="${row.id}">
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
            dom: `<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6">>
                  <"table-responsive"tr>
                  <"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>`,
            initComplete: function () {
                // Capturar e mover a barra de pesquisa
                const searchContainer = $("#tabelaClientes_filter"); // Captura o container original
                const searchInput = searchContainer.find("input"); // Captura o input original

                // Personalizar o input
                searchInput.addClass("form-control").attr({
                    id: "pesquisarCliente",
                    placeholder: "Pesquise por nome ou cpf",
                });

                // Adicionar ícone de pesquisa ao lado
                const customSearchGroup = $(`
                    <div class="input-group">
                        ${searchInput[0].outerHTML}
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                `);

                // Substituir o campo de pesquisa customizado
                $(".input-group").replaceWith(customSearchGroup);

                // Remover o container padrão do DataTables
                searchContainer.remove();
            },
        });
    } else {
        // Recarregar os dados se a tabela já estiver inicializada
        tabelaClientes.ajax.reload();
    }
}

// Disponibiliza a função no escopo global para reutilização
window.listarClientes = listarClientes;

// Inicializa o DataTables
listarClientes();

