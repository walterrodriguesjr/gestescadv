let tabelaClientes; // Variável para a instância do DataTables


function listarClientes() {
    const spinner = $("#listarSpinner");

    // Exibe o spinner
    spinner.removeClass("d-none");

    // Marca o momento em que o spinner foi exibido
    const startTime = Date.now();

    if (!$.fn.DataTable.isDataTable("#tabelaClientes")) {
        // Inicializar DataTables
        tabelaClientes = $("#tabelaClientes").DataTable({
            ajax: {
                url: "/cliente",
                type: "GET",
                dataType: "json",
                dataSrc: "", // Array de dados simples
                complete: function () {
                    // Garante que o spinner fique visível pelo menos 2 segundos
                    const elapsed = Date.now() - startTime;
                    const delay = Math.max(0, 1000 - elapsed);

                    setTimeout(() => {
                        spinner.addClass("d-none");
                    }, delay);
                },
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
                            <button class="btn btn-success btn-sm" id="abrirModalEditarCliente" title="Editar Cliente" data-id="${row.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" id="abrirModalDeletarCliente" title="Excluir Cliente" data-id="${row.id}">
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
                const searchContainer = $("#tabelaClientes_filter");
                const searchInput = searchContainer.find("input");

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

        // Remove o spinner ao final do carregamento
        tabelaClientes.on("xhr", function () {
            const elapsed = Date.now() - startTime;
            const delay = Math.max(0, 1000 - elapsed);

            setTimeout(() => {
                spinner.addClass("d-none");
            }, delay);
        });
    } else {
        // Recarregar os dados se a tabela já estiver inicializada
        tabelaClientes.ajax.reload(null, false);

        // Remove o spinner ao final do carregamento
        tabelaClientes.on("xhr", function () {
            const elapsed = Date.now() - startTime;
            const delay = Math.max(0, 1000 - elapsed);

            setTimeout(() => {
                spinner.addClass("d-none");
            }, delay);
        });
    }
}


// Disponibiliza a função no escopo global para reutilização
window.listarClientes = listarClientes;

// Inicializa o DataTables
listarClientes();

