$(document).on("click", ".btn-documentos", async function () {
    const clienteId = $(this).data("id");
    const tipoCliente = $(this).data("tipo");

    Swal.fire({
        title: '📁 Gerenciar Documentos do Cliente',
        html: `
            <div id="dropzone-area" class="border rounded p-4" style="cursor:pointer;">
                <div class="dz-message">
                    <i class="fas fa-cloud-upload-alt fa-3x text-secondary mb-3"></i>
                    <h5>Arraste arquivos aqui ou clique para selecionar</h5>
                    <small class="text-muted">(Arquivos aceitos: JPG, PNG, PDF. Máximo 5MB)</small>
                </div>
            </div>
            <hr class="my-3">
            <h5>📌 Documentos já anexados:</h5>
            <ul class="list-group text-start mt-2" id="listaDocumentosCliente"></ul>
        `,
        showCloseButton: true,
        showConfirmButton: false,
        width: "700px",
        didOpen: () => {
            carregarDocumentos(clienteId, tipoCliente);

            const myDropzone = new Dropzone("#dropzone-area", {
                url: `/clientes/${tipoCliente}/${clienteId}/documentos`,
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                acceptedFiles: ".pdf,.jpg,.jpeg,.png",
                maxFilesize: 5, // MB
                clickable: true,
                dictDefaultMessage: "Arraste arquivos aqui ou clique para anexar",
                autoProcessQueue: false, // Importante!
                init: function () {
                    this.on("addedfile", file => {
                        Swal.fire({
                            title: "Nomear arquivo",
                            input: "text",
                            inputLabel: "Digite um nome amigável para o documento:",
                            inputValue: file.name.split('.').slice(0, -1).join('.'),
                            showCancelButton: true,
                            confirmButtonText: "Salvar",
                            cancelButtonText: "Cancelar",
                            preConfirm: (nomeDigitado) => {
                                if (!nomeDigitado) {
                                    Swal.showValidationMessage("Nome é obrigatório.");
                                    return false;
                                }
                                file.customName = nomeDigitado;
                            }
                        }).then(result => {
                            if (!result.isConfirmed) {
                                this.removeFile(file);
                            } else {
                                this.processFile(file);
                            }
                        });
                    });

                    this.on("sending", function (file, xhr, formData) {
                        formData.append("nome_original", file.customName || file.name);
                    });

                    this.on("success", function (file, response) {
                        Swal.fire("✅ Sucesso", "Documento salvo com sucesso!", "success");
                        carregarDocumentos(clienteId, tipoCliente);
                    });

                    this.on("error", function (file, response) {
                        Swal.fire("❌ Erro", "Erro ao anexar documento. Verifique o arquivo e tente novamente.", "error");
                    });
                }
            });
        }
    });

    // Função para carregar documentos já anexados
    async function carregarDocumentos(clienteId, tipoCliente) {
        let lista = $("#listaDocumentosCliente");
        lista.html(`
            <li class="list-group-item d-flex align-items-center">
                <i class="fas fa-spinner fa-spin me-2"></i> Carregando documentos...
            </li>`);

        $.get(`/clientes/${tipoCliente}/${clienteId}/documentos`)
            .then(response => {
                lista.empty();
                if (response.documentos.length === 0) {
                    lista.html('<li class="list-group-item text-muted">Nenhum documento anexado.</li>');
                    return;
                }

                response.documentos.forEach(doc => {
                    lista.append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-alt text-secondary me-2"></i> ${doc.nome_original}
                            </div>
                            <div>
                                <a href="${doc.url}" target="_blank" class="btn btn-sm btn-outline-info me-1">
                                    <i class="fas fa-eye"></i> Visualizar
                                </a>
                                <button class="btn btn-sm btn-outline-warning btn-editar-documento me-1"
                                        data-id="${doc.id}" 
                                        data-tipo="${tipoCliente}" 
                                        data-nome="${doc.nome_original}">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-deletar-documento"
                                        data-id="${doc.id}" 
                                        data-tipo="${tipoCliente}">
                                    <i class="fas fa-trash"></i> Deletar
                                </button>
                            </div>
                        </li>
                    `);
                });
            })
            .catch(() => {
                lista.html('<li class="list-group-item text-danger">❌ Erro ao carregar documentos.</li>');
            });
    }
});

// Editar documento com nome atual pré-carregado
$(document).on("click", ".btn-editar-documento", function () {
    const docId = $(this).data("id");
    const tipoCliente = $(this).data("tipo");
    const nomeAtual = $(this).data("nome");

    Swal.fire({
        title: "Editar Nome do Documento",
        input: "text",
        inputLabel: "Digite o novo nome:",
        inputValue: nomeAtual || "",  // Aqui garante o valor atual no input
        showCancelButton: true,
        confirmButtonText: "Salvar",
        cancelButtonText: "Cancelar",
        preConfirm: (novoNome) => {
            if (!novoNome) {
                Swal.showValidationMessage("O nome é obrigatório!");
                return false;
            }
        }
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/clientes/${tipoCliente}/documentos/${docId}`,
                method: "PUT",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                data: { nome_original: result.value },
                success: () => {
                    Swal.fire("✅ Sucesso", "Nome do documento atualizado!", "success");
                    $(`.btn-documentos[data-id="${docId}"][data-tipo="${tipoCliente}"]`).click();
                },
                error: () => {
                    Swal.fire("❌ Erro", "Falha ao atualizar o nome.", "error");
                }
            });
        }
    });
});

// Deletar documento
$(document).on("click", ".btn-deletar-documento", function () {
    const docId = $(this).data("id");
    const tipoCliente = $(this).data("tipo");

    Swal.fire({
        title: "⚠️ Tem certeza?",
        text: "Esta ação é irreversível e apagará permanentemente o documento!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: '<i class="fas fa-trash"></i> Deletar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/clientes/${tipoCliente}/documentos/${docId}`,
                method: "DELETE",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: () => {
                    Swal.fire("✅ Sucesso", "Documento excluído com sucesso!", "success");
                    $(`.btn-documentos[data-id="${docId}"][data-tipo="${tipoCliente}"]`).click();
                },
                error: () => {
                    Swal.fire("❌ Erro", "Falha ao excluir documento.", "error");
                }
            });
        }
    });
});
