$(document).ready(function () {
    const $estadoEscritorio = $("#estadoEscritorio");
    const $cidadeEscritorio = $("#cidadeEscritorio");
    const $cepEscritorio = $("#cepEscritorio");
    const $logradouroEscritorio = $("#logradouroEscritorio");
    const $bairroEscritorio = $("#bairroEscritorio");
    const $numeroEscritorio = $("#numeroEscritorio");

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

    // Define o valor selecionado no Choices.js
    function setChoiceValue($select, value) {
        const instance = $select.data("choicesInstance");
        if (instance && value) {
            instance.setChoiceByValue(value);
        }
    }

    // Máscaras
    $("#cnpjEscritorio").mask("00.000.000/0000-00");
    $("#telefoneEscritorio").mask("(00) 0000-0000");
    $("#celularEscritorio").mask("(00) 00000-0000");
    $("#cepEscritorio").mask("00000-000");

    // Desabilita o select de cidade inicialmente
    $cidadeEscritorio.prop("disabled", true);

    // Função para limpar os campos de endereço
    function limparEndereco() {
        $logradouroEscritorio.val("");
        $bairroEscritorio.val("");
        $numeroEscritorio.val("");
        $estadoEscritorio.val("").trigger("change");
        $cidadeEscritorio.prop("disabled", true).empty().append('<option value="">Selecione uma cidade</option>');
        initializeChoices($cidadeEscritorio, "Selecione uma cidade");
    }

    // Carregar estados via API do IBGE
    async function carregarEstados() {
        try {
            const response = await $.ajax({
                url: "https://servicodados.ibge.gov.br/api/v1/localidades/estados",
                type: "GET",
                dataType: "json",
            });

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
                setChoiceValue($cidadeEscritorio, cidadeSelecionada);
            }
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "Erro ao carregar cidades. Por favor, tente novamente.",
            });
        }
    }

    // Evento ao mudar estado
    $estadoEscritorio.on("change", function () {
        carregarCidades($(this).val());
    });

    // Carregar estados ao iniciar
    carregarEstados();

    // Salvar dados do escritório
    $("#buttonSalvarDadosEscritorio").click(async function (e) {
        e.preventDefault();

        if (!$("#dados-escritorio-form").valid()) {
            Swal.fire({ icon: "warning", title: "Atenção", text: "Preencha todos os campos obrigatórios." });
            return;
        }

        let loadingSwal = Swal.fire({
            title: "Salvando...",
            text: "Aguarde enquanto seus dados estão sendo atualizados.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        let requestStartTime = new Date().getTime();
        let minWaitTime = 1500; // Tempo mínimo de exibição do spinner (1,5 segundos)
        let maxWaitTime = 10000; // Tempo máximo de espera (10 segundos)
        let timeoutReached = false;

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

        const formData = {
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
            const response = await $.post(escritorioStoreUrl, formData);
            clearTimeout(timeout);
            if (timeoutReached) return;

            let requestEndTime = new Date().getTime();
            let elapsedTime = requestEndTime - requestStartTime;

            setTimeout(() => {
                Swal.close();
                Swal.fire({ icon: "success", title: "Sucesso!", text: response.message });
            }, Math.max(minWaitTime - elapsedTime, 0));
        } catch (error) {
            clearTimeout(timeout);
            if (timeoutReached) return;

            Swal.fire({
                icon: "error",
                title: "Erro",
                text: error.responseJSON.message || "Erro ao salvar os dados.",
            });
        }
    });
});
