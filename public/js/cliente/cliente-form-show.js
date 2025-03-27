let choicesTipoClienteListagem;
let tabelaClientes;

$(document).ready(function () {
    choicesTipoClienteListagem = new Choices('#tipoClienteListagem', {
        placeholderValue: "Selecione o tipo de cliente",
        searchEnabled: false,
        shouldSort: false
    });

    $("#tipoClienteListagem").on("change", function () {
        let tipo = $(this).val();

        if ($.fn.DataTable.isDataTable("#tabelaClientes")) {
            tabelaClientes.destroy();
            $("#tabelaClientes thead tr").empty();
            $("#tabelaClientes tbody").empty();
        }

        let columns = [];

        if (tipo === "pessoa_fisica") {
            columns = [
                { title: "Nome", data: "nome" }, // ou "Razão Social"
                { title: "CPF/CNPJ", data: "cpf" }, // ou "cnpj"
                { title: "E-mail", data: "email" },
                {
                    title: "Celular",
                    data: "celular",
                    render: function (data) {
                        if (data) {
                            const celularFormatado = data.replace(/\D/g, "");
                            return `
                                <a href="https://wa.me/55${celularFormatado}" target="_blank" 
                                    class="d-flex align-items-center text-success text-decoration-none">
                                    <i class="fab fa-whatsapp fa-lg mr-1"></i> ${data}
                                </a>`;
                        }
                        return "-";
                    }
                },
                {
                    title: "Documentação",
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-sm btn-info btn-documentos"
                                data-id="${row.id}"
                                data-tipo="${row.tipo_cliente}">
                                <i class="fas fa-folder-open"></i> Documentos
                            </button>`;
                    }
                },
                {
                    title: "Ações",
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-sm btn-primary btn-visualizar" data-id="${row.id}" data-tipo="${row.tipo_cliente}">
                                <i class="fas fa-eye"></i> Visualizar
                            </button>
                            <button class="btn btn-sm btn-success btn-editar" data-id="${row.id}" data-tipo="${row.tipo_cliente}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-danger btn-deletar" data-id="${row.id}" data-tipo="${row.tipo_cliente}">
                                <i class="fas fa-trash"></i> Deletar
                            </button>`;
                    }
                }
            ];
            

        } else if (tipo === "pessoa_juridica") {
            columns = [
                { title: "Razão Social", data: "razao_social" },
                { title: "CNPJ", data: "cnpj" },
                { title: "E-mail", data: "email" },
                {
                    title: "Celular",
                    data: "celular",
                    render: function (data, type, row) {
                        if (!data) {
                            return "Não informado";
                        }
                        let numeroFormatado = data.replace(/\D/g, ""); 
                        return `
                            <a href="https://wa.me/55${numeroFormatado}" target="_blank" class="text-success text-decoration-none">
                                <i class="fab fa-whatsapp fa-lg"></i> ${data}
                            </a>`;
                    }
                },
                {
                    title: "Ações",
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-sm btn-primary btn-visualizar"
                                data-id="${row.id}" 
                                data-tipo="${row.tipo_cliente}">
                                <i class="fas fa-eye"></i> Visualizar
                            </button>
                            <button class="btn btn-sm btn-success btn-editar"
                                data-id="${row.id}" 
                                data-tipo="${row.tipo_cliente}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-danger btn-deletar"
                                data-id="${row.id}" 
                                data-tipo="${row.tipo_cliente}">
                                <i class="fas fa-trash"></i> Deletar
                            </button>`;
                    }
                }
            ];
        } else {
            return;
        }

        tabelaClientes = $("#tabelaClientes").DataTable({
            ajax: {
                url: `/clientes/${tipo}`,
                type: "GET",
                data: {
                    escritorio_id: escritorioId
                },
                dataSrc: "data"
            },
            columns: columns,
            language: {
                url: "/lang/datatables/pt-BR.json"
            }
        });
    });

    // Evento para abrir o Swal com os dados ao clicar no botão "visualizar"
    $(document).on("click", ".btn-visualizar", function () {
        let id = $(this).data("id");
        let tipo = $(this).data("tipo");

        $.ajax({
            url: `/clientes/${tipo}`,
            type: "GET",
            data: { escritorio_id: escritorioId },
            success: function (response) {
                let cliente = response.data.find(c => c.id == id);
                if (!cliente) {
                    Swal.fire("Erro!", "Cliente não encontrado.", "error");
                    return;
                }

                // Criar link do WhatsApp se houver número
                function formatarWhatsapp(numero) {
                    if (!numero) return "Não informado";
                    let numeroFormatado = numero.replace(/\D/g, "");
                    return `<a href="https://wa.me/55${numeroFormatado}" target="_blank" class="text-success text-decoration-none">
                                <i class="fab fa-whatsapp fa-lg"></i> ${numero}
                            </a>
                            <br><small class="text-muted">* Clique para falar no WhatsApp</small>`;
                }

                // Criar link do Google Maps
                function formatarEndereco(logradouro, numero, bairro, cidade, estado) {
                    if (!logradouro || !cidade || !estado) return "Não informado";
                    let enderecoFormatado = `${logradouro}, ${numero} - ${bairro}, ${cidade} - ${estado}`;
                    let enderecoUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(enderecoFormatado)}`;
                    return `<a href="${enderecoUrl}" target="_blank" class="text-primary text-decoration-none">
                                <i class="fas fa-map-marker-alt"></i> ${enderecoFormatado}
                            </a>
                            <br><small class="text-muted">* Clique para ver no Google Maps</small>`;
                }

                let dadosCliente = (tipo === "pessoa_fisica") ? `
                    <p><strong>Nome:</strong> ${cliente.nome}</p>
                    <p><strong>CPF:</strong> ${cliente.cpf}</p>
                    <p><strong>E-mail:</strong> ${cliente.email}</p>
                    <p><strong>Telefone:</strong> ${cliente.telefone || "Não informado"}</p>
                    <p><strong>Celular:</strong> ${formatarWhatsapp(cliente.celular)}</p>
                    <p><strong>CEP:</strong> ${cliente.cep || "Não informado"}</p>
                    <p><strong>Endereço:</strong> 
                       ${formatarEndereco(cliente.logradouro, cliente.numero, cliente.bairro, cliente.cidade, cliente.estado)}
                    </p>
                ` : `
                    <p><strong>Razão Social:</strong> ${cliente.razao_social}</p>
                    <p><strong>Nome Fantasia:</strong> ${cliente.nome_fantasia || "Não informado"}</p>
                    <p><strong>CNPJ:</strong> ${cliente.cnpj}</p>
                    <p><strong>E-mail:</strong> ${cliente.email}</p>
                    <p><strong>Telefone:</strong> ${cliente.telefone || "Não informado"}</p>
                    <p><strong>Celular:</strong> ${formatarWhatsapp(cliente.celular)}</p>
                    <p><strong>CEP:</strong> ${cliente.cep || "Não informado"}</p>
                    <p><strong>Endereço:</strong> 
                       ${formatarEndereco(cliente.logradouro, cliente.numero, cliente.bairro, cliente.cidade, cliente.estado)}
                    </p>
                `;

                Swal.fire({
                    title: "Detalhes do Cliente",
                    html: dadosCliente,
                    icon: "info",
                    showConfirmButton: true,
                    confirmButtonText: `<i class="fas fa-times"></i> Fechar`,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-secondary"
                    }
                });
            },
            error: function () {
                Swal.fire("Erro!", "Não foi possível obter os dados do cliente.", "error");
            }
        });
    });

    choicesTipoClienteListagem = new Choices("#tipoClienteListagem", {
        placeholderValue: "Selecione o tipo de cliente",
        searchEnabled: false,
        shouldSort: false
    });
});
