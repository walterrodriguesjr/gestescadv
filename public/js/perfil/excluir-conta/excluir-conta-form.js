$(document).ready(function () {
    $("#buttonExcluirConta").prop("disabled", true);

    function verificarHabilitacaoBotao() {
        let senha = $("#senhaConfirmacao").val().trim();
        let check = $("#confirmarExclusao").prop("checked");
        $("#buttonExcluirConta").prop("disabled", !(senha.length > 0 && check));
    }

    $("#senhaConfirmacao, #confirmarExclusao").on("input change", function () {
        verificarHabilitacaoBotao();
    });

    $("#buttonExcluirConta").click(function () {
        let senha = $("#senhaConfirmacao").val().trim();

        Swal.fire({
            title: "Verificando senha...",
            text: "Aguarde enquanto validamos sua senha.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "/validar-senha-exclusao",
            type: "POST",
            data: {
                senha_confirmacao: senha,
                _token: $("input[name=_token]").val()
            },
            success: function (response) {
                Swal.close();
                Swal.fire({
                    icon: "success",
                    title: "Código enviado!",
                    text: "O código de exclusão foi enviado para seu e-mail.",
                    confirmButtonText: "<i class='fas fa-check'></i> OK"
                }).then(() => {
                    abrirSweetAlertConfirmacao(senha);
                });
            },
            error: function (xhr) {
                Swal.close();
                let errorMessage = "Erro ao validar a senha. Tente novamente.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: "error",
                    title: "Erro",
                    text: errorMessage,
                    confirmButtonText: "<i class='fas fa-check'></i> OK"
                });
            }
        });
    });

    function abrirSweetAlertConfirmacao(senha) {
        Swal.fire({
            title: "<i class='fas fa-exclamation-triangle text-danger'></i> Atenção! Confirmação de Exclusão",
            html: `
                <p>Digite o código de confirmação enviado ao seu e-mail:</p>
                <input type="text" id="codigoConfirmacaoModal" class="swal2-input" placeholder="Digite o código" maxlength="6">
            `,
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "<i class='fas fa-times'></i> Cancelar",
            confirmButtonText: "<i class='fas fa-trash'></i> Confirmar Exclusão",
            reverseButtons: true,
            customClass: {
                actions: 'd-flex justify-content-center gap-3 mt-3',
                cancelButton: "btn btn-secondary",
                confirmButton: "btn btn-danger"
            },
            buttonsStyling: false,
            preConfirm: () => {
                let inputCodigo = Swal.getPopup().querySelector("#codigoConfirmacaoModal");

                if (!inputCodigo || inputCodigo.value.trim() === "") {
                    return Swal.showValidationMessage("O código de confirmação é obrigatório.");
                }

                let codigo = inputCodigo.value.trim();

                if (!/^\d{6}$/.test(codigo)) {
                    return Swal.showValidationMessage("O código deve conter exatamente 6 dígitos numéricos.");
                }

                return codigo;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let codigo = parseInt(result.value, 10); // **Agora capturando corretamente**
                confirmarExclusaoConta(senha, codigo);
            }
        });
    }

    function confirmarExclusaoConta(senha, codigo) {
        Swal.fire({
            title: "Excluindo conta...",
            text: "Aguarde enquanto sua conta está sendo removida.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "/excluir-conta",
            type: "POST",
            data: {
                senha_confirmacao: senha,
                codigo_exclusao: codigo,
                _token: $("input[name=_token]").val()
            },
            success: function (response) {
                Swal.close();
                Swal.fire({
                    icon: "success",
                    title: "Conta excluída!",
                    text: response.message,
                    confirmButtonText: "<i class='fas fa-check'></i> OK"
                }).then(() => {
                    window.location.href = "/logout";
                });
            },
            error: function (xhr) {
                Swal.close();
                let errorMessage = "Erro ao excluir a conta. Tente novamente.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: "error",
                    title: "Erro",
                    text: errorMessage,
                    confirmButtonText: "<i class='fas fa-check'></i> OK"
                });
            }
        });
    }
});
