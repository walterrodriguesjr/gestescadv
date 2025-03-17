$(document).ready(function () {
    const $estadoEscritorio = $("#estadoEscritorio");
    const $cidadeEscritorio = $("#cidadeEscritorio");
    const $cepEscritorio = $("#cepEscritorio");
    const $logradouroEscritorio = $("#logradouroEscritorio");
    const $bairroEscritorio = $("#bairroEscritorio");
    const $numeroEscritorio = $("#numeroEscritorio");

    const $buttonSalvar = $("#buttonSalvarDadosEscritorio");
    const $buttonAtualizar = $("#buttonAtualizarDadosEscritorio");

    function initializeChoices($select, placeholder) {
        if ($select.data("choicesInstance")) {
            $select.data("choicesInstance").destroy();
        }
        const choicesInstance = new Choices($select[0], {
            searchPlaceholderValue: placeholder,
            placeholderValue: placeholder,
            removeItemButton: true,
            shouldSort: false,
            noResultsText: "Nenhum resultado encontrado",
            noChoicesText: "Nenhuma opção disponível",
        });
        $select.data("choicesInstance", choicesInstance);
    }

    // Máscaras
    $("#cnpjEscritorio").mask("00.000.000/0000-00");
    $("#telefoneEscritorio").mask("(00) 0000-0000");
    $("#celularEscritorio").mask("(00) 00000-0000");
    $("#cepEscritorio").mask("00000-000");

    // Validação
    $("#dados-escritorio-form").validate({
        rules: {
            nome_escritorio: { required: true, minlength: 3 },
            email_escritorio: { required: true, email: true },
            celular_escritorio: { required: true, minlength: 14, maxlength: 15 },
        },
        messages: {
            nome_escritorio: {
                required: "O nome do escritório é obrigatório.",
                minlength: "O nome deve ter no mínimo 3 caracteres."
            },
            email_escritorio: {
                required: "O email é obrigatório.",
                email: "Digite um email válido."
            },
            celular_escritorio: {
                required: "O celular é obrigatório.",
                minlength: "Digite um celular válido.",
                maxlength: "Digite um celular válido."
            }
        },
        errorPlacement: function (error, element) {
            error.addClass('text-danger small');
            error.insertAfter(element);
        }
    });

    async function carregarEstados() {
        try {
            const response = await $.getJSON("https://servicodados.ibge.gov.br/api/v1/localidades/estados");
            $estadoEscritorio.empty().append('<option value="">Selecione um estado</option>');
            response.forEach(estado => {
                $estadoEscritorio.append(`<option value="${estado.sigla}">${estado.nome}</option>`);
            });
            initializeChoices($estadoEscritorio, "Selecione um estado");
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "Erro ao carregar estados. Por favor, tente novamente.",
            });
        }
    }

    async function carregarCidades(estadoSigla) {
        if (!estadoSigla) {
            $cidadeEscritorio.prop("disabled", true).empty().append('<option value="">Selecione uma cidade</option>');
            initializeChoices($cidadeEscritorio, "Selecione uma cidade");
            return;
        }

        try {
            const response = await $.getJSON(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoSigla}/municipios`);
            $cidadeEscritorio.empty().append('<option value="">Selecione uma cidade</option>');
            response.forEach(cidade => {
                $cidadeEscritorio.append(`<option value="${cidade.nome}">${cidade.nome}</option>`);
            });
            $cidadeEscritorio.prop("disabled", false);
            initializeChoices($cidadeEscritorio, "Selecione uma cidade");
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "Erro ao carregar cidades. Por favor, tente novamente.",
            });
        }
    }

    $estadoEscritorio.on("change", function () {
        carregarCidades($(this).val());
    });

    $buttonSalvar.click(async function (e) {
        e.preventDefault();

        if (!$("#dados-escritorio-form").valid()) {
            Swal.fire({ icon: "warning", title: "Atenção", text: "Preencha todos os campos obrigatórios." });
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
            text: "Aguarde enquanto os dados estão sendo salvos.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        const formData = {
            nome_escritorio: $("#nomeEscritorio").val(),
            cnpj_escritorio: $("#cnpjEscritorio").val() || null,
            telefone_escritorio: $("#telefoneEscritorio").val() || null,
            celular_escritorio: $("#celularEscritorio").val(),
            email_escritorio: $("#emailEscritorio").val(),
            cep_escritorio: $("#cepEscritorio").val() || null,
            logradouro_escritorio: $("#logradouroEscritorio").val() || null,
            numero_escritorio: $("#numeroEscritorio").val() || null,
            bairro_escritorio: $("#bairroEscritorio").val() || null,
            estado_escritorio: $("#estadoEscritorio").val() || null,
            cidade_escritorio: $("#cidadeEscritorio").val() || null,
            _token: csrfToken,
        };

        try {
            const response = await $.post(escritorioStoreUrl, formData);
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

                // Oculta o botão de salvar e exibe o de atualizar
                $buttonSalvar.hide();
                $buttonAtualizar.show();
            }, Math.max(minWaitTime - elapsedTime, 0));

            // Se o back-end retornou "dados.id"
            if (response.dados && response.dados.id) {
                escritorioId = response.dados.id;

                // Substitui na string "template"
                escritorioUpdateUrl = escritorioUpdateTemplate.replace(':id', escritorioId);
                escritorioShowUrl = escritorioShowTemplate.replace(':id', escritorioId);

                // Se quiser recarregar dados na tela imediatamente (opcional)
                if (typeof carregarDadosEscritorioGlobal === 'function') {
                    await carregarDadosEscritorioGlobal();
                }
            } else {
                // ⚠️ Se não vier "dados.id", cai no fallback (caso queira)
                console.warn("⚠️ O store não retornou 'dados.id'. Botão de update vai falhar se não houver fallback.");
            }

        } catch (error) {
            clearTimeout(timeout);
            if (timeoutReached) return;

            console.error("❌ Erro no POST store:", error);

            let errorMessage = "Erro ao salvar os dados.";
            if (error.status === 422) {
                const errors = error.responseJSON.errors;
                errorMessage = Object.values(errors).join("\n");
            }
            Swal.fire({ icon: "error", title: "Erro", text: errorMessage });
        }
    });

    carregarEstados();
});
