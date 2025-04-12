/**
 * Script de exclusão de cliente (cliente-form-delete.js)
 * Inclua no HTML depois dos demais scripts, ex.:
 * <script src="/js/cliente/cliente-form-delete.js"></script>
 */

$(document).on("click", ".btn-deletar", function () {
    const clienteId = $(this).data("id");
    const tipoCliente = $(this).data("tipo");
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    Swal.fire({
        title: "Tem certeza que deseja deletar este cliente?",
        text: "⚠️ Todos os dados vinculados a este cliente serão apagados permanentemente!",
        icon: "warning",
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonText: '<i class="fas fa-trash"></i> Sim, deletar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-danger ms-2",
            cancelButton: "btn btn-secondary me-2"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const token = Math.floor(100000 + Math.random() * 900000).toString();

            Swal.fire({
                title: "⚠️ Exclusão Irreversível!",
                html: `
                    <p>Este cliente e todos os dados vinculados a ele serão <b>permanentemente excluídos</b>.</p>
                    <p>Para confirmar, digite o código de segurança abaixo:</p>
                    <p><b style="font-size: 1.4em;">${token}</b></p>
                `,
                input: "text",
                inputPlaceholder: "Digite o código aqui",
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: '<i class="fas fa-trash"></i> Deletar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-danger ms-2",
                    cancelButton: "btn btn-secondary me-2"
                },
                preConfirm: (valorDigitado) => {
                    if (valorDigitado !== token) {
                        Swal.showValidationMessage("❌ Código incorreto. Tente novamente.");
                        return false;
                    }
                }
            }).then((confirmacao) => {
                if (confirmacao.isConfirmed) {
                    let podeFechar = false;
                    const tempoMinimo = 1500;
                    const tempoMaximo = 10000;

                    Swal.fire({
                        title: "Deletando...",
                        text: "Aguarde enquanto processamos a exclusão.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            setTimeout(() => { podeFechar = true; }, tempoMinimo);
                            setTimeout(() => { if (Swal.isVisible()) Swal.close(); }, tempoMaximo);
                        }
                    });

                    $.ajax({
                        url: `/clientes/${clienteId}`,
                        type: "DELETE",
                        headers: { "X-CSRF-TOKEN": csrfToken },
                        success: function (response) {
                            const sucesso = () => {
                                Swal.fire({
                                    icon: "success",
                                    title: "Cliente deletado!",
                                    text: response.message || "O cliente foi removido com sucesso."
                                });
                                $("#tabelaClientes").DataTable().ajax.reload();
                            };

                            if (podeFechar) {
                                Swal.close();
                                sucesso();
                            } else {
                                setTimeout(() => {
                                    Swal.close();
                                    sucesso();
                                }, tempoMinimo);
                            }
                        },
                        error: function () {
                            const erro = () => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Erro!",
                                    text: "Não foi possível deletar o cliente. Tente novamente."
                                });
                            };

                            if (podeFechar) {
                                Swal.close();
                                erro();
                            } else {
                                setTimeout(() => {
                                    Swal.close();
                                    erro();
                                }, tempoMinimo);
                            }
                        }
                    });
                }
            });
        }
    });
});
