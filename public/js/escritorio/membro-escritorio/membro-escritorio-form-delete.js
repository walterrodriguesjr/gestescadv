/**
 * Função para deletar um membro inativo com confirmação e token de segurança
 */
function deletarMembro(membroId) {
    // Primeiro Swal de confirmação
    Swal.fire({
        title: "Tem certeza?",
        text: "Esta ação é irreversível. Você perderá todos os dados deste membro.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sim, excluir",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            // Gera um token aleatório de 6 dígitos
            const token = Math.floor(100000 + Math.random() * 900000).toString();

            // Segundo Swal para digitar o token
            Swal.fire({
                title: "Confirme a Exclusão",
                text: `Digite o código de segurança: ${token}`,
                input: "text",
                inputPlaceholder: "Digite o código aqui",
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar",
                preConfirm: (valorDigitado) => {
                    if (valorDigitado !== token) {
                        Swal.showValidationMessage("Código incorreto. Tente novamente.");
                        return false;
                    }
                }
            }).then((confirmacao) => {
                if (confirmacao.isConfirmed) {
                    // Mostra o carregamento antes da requisição AJAX
                    Swal.fire({
                        title: "Excluindo...",
                        text: "Aguarde enquanto processamos a exclusão.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Requisição AJAX para excluir o membro
                    $.ajax({
                        url: `/membros/${membroId}/delete`, // Ajuste conforme necessário
                        type: "DELETE",
                        headers: { "X-CSRF-TOKEN": csrfToken },
                        success: function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Membro excluído!",
                                text: response.message || "O membro foi removido com sucesso."
                            });

                            // Recarrega a tabela após exclusão
                            $('#membrosEscritorioTable').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: "error",
                                title: "Erro!",
                                text: "Não foi possível excluir o membro. Tente novamente."
                            });
                        }
                    });
                }
            });
        }
    });
}
