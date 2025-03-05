$(document).ready(function () {
    const $formMembro        = $("#dados-membro-escritorio-form");
    const $nomeMembro        = $("#nomeMembro");
    const $cpfMembro         = $("#cpfMembro");
    const $emailMembro       = $("#emailMembro");
    const $nivelAcessoMembro = $("#nivelAcessoMembro");

    // Máscara para o CPF
    $cpfMembro.mask("000.000.000-00");

    // Formata cada palavra do nome para ter inicial maiúscula
    function formatarNome(nome) {
        return nome.replace(/\b\w/g, function (char) {
            return char.toUpperCase();
        });
    }

    $nomeMembro.on("input", function () {
        $(this).val(formatarNome($(this).val()));
    });

    // Carrega os níveis de acesso via AJAX
    async function carregarNiveisAcesso() {
        try {
            const response = await $.ajax({
                url: nivelAcessoIndexUrl,
                type: "GET",
                dataType: "json",
            });

            if (response.success) {
                $nivelAcessoMembro.empty().append(`<option value="">Selecione um nível de acesso</option>`);
                response.niveis.forEach(nivel => {
                    $nivelAcessoMembro.append(`<option value="${nivel.id}">${nivel.nome}</option>`);
                });
            } else {
                throw new Error(response.message || "Erro ao buscar níveis de acesso.");
            }
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: error.message || "Erro ao carregar níveis de acesso.",
            });
        }
    }

    // Validação do formulário
    $formMembro.validate({
        ignore: [],
        rules: {
            nome_membro: { required: true },
            cpf_membro: { required: true, minlength: 14, maxlength: 14 },
            email_membro: { required: true, email: true },
            nivel_acesso_membro: { required: true }
        },
        messages: {
            nome_membro: { required: "O nome do membro é obrigatório." },
            cpf_membro: { required: "O CPF é obrigatório.", minlength: "CPF inválido.", maxlength: "CPF inválido." },
            email_membro: { required: "O e-mail é obrigatório.", email: "Digite um e-mail válido." },
            nivel_acesso_membro: { required: "O nível de acesso é obrigatório." }
        },
        errorClass: "is-invalid",
        validClass: "is-valid",
        errorPlacement: function (error, element) {
            error.addClass("invalid-feedback");
            element.closest(".form-group, .col-md-3, .col-md-4, .col-md-6, .col-md-12").append(error);
        },
        highlight: function (element) { $(element).addClass("is-invalid").removeClass("is-valid"); },
        unhighlight: function (element) { $(element).removeClass("is-invalid").addClass("is-valid"); }
    });

    // Força a revalidação quando o nível de acesso muda
    $nivelAcessoMembro.on("change", function () { $(this).valid(); });

    // Botão "Salvar Membro"
    $("#buttonSalvarMembroEscritorio").click(async function (e) {
        e.preventDefault();

        if (!$formMembro.valid()) {
            Swal.fire({
                icon: "warning",
                title: "Atenção",
                text: "Preencha todos os campos obrigatórios corretamente.",
            });
            return;
        }

        let requestStartTime = new Date().getTime();
        let minWaitTime = 1500;
        let maxWaitTime = 10000;
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

        Swal.fire({
            title: "Cadastrando...",
            text: "Aguarde enquanto o membro está sendo cadastrado.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        const formData = {
            nome_membro: $nomeMembro.val(),
            cpf_membro: $cpfMembro.val(),
            email_membro: $emailMembro.val(),
            nivel_acesso_membro: $nivelAcessoMembro.val(),
            _token: csrfToken
        };

        try {
            const response = await $.post(membroStoreUrl, formData);
            clearTimeout(timeout);
            if (timeoutReached) return;

            let requestEndTime = new Date().getTime();
            let elapsedTime = requestEndTime - requestStartTime;

            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    icon: "success",
                    title: "Sucesso!",
                    text: response.message
                });

                $formMembro[0].reset();
                $(".form-control").removeClass("is-valid is-invalid");

                carregarNiveisAcesso();
                
                // ✅ Atualiza a tabela DataTables automaticamente
                $('#membrosEscritorioTable').DataTable().ajax.reload(null, false);
                
            }, Math.max(minWaitTime - elapsedTime, 0));

        } catch (error) {
            clearTimeout(timeout);
            if (timeoutReached) return;

            let errorMessage = "Erro ao cadastrar membro.";
            if (error.status === 422) {
                const errors = error.responseJSON.errors;
                errorMessage = Object.values(errors).join("\n");
            }
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: errorMessage
            });
        }
    });

    carregarNiveisAcesso();
});
