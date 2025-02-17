$(document).ready(function () {
    const $estadoEscritorio = $("#estadoEscritorio");
    const $cidadeEscritorio = $("#cidadeEscritorio");
    const $cepEscritorio = $("#cepEscritorio");
    const $logradouroEscritorio = $("#logradouroEscritorio");
    const $bairroEscritorio = $("#bairroEscritorio");
    const $numeroEscritorio = $("#numeroEscritorio");

    let ultimoCepConsultado = "";

    // Inicializa Choices.js nos selects
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

    // Define valor no Choices.js corretamente
    function setChoiceValue($select, value) {
        const instance = $select.data("choicesInstance");
        if (instance && value) {
            instance.setChoiceByValue(value);
        }
    }

    // Carregar cidades ao selecionar um estado
    async function carregarCidades(estadoSigla, cidadeSelecionada = null) {
        if (!estadoSigla) {
            $cidadeEscritorio.prop("disabled", true).empty().append('<option value="">Selecione uma cidade</option>');
            initializeChoices($cidadeEscritorio, "Selecione uma cidade");
            return;
        }

        try {
            const response = await $.ajax({
                url: `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoSigla}/municipios`,
                type: "GET",
                dataType: "json",
            });

            $cidadeEscritorio.empty().append('<option value="">Selecione uma cidade</option>');
            response.forEach(cidade => {
                $cidadeEscritorio.append(`<option value="${cidade.nome}">${cidade.nome}</option>`);
            });

            $cidadeEscritorio.prop("disabled", false);
            initializeChoices($cidadeEscritorio, "Selecione uma cidade");

            if (cidadeSelecionada) {
                setTimeout(() => {
                    setChoiceValue($cidadeEscritorio, cidadeSelecionada);
                }, 300);
            }
        } catch (error) {
            Swal.fire({ icon: "error", title: "Erro", text: "Erro ao carregar cidades. Tente novamente." });
        }
    }

    // Busca CEP e preenche os campos automaticamente
    $cepEscritorio.on("input", function () {
        const cep = $(this).val().replace(/\D/g, "");

        if (cep.length === 8 && cep !== ultimoCepConsultado) {
            ultimoCepConsultado = cep;

            $.ajax({
                url: `https://viacep.com.br/ws/${cep}/json/`,
                type: "GET",
                dataType: "json",
                success: async function (data) {
                    if (data.erro) {
                        Swal.fire({ icon: "warning", title: "Atenção!", text: "CEP não encontrado!" });
                        limparEndereco();
                        return;
                    }

                    $logradouroEscritorio.val(data.logradouro);
                    $bairroEscritorio.val(data.bairro);
                    setChoiceValue($estadoEscritorio, data.uf);

                    setTimeout(() => {
                        carregarCidades(data.uf, data.localidade);
                    }, 500);
                },
                error: function () {
                    Swal.fire({ icon: "error", title: "Erro", text: "Erro ao buscar o CEP. Tente novamente." });
                    limparEndereco();
                },
            });
        }
    });

    // Função para limpar os campos de endereço
    function limparEndereco() {
        $logradouroEscritorio.val("");
        $bairroEscritorio.val("");
        $numeroEscritorio.val("");
        $estadoEscritorio.val("");
        $cidadeEscritorio.prop("disabled", true).empty().append('<option value="">Selecione uma cidade</option>');
    }

    // Evento ao mudar estado manualmente
    $estadoEscritorio.on("change", function () {
        carregarCidades($(this).val());
    });

    // Inicializa Choices.js
    initializeChoices($estadoEscritorio, "Selecione um estado");
    initializeChoices($cidadeEscritorio, "Selecione uma cidade");

    // 🔥 **Corrigindo o problema do botão de atualização** 🔥
    $(document).off("click", "#buttonAtualizarDadosEscritorio").on("click", "#buttonAtualizarDadosEscritorio", async function (e) {
        e.preventDefault();

        if (!escritorioUpdateUrl) {
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "Nenhum escritório foi encontrado para atualização.",
            });
            return;
        }

        if (!$("#dados-escritorio-form").valid()) {
            Swal.fire({
                icon: "warning",
                title: "Atenção",
                text: "Preencha todos os campos obrigatórios.",
            });
            return;
        }

        let loadingSwal = Swal.fire({
            title: "Atualizando...",
            text: "Aguarde enquanto seus dados estão sendo atualizados.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

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

        const formData = {
            _method: "PUT",
            nome_escritorio: $("#nomeEscritorio").val(),
            cnpj_escritorio: $("#cnpjEscritorio").val(),
            telefone_escritorio: $("#telefoneEscritorio").val(),
            celular_escritorio: $("#celularEscritorio").val(),
            email_escritorio: $("#emailEscritorio").val(),
            cep_escritorio: $("#cepEscritorio").val(),
            logradouro_escritorio: $("#logradouroEscritorio").val(),
            numero_escritorio: $("#numeroEscritorio").val(),
            bairro_escritorio: $("#bairroEscritorio").val(),
            estado_escritorio: $("#estadoEscritorio").val(),
            cidade_escritorio: $("#cidadeEscritorio").val(),
            _token: csrfToken,
        };

        try {
            const response = await $.post(escritorioUpdateUrl, formData);

            clearTimeout(timeout);
            if (timeoutReached) return;

            let requestEndTime = new Date().getTime();
            let elapsedTime = requestEndTime - requestStartTime;

            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    icon: "success",
                    title: "Sucesso!",
                    text: "Dados do escritório atualizados com sucesso!",
                });
            }, Math.max(minWaitTime - elapsedTime, 0));
        } catch (error) {
            clearTimeout(timeout);
            if (timeoutReached) return;

            let errorMessage = "Erro ao atualizar os dados. Por favor, tente novamente.";

            if (error.status === 422) {
                const errors = error.responseJSON.errors;
                errorMessage = Object.values(errors).join("\n");
            }

            Swal.fire({
                icon: "error",
                title: "Erro",
                text: errorMessage,
            });
        }
    });

});
