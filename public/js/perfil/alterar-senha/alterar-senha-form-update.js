$(document).ready(function () {
    $("#buttonAlterarSenha").click(function (e) {
        e.preventDefault();

        // Verifica se o formulário está válido
        if ($("#alterar-senha-form").valid()) {
            let loadingSwal = Swal.fire({
                title: "Alterando senha...",
                text: "Aguarde enquanto sua senha está sendo atualizada.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let requestStartTime = new Date().getTime(); // Marca o tempo de início da requisição
            let minWaitTime = 1500; // Tempo mínimo de exibição do spinner (1.5 segundos)
            let maxWaitTime = 10000; // Tempo máximo de espera (10 segundos)
            let timeoutReached = false; // Controle do timeout

            const formData = {
                senha_atual: $("#senhaAtual").val(),
                nova_senha: $("#novaSenha").val(),
                nova_senha_confirmation: $("#confirmarSenha").val(),
                _token: csrfToken, // Token CSRF
            };

            // **Define um timeout para forçar um erro após 10 segundos**
            let timeout = setTimeout(() => {
                timeoutReached = true;
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Erro",
                    text: "A requisição demorou muito para responder. Tente novamente.",
                    confirmButtonText: "<i class='fas fa-check'></i> OK"
                });
            }, maxWaitTime);

            // Faz a requisição AJAX
            $.ajax({
                url: "/alterar-senha",
                type: "POST",
                data: formData,
                headers: { "X-CSRF-TOKEN": csrfToken },
                success: function (response) {
                    clearTimeout(timeout); // Cancela o timeout caso a requisição seja bem-sucedida
                    if (timeoutReached) return; // Se o timeout foi atingido, ignora a resposta

                    let requestEndTime = new Date().getTime();
                    let elapsedTime = requestEndTime - requestStartTime;

                    setTimeout(() => {
                        Swal.close(); // Fecha o alerta de carregamento após tempo mínimo
                        Swal.fire({
                            icon: "success",
                            title: "Sucesso!",
                            text: response.success,
                            confirmButtonText: "<i class='fas fa-check'></i> OK"
                        });

                        $("#alterar-senha-form")[0].reset(); // Reseta os campos
                        atualizarIndicadoresSenha(""); // Reseta os indicadores
                    }, Math.max(minWaitTime - elapsedTime, 0));
                },
                error: function (xhr) {
                    clearTimeout(timeout); // Cancela o timeout caso a requisição falhe
                    if (timeoutReached) return; // Se o timeout foi atingido, ignora a resposta

                    let requestEndTime = new Date().getTime();
                    let elapsedTime = requestEndTime - requestStartTime;

                    setTimeout(() => {
                        Swal.close(); // Fecha o alerta de carregamento após tempo mínimo

                        let errorMessage = "Erro ao alterar a senha. Tente novamente.";

                        if (xhr.status === 422) {
                            // Se o backend retorna erro de validação
                            if (xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                errorMessage = "Ocorreram os seguintes erros:\n";
                                Object.keys(errors).forEach(function (key) {
                                    errorMessage += `• ${errors[key]}\n`;
                                });
                            } 
                            // Se o backend retorna a mensagem "A senha atual está incorreta"
                            else if (xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            }
                        }

                        Swal.fire({
                            icon: "error",
                            title: "Erro",
                            text: errorMessage,
                            confirmButtonText: "<i class='fas fa-check'></i> OK"
                        });

                    }, Math.max(minWaitTime - elapsedTime, 0));
                }
            });
        } else {
            Swal.fire({
                icon: "warning",
                title: "Atenção",
                text: "Preencha todos os campos corretamente.",
                confirmButtonText: "<i class='fas fa-check'></i> OK"
            });
        }
    });

    // Adiciona o método 'regex' para validação personalizada
    $.validator.addMethod("regex", function (value, element, param) {
        return this.optional(element) || param.test(value);
    }, "A senha deve conter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma minúscula, um número e um caractere especial.");

    // Validação do formulário
    $("#alterar-senha-form").validate({
        rules: {
            senha_atual: { required: true },
            nova_senha: {
                required: true,
                minlength: 8,
                regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
            },
            nova_senha_confirmation: {
                required: true,
                equalTo: "#novaSenha"
            },
        },
        messages: {
            senha_atual: { required: "Digite sua senha atual." },
            nova_senha: {
                required: "Digite uma nova senha.",
                minlength: "A senha deve ter pelo menos 8 caracteres.",
                regex: "A senha deve conter pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial.",
            },
            nova_senha_confirmation: {
                required: "Confirme a nova senha.",
                equalTo: "As senhas não coincidem.",
            },
        },
    });

    // Evento para monitorar a digitação da senha e atualizar os indicadores
    $("#novaSenha").on("input", function () {
        atualizarIndicadoresSenha($(this).val());
    });

    function atualizarIndicadoresSenha(senha) {
        let requisitos = {
            comprimento: senha.length >= 8,
            maiuscula: /[A-Z]/.test(senha),
            minuscula: /[a-z]/.test(senha),
            numero: /\d/.test(senha),
            especial: /[@$!%*?&]/.test(senha),
        };

        // Atualiza os indicadores de cada requisito
        $("#requisito-comprimento").html(requisitos.comprimento ? `<i class="fas fa-check-circle text-success"></i> Pelo menos <strong>8 caracteres</strong>` : `<i class="fas fa-times-circle text-danger"></i> Pelo menos <strong>8 caracteres</strong>`);
        $("#requisito-maiuscula").html(requisitos.maiuscula ? `<i class="fas fa-check-circle text-success"></i> Uma <strong>letra maiúscula</strong>` : `<i class="fas fa-times-circle text-danger"></i> Uma <strong>letra maiúscula</strong>`);
        $("#requisito-minuscula").html(requisitos.minuscula ? `<i class="fas fa-check-circle text-success"></i> Uma <strong>letra minúscula</strong>` : `<i class="fas fa-times-circle text-danger"></i> Uma <strong>letra minúscula</strong>`);
        $("#requisito-numero").html(requisitos.numero ? `<i class="fas fa-check-circle text-success"></i> Um <strong>número</strong>` : `<i class="fas fa-times-circle text-danger"></i> Um <strong>número</strong>`);
        $("#requisito-especial").html(requisitos.especial ? `<i class="fas fa-check-circle text-success"></i> Um <strong>caractere especial</strong>` : `<i class="fas fa-times-circle text-danger"></i> Um <strong>caractere especial</strong>`);

        // **Se todos os requisitos forem atendidos, altera o fundo do checklist**
        if (Object.values(requisitos).every(Boolean)) {
            $("#mensagemSenha").removeClass("alert-warning").addClass("alert-success");
        } else {
            $("#mensagemSenha").removeClass("alert-success").addClass("alert-warning");
        }
    }
});
