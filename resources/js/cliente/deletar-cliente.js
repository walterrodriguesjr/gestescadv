// Abre o modal de exclusão e armazena o ID do cliente
$(document).on("click", "#abrirModalDeletarCliente", function (e) {
    e.preventDefault();

    const clienteId = $(this).data("id"); // Obtém o ID do cliente do botão
    $("#clienteIdDeletar").val(clienteId); // Define o ID no campo oculto do modal

    // Abre o modal de confirmação
    $("#clienteModalDeletar").modal("show");
});

// Deleta o cliente ao clicar no botão "Deletar" no modal
$("#salvarClienteDeletar").click(function (e) {
    e.preventDefault();

    const clienteId = $("#clienteIdDeletar").val(); // Recupera o ID do cliente do campo oculto

    // Exibe o spinner
    $("#deletarSpinner").removeClass("d-none");

    // Faz a requisição DELETE para excluir o cliente
    $.ajax({
        type: "DELETE",
        url: `/cliente/${clienteId}`, // Endpoint para exclusão do cliente
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"), // CSRF Token
        },
        success: function () {
            toastr.success("Cliente deletado com sucesso!");

            // Fecha o modal e atualiza a tabela
            $("#clienteModalDeletar").modal("hide");
            listarClientes();
            // Remove o spinner após 1 segundos
            setTimeout(() => {
                $("#deletarSpinner").addClass("d-none");
            }, 1000);
        },
        error: function () {
            $("#deletarSpinner").addClass("d-none");
            toastr.error("Erro ao deletar o cliente. Tente novamente.");
        },
    });
});
