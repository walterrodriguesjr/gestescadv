/**
 * Script de exclusão de cliente (cliente-form-delete.js)
 * Inclua no HTML depois dos demais scripts, ex.:
 * <script src="/js/cliente/cliente-form-delete.js"></script>
 */

$(document).on("click", ".btn-deletar", function () {
    const clienteId = $(this).data("id");
    const tipoCliente = $(this).data("tipo");
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // 1) Primeiro Swal de Confirmação
    Swal.fire({
        title: "Tem certeza que deseja deletar este cliente?",
        text: "⚠️ Todos os dados vinculados a este cliente serão apagados permanentemente!",
        icon: "warning",
        showCancelButton: true,
        // Botão principal => vermelho com ícone de lixeira, no lado direito
        confirmButtonColor: "#d33",
        // Botão secundário => cinza, lado esquerdo
        cancelButtonColor: "#6c757d",
        confirmButtonText: '<i class="fas fa-trash"></i> Sim, deletar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        reverseButtons: true // inverte => cancel à esquerda, confirmar à direita
    }).then((result) => {
        if (result.isConfirmed) {
            // 2) Gerar Token de Segurança (6 dígitos)
            const token = Math.floor(100000 + Math.random() * 900000).toString();

            // 3) Segundo Swal => inserir o Token
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
                // Botão principal => vermelho
                confirmButtonColor: "#d33",
                // Botão secundário => cinza
                cancelButtonColor: "#6c757d",
                confirmButtonText: '<i class="fas fa-trash"></i> Deletar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true,
                preConfirm: (valorDigitado) => {
                    if (valorDigitado !== token) {
                        Swal.showValidationMessage("❌ Código incorreto. Tente novamente.");
                        return false;
                    }
                }
            }).then((confirmacao) => {
                if (confirmacao.isConfirmed) {
                    // 4) Swal de loading
                    Swal.fire({
                        title: "Deletando...",
                        text: "Aguarde enquanto processamos a exclusão.",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // 5) Requisição AJAX para deletar
                    $.ajax({
                        url: `/clientes/${clienteId}`,
                        type: "DELETE",
                        headers: { "X-CSRF-TOKEN": csrfToken },
                        success: function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Cliente deletado!",
                                text: response.message || "O cliente foi removido com sucesso."
                            });

                            // Recarrega a tabela após exclusão
                            $("#tabelaClientes").DataTable().ajax.reload();
                        },
                        error: function () {
                            Swal.fire({
                                icon: "error",
                                title: "Erro!",
                                text: "Não foi possível deletar o cliente. Tente novamente."
                            });
                        }
                    });
                }
            });
        }
    });
});
