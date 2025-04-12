$(document).ready(function () {
    if (!membroEscritorioShowUrl) {
        console.warn("⚠️ Nenhuma URL definida para buscar membros.");
        return;
    }

    // Inicializa o DataTable
    $('#membrosEscritorioTable').DataTable({
        processing: true,
        serverSide: false,
        autoWidth: false,
        responsive: true,
        destroy: true, // Evita erro de reinitialização
        ajax: {
            url: membroEscritorioShowUrl,
            type: "GET",
            headers: { "X-CSRF-TOKEN": csrfToken },
            dataSrc: function (json) {
                if (!json.success) {
                    console.error("❌ Erro na resposta do servidor:", json.message);
                    Swal.fire("Erro!", json.message, "error");
                    return [];
                }
                return json.data;
            },
            error: function (xhr) {
                console.error("❌ Erro na requisição:", xhr.responseText);
                Swal.fire("Erro!", "Não foi possível carregar os membros.", "error");
            }
        },
        columns: [
            { data: 'nome', name: 'nome' },
            { data: 'email', name: 'email' },
            { data: 'nivel_acesso', name: 'nivel_acesso' },
            {
                data: 'status',
                name: 'status',
                render: function (data, type, row) {
                    if (data === "ativo") {
                        return '<span class="badge badge-success">Ativo</span>';
                    } else if (data === "pendente") {
                        // Se estiver pendente, verifica se o token expirou
                        return row.token_expirado
                            ? '<span class="badge badge-warning">Pendente, com token expirado</span>'
                            : '<span class="badge badge-warning">Pendente</span>';
                    } else {
                        // status === inativo
                        return '<span class="badge badge-secondary">Inativo</span>';
                    }
                }
            },
            {
                data: 'id',
                name: 'acoes',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    // Botão VISUALIZAR (passando TODOS os dados)
                    const btnVisualizar = `
                        <button class="btn btn-primary btn-sm"
                            onclick="visualizarMembro(
                                ${data},
                                '${row.nome}',
                                '${row.email}',
                                '${row.nivel_acesso}',
                                '${row.status}',
                                '${row.cpf}',
                                '${row.telefone}',
                                '${row.celular}',
                                '${row.cidade}',
                                '${row.estado}',
                                '${row.oab}',
                                '${row.estado_oab}',
                                '${row.data_nascimento}',
                                '${row.foto}'
                            )">
                            <i class="fas fa-eye"></i> Visualizar
                        </button>
                    `;

                    // Botão EDITAR (aparece se status for Ativo, Pendente ou Pendente com token expirado)
                    let btnEditar = "";
                    if (row.status === "ativo" || row.status === "pendente" || row.token_expirado) {
                        btnEditar = `
                            <button class="btn btn-success btn-sm"
                                onclick="abrirEdicaoMembro(
                                    ${data},
                                    '${row.nome}',
                                    '${row.email}',
                                    '${row.nivel_acesso}',
                                    '${row.cpf}'
                                )">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        `;
                    }

                    // Botão REATIVAR (só aparece para inativos)
                    let btnReativar = "";
                    if (row.status === "inativo") {
                        btnReativar = `
                            <button class="btn btn-success btn-sm" onclick="reativarMembro(${data})">
                                <i class="fas fa-user-check"></i> Reativar
                            </button>
                        `;
                    }

                    // Botão SUSPENDER (só aparece se NÃO estiver inativo e o token NÃO estiver expirado)
                    let btnSuspender = "";
                    if (row.status !== "inativo" && !row.token_expirado) {
                        btnSuspender = `
                            <button class="btn btn-danger btn-sm" onclick="suspenderMembro(${data})">
                                <i class="fas fa-user-slash"></i> Suspender
                            </button>
                        `;
                    }

                    // Botão REENVIAR CONVITE (aparece quando o token está expirado)
                    let btnReenviarConvite = "";
                    if (row.token_expirado) {
                        btnReenviarConvite = `
                            <button class="btn btn-info btn-sm" onclick="reenviarConvite(${data})">
                                <i class="fas fa-envelope"></i> Reenviar Convite
                            </button>
                        `;
                    }

                    // Botão DELETAR (aparece somente para inativos)
                    let btnDeletar = "";
                    if (row.status === "inativo") {
                        btnDeletar = `
                            <button class="btn btn-danger btn-sm" onclick="deletarMembro(${data})">
                                <i class="fas fa-trash"></i> Deletar
                            </button>
                        `;
                    }

                    // Retorna todos os botões necessários na mesma célula
                    return `
                        ${btnVisualizar}
                        ${btnEditar}
                        ${btnReativar}
                        ${btnSuspender}
                        ${btnReenviarConvite}
                        ${btnDeletar}
                    `;
                }
            }
        ],
        language: {
            url: "/lang/datatables/pt-BR.json"
        }
    });
});

/**
 * Função para exibir os detalhes do membro em um Swal
 */
function visualizarMembro(
    membroId,
    nome, email, nivelAcesso, status, cpf, telefone, celular, cidade,
    estado, oab, estadoOab, dataNascimento, foto
) {
    // Formata data no formato DD-MM-YYYY
    function formatarData(data) {
        if (!data || data === "Não informado") return "Não informado";
        const partes = data.split("-");
        return partes.length === 3 ? `${partes[2]}-${partes[1]}-${partes[0]}` : data;
    }

    let fotoHtml = foto
        ? `<img src="${foto}" alt="Foto do Membro" class="img-fluid rounded-circle mb-3 shadow" width="140">`
        : `<i class="fas fa-user-circle fa-7x text-muted mb-3"></i>`;

    Swal.fire({
        icon: 'info',
        title: 'Dados do Membro',
        html: `
                <div class="container text-center" style="max-width: 600px;">
                    <p><strong>Nome:</strong> ${nome}</p>
                    <p><strong>Email:</strong> ${email}</p>
                    <p><strong>CPF:</strong> ${cpf ? cpf : 'Não informado'}</p>
                </div>
            `,
        width: 600,
        showCancelButton: false,
        showConfirmButton: true,
        confirmButtonText: "<i class='fas fa-times'></i> Fechar",
        customClass: {
            confirmButton: "btn btn-secondary"
        },
        buttonsStyling: false
    });
}

/**
 * Função para reenviar convite com Swal de confirmação
 */
function reenviarConvite(membroId) {
    Swal.fire({
        title: "Reenviar Convite",
        text: "Deseja realmente reenviar o convite para este membro?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "<i class='fas fa-envelope'></i> Sim, reenviar",
        cancelButtonText: "<i class='fas fa-times'></i> Cancelar",
        buttonsStyling: false,
        reverseButtons: true,
        customClass: {
            confirmButton: "btn btn-primary ms-2",
            cancelButton: "btn btn-secondary me-2"
        }
    }).then((result) => {
        if (result.isConfirmed) {

            // Exibe o Swal de carregamento
            Swal.fire({
                title: "Enviando convite...",
                text: "Aguarde enquanto processamos o reenvio.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `/membros/${membroId}/reenviar-convite`,
                type: "POST",
                headers: { "X-CSRF-TOKEN": csrfToken },
                success: function (response) {
                    Swal.fire({
                        icon: "success",
                        title: "Convite reenviado!",
                        text: response.message || "O convite foi reenviado com sucesso."
                    });

                    // Recarrega a tabela após reenvio
                    $('#membrosEscritorioTable').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Erro",
                        text: "Não foi possível reenviar o convite. Tente novamente."
                    });
                }
            });
        }
    });
}
