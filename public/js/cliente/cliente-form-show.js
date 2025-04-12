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
                { title: "Nome", data: "nome" },
                {
                    title: "CPF",
                    data: "cpf",
                    render: function (data) {
                        if (!data) return "-";
                        const cpfLimpo = data.replace(/\D/g, "");
                        return cpfLimpo.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
                    }
                },
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
                {
                    title: "CNPJ",
                    data: "cnpj",
                    render: function (data) {
                        if (!data) return "-";
                        const cnpjLimpo = data.replace(/\D/g, "");
                        return cnpjLimpo.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, "$1.$2.$3/$4-$5");
                    }
                },
                {
                    title: "Celular",
                    data: "celular",
                    render: function (data) {
                        if (!data) return "Não informado";
                        let numeroFormatado = data.replace(/\D/g, "");
                        return `
                            <a href="https://wa.me/55${numeroFormatado}" target="_blank" class="text-success text-decoration-none">
                                <i class="fab fa-whatsapp fa-lg"></i> ${data}
                            </a>`;
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
        } else {
            return;
        }

        tabelaClientes = $("#tabelaClientes").DataTable({
            ajax: {
                url: `/clientes/${tipo}`,
                type: "GET",
                data: { escritorio_id: escritorioId },
                dataSrc: "data"
            },
            columns: columns,
            language: { url: "/lang/datatables/pt-BR.json" }
        });
    });

    // Visualização dos detalhes do cliente (mantido igual ao seu código original)
    $(document).on("click", ".btn-visualizar", function () {
        const id = $(this).data("id");
        const tipo = $(this).data("tipo");

        $.ajax({
            url: `/clientes/${tipo}`,
            type: "GET",
            data: { escritorio_id: escritorioId },
            success: function (response) {
                const cliente = response.data.find(c => c.id == id);
                if (!cliente) {
                    Swal.fire("Erro!", "Cliente não encontrado.", "error");
                    return;
                }

                const aplicarMascaraCPF = cpf => cpf?.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4") || "Não informado";
                const aplicarMascaraCNPJ = cnpj => cnpj?.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5") || "Não informado";
                const aplicarMascaraCelular = cel => cel?.replace(/(\d{2})(\d{5})(\d{4})/, "($1) $2-$3") || "Não informado";
                const aplicarMascaraCEP = cep => cep?.replace(/(\d{5})(\d{3})/, "$1-$2") || "Não informado";

                const formatarWhatsapp = numero => {
                    if (!numero) return "Não informado";
                    const numeroLimpo = numero.replace(/\D/g, "");
                    const numeroFormatado = aplicarMascaraCelular(numeroLimpo);
                    return `<a href="https://wa.me/55${numeroLimpo}" target="_blank" class="text-success text-decoration-none">
                                <i class="fab fa-whatsapp fa-lg"></i> ${numeroFormatado}
                            </a>
                            <br><small class="text-muted">* Clique para falar no WhatsApp</small>`;
                };

                const formatarEndereco = (logradouro, numero, bairro, cidade, estado) => {
                    if (!logradouro || !cidade || !estado) return "Não informado";
                    const enderecoFormatado = `${logradouro}, ${numero} - ${bairro}, ${cidade} - ${estado}`;
                    const enderecoUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(enderecoFormatado)}`;
                    return `<a href="${enderecoUrl}" target="_blank" class="text-primary text-decoration-none">
                                <i class="fas fa-map-marker-alt"></i> ${enderecoFormatado}
                            </a>
                            <br><small class="text-muted">* Clique para ver no Google Maps</small>`;
                };

                let html = "";

                if (tipo === "pessoa_fisica") {
                    html = `
                        <p><strong>Nome:</strong> ${cliente.nome || "Não informado"}</p>
                        <p><strong>CPF:</strong> ${aplicarMascaraCPF(cliente.cpf)}</p>
                        <p><strong>E-mail:</strong> ${cliente.email || "Não informado"}</p>
                        <p><strong>Celular:</strong> ${formatarWhatsapp(cliente.celular)}</p>
                        <p><strong>Telefone:</strong> ${cliente.telefone || "Não informado"}</p>
                        <p><strong>Endereço:</strong> ${formatarEndereco(cliente.logradouro, cliente.numero, cliente.bairro, cliente.cidade, cliente.estado)}</p>
                        <p><strong>CEP:</strong> ${aplicarMascaraCEP(cliente.cep)}</p>
                    `;
                } else {
                    html = `
                        <p><strong>Razão Social:</strong> ${cliente.razao_social || "Não informado"}</p>
                        <p><strong>Nome Fantasia:</strong> ${cliente.nome_fantasia || "Não informado"}</p>
                        <p><strong>CNPJ:</strong> ${aplicarMascaraCNPJ(cliente.cnpj)}</p>
                        <p><strong>E-mail:</strong> ${cliente.email || "Não informado"}</p>
                        <p><strong>Celular:</strong> ${formatarWhatsapp(cliente.celular)}</p>
                        <p><strong>Telefone:</strong> ${cliente.telefone || "Não informado"}</p>
                        <p><strong>Endereço:</strong> ${formatarEndereco(cliente.logradouro, cliente.numero, cliente.bairro, cliente.cidade, cliente.estado)}</p>
                        <p><strong>CEP:</strong> ${aplicarMascaraCEP(cliente.cep)}</p>
                    `;
                }

                Swal.fire({
                    title: "Detalhes do Cliente",
                    html: html,
                    icon: "info",
                    confirmButtonText: `<i class="fas fa-times"></i> Fechar`,
                    buttonsStyling: false,
                    customClass: { confirmButton: "btn btn-secondary" }
                });
            }
        });
    });


});
