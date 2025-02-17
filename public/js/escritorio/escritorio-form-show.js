$(document).ready(function () {
    const $estadoEscritorio = $("#estadoEscritorio");
    const $cidadeEscritorio = $("#cidadeEscritorio");

    // Inicializa Choices.js no select
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

    // Define o valor selecionado no Choices.js
    function setChoiceValue($select, value) {
        const instance = $select.data('choicesInstance');
        if (instance && value) {
            instance.setChoiceByValue(value);
        }
    }

    // Carrega os estados e inicializa Choices.js
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

    // Carrega as cidades com base no estado selecionado
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

            // Define a cidade previamente salva
            if (cidadeSelecionada) {
                setTimeout(() => {
                    setChoiceValue($cidadeEscritorio, cidadeSelecionada);
                }, 300);
            }

            $cidadeEscritorio.prop("disabled", false);
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "Erro ao carregar cidades. Por favor, tente novamente.",
            });
        }
    }

    // Carrega os dados do escritório e preenche os campos
    async function carregarDadosEscritorio() {
        if (!escritorioShowUrl) {
            Swal.fire({
                icon: "warning",
                title: "Aviso",
                text: "Nenhum escritório cadastrado.",
            });
            return;
        }

        try {
            const response = await $.get(escritorioShowUrl, { headers: { "X-CSRF-TOKEN": csrfToken } });

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

                // Aguarda carregar estados e só depois define o estado e cidade
                await carregarEstados();

                if (dados.estado_escritorio) {
                    setChoiceValue($estadoEscritorio, dados.estado_escritorio);

                    // Aguarda carregar cidades antes de definir a selecionada
                    setTimeout(async () => {
                        await carregarCidades(dados.estado_escritorio, dados.cidade_escritorio);
                    }, 500);
                }
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Aviso",
                    text: "Nenhum escritório cadastrado.",
                });
            }
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "Erro ao carregar os dados do escritório.",
            });
        }
    }

    // Evento de mudança no select de estados
    $estadoEscritorio.on("change", function () {
        const estadoSelecionado = $(this).val();
        carregarCidades(estadoSelecionado);
    });

    // Chama o carregamento de dados do escritório ao iniciar
    carregarDadosEscritorio();
});
