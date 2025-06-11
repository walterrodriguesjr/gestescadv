window.abrirArquivosSwal = function (servicoId, clienteId, tipoCliente, andamentoId) {
    Swal.fire({
        title: '📁 Gerenciar Arquivos do Andamento',
        html: `
            <div id="dropzone-andamento" class="border rounded p-4" style="cursor:pointer;">
                <div class="dz-message">
                    <i class="fas fa-cloud-upload-alt fa-3x text-secondary mb-3"></i>
                    <h5>Arraste arquivos aqui ou clique para selecionar</h5>
                    <small class="text-muted">(JPG, PNG, PDF. Máximo 5MB)</small>
                </div>
            </div>
            <hr class="my-3">
            <h5>📌 Arquivos já anexados:</h5>
            <ul class="list-group text-start mt-2" id="listaArquivosAndamento"></ul>
        `,
        width: "1000px",
        showConfirmButton: false,
        showCloseButton: true,
        didOpen: () => {
            carregarArquivos(servicoId, clienteId, andamentoId);

            const myDropzone = new Dropzone("#dropzone-andamento", {
                url: `/andamentos/${servicoId}/${andamentoId}/${clienteId}/arquivos`,
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                acceptedFiles: ".pdf,.jpg,.jpeg,.png",
                maxFilesize: 5, // MB
                clickable: true,
                autoProcessQueue: false,
                init: function () {
                    this.on("addedfile", file => {
                        Swal.fire({
                            title: "Nomear Arquivo",
                            input: "text",
                            inputLabel: "Digite um nome amigável para o arquivo:",
                            inputValue: file.name.split('.').slice(0, -1).join('.'),
                            showCancelButton: true,
                            confirmButtonText: '<i class="fas fa-check"></i> Salvar',
                            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                            reverseButtons: true,
                            buttonsStyling: false,
                            focusConfirm: false,
                            customClass: {
                                confirmButton: 'btn btn-primary ml-2',
                                cancelButton: 'btn btn-secondary'
                            },
                            preConfirm: (nomeDigitado) => {
                                if (!nomeDigitado) {
                                    Swal.showValidationMessage("O nome é obrigatório.");
                                    return false;
                                }
                                file.customName = nomeDigitado;
                            }
                        }).then(result => {
                            if (!result.isConfirmed) {
                                this.removeFile(file);
                            } else {
                                const tempoMinimo = 1500;
                                const inicio = Date.now();

                                Swal.fire({
                                    title: "Enviando...",
                                    text: "Aguarde enquanto o arquivo é salvo.",
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => Swal.showLoading()
                                });

                                this.processFile(file);

                                this.on("success", function () {
                                    const tempo = Date.now() - inicio;
                                    const atraso = tempoMinimo - tempo;

                                    setTimeout(() => {
                                        Swal.fire({
                                            icon: "success",
                                            title: "Sucesso!",
                                            text: "Arquivo salvo com sucesso."
                                        });
                                        carregarArquivos(servicoId, clienteId, andamentoId);
                                    }, atraso > 0 ? atraso : 0);
                                });

                                this.on("error", function () {
                                    Swal.close();
                                    Swal.fire("❌ Erro", "Erro ao anexar o arquivo. Verifique o formato e tente novamente.", "error");
                                });
                            }
                        });
                    });

                    this.on("sending", function (file, xhr, formData) {
                        formData.append("nome_original", file.customName || file.name);
                    });
                }
            });

        }
    });
};

function carregarArquivos(servicoId, clienteId, andamentoId) {
    let lista = $("#listaArquivosAndamento");
    lista.html(`
        <li class="list-group-item">
            <i class="fas fa-spinner fa-spin"></i> Carregando arquivos...
        </li>
    `);

    $.get(`/andamentos/${servicoId}/${andamentoId}/${clienteId}/arquivos`)
        .done(res => {
            lista.empty();
            if (res.arquivos.length === 0) {
                lista.html('<li class="list-group-item text-muted">Nenhum arquivo encontrado.</li>');
                return;
            }

            res.arquivos.forEach(arquivo => {
                lista.append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-file-alt text-secondary me-2"></i> ${arquivo.nome_original}
                        </div>
                        <div>
                            <a href="${arquivo.url}" target="_blank" class="btn btn-sm btn-primary me-1">
                                <i class="fas fa-eye"></i> Visualizar
                            </a>
                            <button class="btn btn-sm btn-success btn-editar-arquivo me-1"
                                    data-nome="${arquivo.nome_original}"
                                    data-servico="${servicoId}"
                                    data-andamento="${andamentoId}"
                                    data-cliente="${clienteId}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-danger btn-deletar-arquivo"
                                    data-nome="${arquivo.nome_original}"
                                    data-servico="${servicoId}"
                                    data-andamento="${andamentoId}"
                                    data-cliente="${clienteId}">
                                <i class="fas fa-trash-alt"></i> Deletar
                            </button>
                        </div>
                    </li>
                `);
            });
        })
        .fail(() => {
            lista.html('<li class="list-group-item text-danger">❌ Erro ao carregar arquivos.</li>');
        });
}

//Editar
$(document).on("click", ".btn-editar-arquivo", function () {

    const nomeAtual = $(this).data("nome");
    const servicoId = $(this).data("servico");
    const andamentoId = $(this).data("andamento");
    const clienteId = $(this).data("cliente");

    // remove a extensão para não deixar o usuário alterá‑la
    const nomeSemExt = nomeAtual.split('.').slice(0, -1).join('.');

    Swal.fire({
        title: "Editar Nome do Arquivo",
        input: "text",
        inputLabel: "Digite o novo nome:",
        inputValue: nomeSemExt,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Atualizar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        buttonsStyling: false,
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-success ml-2',
            cancelButton: 'btn btn-secondary'
        },
        preConfirm: (novoNome) => {
            if (!novoNome) {
                Swal.showValidationMessage("O nome é obrigatório!");
                return false;
            }
            return novoNome;        // devolve o nome para o then(...)
        }
    }).then(result => {
        if (!result.isConfirmed) return;

        const novoNome = result.value.trim();
        const inicio = Date.now();
        const minDelay = 1500;

        Swal.fire({
            title: "Atualizando...",
            text: "Aguarde enquanto o nome é alterado.",
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: "/arquivos/editar-nome",
            type: "PUT",                    // usamos PUT diretamente
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            data: {
                servico_id: servicoId,
                andamento_id: andamentoId,
                cliente_id: clienteId,
                nome_atual: nomeAtual,     // inclui a extensão
                nome_original: novoNome       // sem extensão (o back‑end mantém a ext.)
            },
            success: () => {
                const diff = Date.now() - inicio;
                setTimeout(() => {
                    Swal.fire({
                        icon: "success",
                        title: "Sucesso!",
                        text: "Nome do arquivo atualizado."
                    }).then(() => {
                        // recarrega a lista
                        carregarArquivos(servicoId, clienteId, andamentoId);
                    });
                }, diff < minDelay ? minDelay - diff : 0);
            },
            error: () => {
                Swal.fire("❌ Erro", "Falha ao atualizar o nome.", "error");
            }
        });
    });
});

// Excluir
$(document).on("click", ".btn-deletar-arquivo", function () {
    const nome = $(this).data("nome");
    const servicoId = $(this).data("servico");
    const andamentoId = $(this).data("andamento");
    const clienteId = $(this).data("cliente");

    Swal.fire({
        title: "⚠️ Tem certeza?",
        text: "Esta ação é irreversível e apagará permanentemente o arquivo!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash"></i> Deletar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        reverseButtons: true,
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-danger ml-2',
            cancelButton: 'btn btn-secondary'
        }
    }).then(result => {
        if (result.isConfirmed) {
            const tempoMinimo = 1500;
            const inicio = Date.now();

            Swal.fire({
                title: "Excluindo...",
                text: "Aguarde enquanto o arquivo é excluído.",
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `/andamentos/arquivos/${servicoId}/${andamentoId}/${clienteId}/deletar`,
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    _method: 'DELETE',
                    arquivo: nome
                },
                success: () => {
                    const tempo = Date.now() - inicio;
                    const atraso = tempoMinimo - tempo;

                    setTimeout(() => {
                        Swal.fire({
                            icon: "success",
                            title: "Sucesso!",
                            text: "Arquivo excluído com sucesso.",
                        }).then(() => {
                            carregarArquivos(servicoId, clienteId, andamentoId);
                        });
                    }, atraso > 0 ? atraso : 0);
                },
                error: () => {
                    Swal.fire("❌ Erro", "Falha ao excluir o arquivo.", "error");
                }
            });
        }
    });
});





