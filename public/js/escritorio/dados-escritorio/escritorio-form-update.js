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

    function setChoiceValue($select, value) {
        const instance = $select.data("choicesInstance");
        if (instance && value) {
            instance.setChoiceByValue(value);
        }
    }

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

            // Exibe o Swal de carregamento por pelo menos 1.5 segundos
            let swalLoading = Swal.fire({
                title: "Buscando CEP...",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                timer: 10000, // Tempo máximo de 10s
                timerProgressBar: true
            });

            let tempoMinimo = new Promise(resolve => setTimeout(resolve, 1500));

            $.ajax({
                url: `https://viacep.com.br/ws/${cep}/json/`,
                type: "GET",
                dataType: "json",
                success: async function (data) {
                    await tempoMinimo; // Aguarda pelo menos 1.5 segundos antes de fechar o Swal

                    Swal.close();

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
                error: async function () {
                    await tempoMinimo; // Aguarda pelo menos 1.5 segundos antes de fechar o Swal

                    Swal.close();
                    Swal.fire({ icon: "error", title: "Erro", text: "Erro ao buscar o CEP. Tente novamente." });
                    limparEndereco();
                },
            });
        }
    });


    function limparEndereco() {
        $logradouroEscritorio.val("");
        $bairroEscritorio.val("");
        $numeroEscritorio.val("");
        $estadoEscritorio.val("");
        $cidadeEscritorio.prop("disabled", true).empty().append('<option value="">Selecione uma cidade</option>');
    }

    $estadoEscritorio.on("change", function () {
        carregarCidades($(this).val());
    });

    initializeChoices($estadoEscritorio, "Selecione um estado");
    initializeChoices($cidadeEscritorio, "Selecione uma cidade");

    // ───────────────────────────────────────────────────────────────────
    // ⚠️ NOVA FUNÇÃO: carrega o ID do escritório via show, se estiver faltando
    // ───────────────────────────────────────────────────────────────────
    async function obterEscritorioIdSeNecessario() {
        // Se já existir 'escritorioId' e 'escritorioUpdateUrl', não faz nada
        if (escritorioId && escritorioUpdateUrl && escritorioUpdateUrl !== "null") {
            return;
        }

        // Se não houver URL de show, não tem o que fazer
        if (!escritorioShowUrl || escritorioShowUrl === "null") {
            return;
        }

        try {
            const response = await $.ajax({
                url: escritorioShowUrl,
                type: "GET",
                dataType: "json",
                headers: { "X-CSRF-TOKEN": csrfToken }
            });

            if (response.success && response.dados) {
                escritorioId = response.dados.id;
                escritorioUpdateUrl = "{{ route('dados-escritorio.update', ':id') }}".replace(':id', escritorioId);
            }
        } catch (err) {
        }
    }


    // 🔥 **Corrigindo o problema do botão de atualização** 🔥
    $(document).off("click", "#buttonAtualizarDadosEscritorio").on("click", "#buttonAtualizarDadosEscritorio", async function (e) {
        e.preventDefault();

        // 1) Garante que temos o ID do escritório (via GET se necessário)
        await obterEscritorioIdSeNecessario();

        // 3) Se não existir URL de update, significa que não há ID definido
        if (!escritorioUpdateUrl) {
            Swal.fire({
                icon: "error",
                title: "Erro",
                text: "Nenhum escritório foi encontrado para atualização.",
            });
            return;
        }

        // 4) Validação do formulário
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
