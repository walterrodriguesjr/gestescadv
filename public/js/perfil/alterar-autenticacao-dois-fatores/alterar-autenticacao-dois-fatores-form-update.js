$(document).ready(function () {
    // Inicializa o Select2 para o método de autenticação
    $('#tipoAutenticacao').select2({
        placeholder: "Selecione um método",
        allowClear: true,
        width: "100%"
    });

    // Atualiza mensagem e exibição do select de métodos
    function atualizarMensagem() {
        if ($("#switch2FA").is(":checked")) {
            $("#mensagem2FA").html(`
            <i class="fas fa-check-circle"></i> <strong>Sua autenticação de dois fatores já está ativa.</strong> 
            Caso queira desabilitar, clique no botão abaixo.
        `).removeClass("alert-danger").addClass("alert-success");

            $("#metodoAutenticacao").slideDown();
        } else {
            $("#mensagem2FA").html(`
            <i class="fas fa-exclamation-triangle"></i> <strong>Clique para habilitar sua autenticação de dois fatores.</strong>
            <br><br>
            <strong>Proteja sua conta!</strong> A <strong>2FA</strong> aumenta a segurança ao exigir um segundo fator (e-mail) 
            para login, dificultando acessos indevidos, mesmo que sua senha seja comprometida.
            <br><br>
            Ative agora e evite invasões ou tentativas de fraude!
        `).removeClass("alert-info").addClass("alert-danger");

            $("#metodoAutenticacao").slideUp();
        }
    }

    atualizarMensagem(); // Aplica no carregamento da página

    // Atualiza dinamicamente ao clicar no toggle switch
    $("#switch2FA").change(function () {
        atualizarMensagem();
    });

    // AJAX para atualizar as configurações de 2FA
    $("#buttonAlterarLaterarAutenticaoDoisFatores").click(function (e) {
        e.preventDefault();

        const doisFatoresAtivo = $("#switch2FA").is(":checked") ? "sim" : "nao";
        const metodoAutenticacao = $("#tipoAutenticacao").val();

        const formData = {
            dois_fatores: doisFatoresAtivo,
            tipo_autenticacao: metodoAutenticacao,
            _token: csrfToken
        };

        // Define os tempos de espera mínimo e máximo
        let requestStartTime = new Date().getTime();
        let minWaitTime = 1500; // Tempo mínimo do spinner (1.5 segundos)
        let maxWaitTime = 10000; // Tempo máximo antes de erro (10 segundos)
        let timeoutReached = false;

        let timeout = setTimeout(() => {
            timeoutReached = true;
            Swal.close();
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "A requisição demorou muito para responder. Tente novamente.",
            });
        }, maxWaitTime);

        // Exibe o SweetAlert de carregamento
        Swal.fire({
            title: "Atualizando autenticação...",
            text: "Aguarde enquanto sua configuração de segurança está sendo atualizada.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "/atualizar-2fa",
            type: "POST",
            data: formData,
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: function (response) {
                clearTimeout(timeout); // Cancela o timeout se a resposta chegou
                if (timeoutReached) return; // Se já chegou no timeout, não faz nada

                let requestEndTime = new Date().getTime();
                let elapsedTime = requestEndTime - requestStartTime;

                setTimeout(() => {
                    Swal.close(); // Fecha o alerta de carregamento após tempo mínimo
                    Swal.fire({
                        icon: "success",
                        title: "Sucesso!",
                        text: response.message,
                        confirmButtonText: "<i class='fas fa-check'></i> OK"
                    });

                    atualizarMensagem(); // Atualiza a mensagem na interface
                }, Math.max(minWaitTime - elapsedTime, 0)); // Garante tempo mínimo do spinner
            },
            error: function () {
                clearTimeout(timeout);
                if (timeoutReached) return;

                let requestEndTime = new Date().getTime();
                let elapsedTime = requestEndTime - requestStartTime;

                setTimeout(() => {
                    Swal.close(); // Fecha o alerta de carregamento após tempo mínimo
                    Swal.fire({
                        icon: "error",
                        title: "Erro",
                        text: "Erro ao atualizar configuração de segurança.",
                        confirmButtonText: "<i class='fas fa-check'></i> OK"
                    });
                }, Math.max(minWaitTime - elapsedTime, 0)); // Garante tempo mínimo do spinner
            }
        });
    });
});
