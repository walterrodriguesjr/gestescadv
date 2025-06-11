$(document).ready(function () {
    const $estadoEscritorio = $("#estadoEscritorio");
    const $cidadeEscritorio = $("#cidadeEscritorio");

    function initializeChoices($select, placeholder) {
        if ($select.data('choicesInstance')) {
            $select.data('choicesInstance').destroy();
        }

        const choicesInstance = new Choices($select[0], {
            searchPlaceholderValue: placeholder,
            placeholderValue: placeholder,
            removeItemButton: true,
            shouldSort: false,
            noResultsText: "Nenhum resultado encontrado",
            noChoicesText: "Nenhuma opção disponível"
        });

        $select.data('choicesInstance', choicesInstance);
    }

    function setChoiceValue($select, value) {
        const instance = $select.data('choicesInstance');
        if (instance && value) {
            instance.setChoiceByValue(value);
        }
    }

    async function carregarEstados(estadoSelecionado = null, cidadeSelecionada = null) {
        try {
            const response = await $.getJSON("https://servicodados.ibge.gov.br/api/v1/localidades/estados");

            $estadoEscritorio.empty().append('<option value="">Selecione um estado</option>');
            response.forEach(estado => {
                $estadoEscritorio.append(`<option value="${estado.sigla}">${estado.nome}</option>`);
            });

            initializeChoices($estadoEscritorio, "Selecione um estado");

            if (estadoSelecionado) {
                setTimeout(() => {
                    setChoiceValue($estadoEscritorio, estadoSelecionado);
                    carregarCidades(estadoSelecionado, cidadeSelecionada);
                }, 300);
            }
        } catch (error) {
            console.error("❌ Erro ao carregar estados:", error);
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "Erro ao carregar estados. Por favor, tente novamente.",
            });
        }
    }

    async function carregarCidades(estadoSigla, cidadeSelecionada = null) {
        try {
            if (!estadoSigla) {
                $cidadeEscritorio.prop("disabled", true).empty().append('<option value="">Selecione uma cidade</option>');
                initializeChoices($cidadeEscritorio, "Selecione uma cidade");
                return;
            }

            const response = await $.getJSON(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoSigla}/municipios`);
            $cidadeEscritorio.empty().append('<option value="">Selecione uma cidade</option>');
            response.forEach(cidade => {
                $cidadeEscritorio.append(`<option value="${cidade.nome}">${cidade.nome}</option>`);
            });

            initializeChoices($cidadeEscritorio, "Selecione uma cidade");

            if (cidadeSelecionada) {
                setTimeout(() => {
                    setChoiceValue($cidadeEscritorio, cidadeSelecionada);
                }, 300);
            }

            $cidadeEscritorio.prop("disabled", false);
        } catch (error) {
            console.error("❌ Erro ao carregar cidades:", error);
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "Erro ao carregar cidades. Por favor, tente novamente.",
            });
        }
    }

    async function carregarDadosEscritorio() {
        if (!escritorioShowUrl || escritorioShowUrl === "null") {
            console.warn("⚠️ Nenhuma URL de escritório definida.");
            return;
        }

        try {
            const response = await $.ajax({
                type: "GET",
                url: escritorioShowUrl,
                dataType: "json",
                headers: { "X-CSRF-TOKEN": csrfToken }
            });

            if (response.success) {
                const dados = response.dados;

                $("#nomeEscritorio").val(dados.nome_escritorio);
                $("#cnpjEscritorio").val(dados.cnpj_escritorio);
                $("#telefoneEscritorio").val(dados.telefone_escritorio);
                $("#celularEscritorio").val(dados.celular_escritorio);
                $("#emailEscritorio").val(dados.email_escritorio);
                $("#cepEscritorio").val(dados.cep_escritorio);
                $("#logradouroEscritorio").val(dados.logradouro_escritorio);
                $("#numeroEscritorio").val(dados.numero_escritorio);
                $("#bairroEscritorio").val(dados.bairro_escritorio);

                await carregarEstados(dados.estado_escritorio, dados.cidade_escritorio);
            } else {
                console.warn("⚠️ Nenhum escritório cadastrado.");
            }
        } catch (error) {
            console.error("❌ Erro ao carregar escritório:", error);
        }
    }

    // (Opcional) Disponibiliza a função de show globalmente
    window.carregarDadosEscritorioGlobal = carregarDadosEscritorio;

    // Evento de mudança no select de estados
    $estadoEscritorio.on("change", function () {
        carregarCidades($(this).val());
    });

    // Chama a função para carregar os dados, caso já exista um ID
    if (escritorioId) {
        carregarDadosEscritorio();
    }
});
