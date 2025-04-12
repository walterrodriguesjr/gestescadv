/**
 * Função para deletar um membro inativo com confirmação e token de segurança
 */
function deletarMembro(membroId) {
    Swal.fire({
        title: "Tem certeza?",
        text: "Esta ação é irreversível. Você perderá todos os dados deste membro.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "<i class='fas fa-trash'></i> Sim, excluir",
        cancelButtonText: "<i class='fas fa-times'></i> Cancelar",
        buttonsStyling: false,
        reverseButtons: true,
        customClass: {
            confirmButton: "btn btn-danger ms-2",
            cancelButton: "btn btn-secondary me-2"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const token = Math.floor(100000 + Math.random() * 900000).toString();

            Swal.fire({
                title: "Confirme a Exclusão",
                text: `Digite o código de segurança: ${token}`,
                input: "text",
                inputPlaceholder: "Digite o código aqui",
                showCancelButton: true,
                confirmButtonText: "<i class='fas fa-check'></i> Confirmar",
                cancelButtonText: "<i class='fas fa-times'></i> Cancelar",
                buttonsStyling: false,
                reverseButtons: true,
                customClass: {
                    confirmButton: "btn btn-primary ms-2",
                    cancelButton: "btn btn-secondary me-2"
                },
                preConfirm: (valorDigitado) => {
                    if (valorDigitado !== token) {
                        Swal.showValidationMessage("Código incorreto. Tente novamente.");
                        return false;
                    }
                }
            }).then((confirmacao) => {
                if (confirmacao.isConfirmed) {
                    let podeFechar = false;
                    const tempoMinimo = 1500;
                    const tempoMaximo = 10000;

                    Swal.fire({
                        title: "Excluindo...",
                        text: "Aguarde enquanto processamos a exclusão.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            setTimeout(() => { podeFechar = true; }, tempoMinimo);
                            setTimeout(() => {
                                if (Swal.isVisible()) Swal.close();
                            }, tempoMaximo);
                        }
                    });

                    $.ajax({
                        url: `/membros/${membroId}/delete`,
                        type: "DELETE",
                        headers: { "X-CSRF-TOKEN": csrfToken },
                        success: function (response) {
                            const mostrarSucesso = () => {
                                Swal.fire({
                                    icon: "success",
                                    title: "Membro excluído!",
                                    text: response.message || "O membro foi removido com sucesso."
                                });
                                $('#membrosEscritorioTable').DataTable().ajax.reload();
                            };

                            if (podeFechar) {
                                Swal.close();
                                mostrarSucesso();
                            } else {
                                setTimeout(() => {
                                    Swal.close();
                                    mostrarSucesso();
                                }, tempoMinimo);
                            }
                        },
                        error: function () {
                            const mostrarErro = () => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Erro!",
                                    text: "Não foi possível excluir o membro. Tente novamente."
                                });
                            };

                            if (podeFechar) {
                                Swal.close();
                                mostrarErro();
                            } else {
                                setTimeout(() => {
                                    Swal.close();
                                    mostrarErro();
                                }, tempoMinimo);
                            }
                        }
                    });
                }
            });
        }
    });
}
