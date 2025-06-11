    $(document).ready(function () {
        // Variável para guardar os dados do perfil do usuário
        let userProfileData = null;

        // Função para inicializar o Choices.js em um select
        function initializeChoices($select, placeholder) {
            // Se já existir uma instância do Choices no elemento, destruímos para recriar
            if ($select.data('choicesInstance')) {
                $select.data('choicesInstance').destroy();
            }

            // Cria uma nova instância do Choices no select
            const choicesInstance = new Choices($select[0], {
                searchPlaceholderValue: placeholder,
                placeholderValue: placeholder,
                removeItemButton: true,
                shouldSort: false,
                noResultsText: "Nenhum resultado encontrado",
                noChoicesText: "Nenhuma opção disponível"
            });

            // Armazena a instância no próprio elemento para uso futuro
            $select.data('choicesInstance', choicesInstance);
        }

        // Função para definir o valor selecionado no Choices.js
        function setChoiceValue($select, value) {
            const instance = $select.data('choicesInstance');
            if (instance && value) {
                instance.setChoiceByValue(value);
            }
        }

        const $estadoUsuario = $("#estadoUsuario");
        const $cidadeUsuario = $("#cidadeUsuario");
        const $estadoOabUsuario = $("#estadoOabUsuario");

        // Desabilita o select de cidade inicialmente
        $cidadeUsuario.prop("disabled", true);

        // Carrega a lista de estados do IBGE e inicializa Choices
        function carregarEstados() {
            $.ajax({
                url: "https://servicodados.ibge.gov.br/api/v1/localidades/estados",
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $estadoUsuario.empty().append('<option value="">Selecione um estado</option>');
                    $estadoOabUsuario.empty().append('<option value="">Selecione um estado da OAB</option>');

                    data.forEach(function (estado) {
                        const option = `<option value="${estado.sigla}">${estado.nome}</option>`;
                        $estadoUsuario.append(option);
                        $estadoOabUsuario.append(option);
                    });

                    // Inicializa o Choices nos selects de estado
                    initializeChoices($estadoUsuario, "Selecione um estado");
                    initializeChoices($estadoOabUsuario, "Selecione um estado da OAB");

                    // Se já temos dados do usuário carregados, setamos os valores
                    if (userProfileData) {
                        // Ajusta o estado do usuário, se existir
                        if (userProfileData.estado_usuario) {
                            setChoiceValue($estadoUsuario, userProfileData.estado_usuario);
                            carregarCidades(userProfileData.estado_usuario, userProfileData.cidade_usuario);
                        } else {
                            // Se não há estado, deixa a cidade desabilitada
                            $cidadeUsuario.prop("disabled", true)
                                .empty()
                                .append('<option value="">Selecione uma cidade</option>');
                            initializeChoices($cidadeUsuario, "Selecione uma cidade");
                        }

                        // Ajusta o estado da OAB, se existir
                        if (userProfileData.estado_oab_usuario) {
                            setChoiceValue($estadoOabUsuario, userProfileData.estado_oab_usuario);
                        }
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Erro",
                        text: "Erro ao carregar estados. Por favor, tente novamente.",
                        confirmButtonText: "<i class='fas fa-check'></i> OK"
                    });
                }
            });
        }

        // Carrega a lista de cidades do IBGE com base no estado
        function carregarCidades(estadoSigla, cidadeSelecionada = null) {
            // Se não houver sigla de estado, apenas reseta o select de cidade
            if (!estadoSigla) {
                $cidadeUsuario.prop("disabled", true)
                    .empty()
                    .append('<option value="">Selecione uma cidade</option>');
                initializeChoices($cidadeUsuario, "Selecione uma cidade");
                return;
            }

            $.ajax({
                url: `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoSigla}/municipios`,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $cidadeUsuario.empty().append('<option value="">Selecione uma cidade</option>');
                    $cidadeUsuario.prop("disabled", false);

                    data.forEach(function (cidade) {
                        $cidadeUsuario.append(`<option value="${cidade.nome}">${cidade.nome}</option>`);
                    });

                    // Inicializa o Choices no select de cidade
                    initializeChoices($cidadeUsuario, "Selecione uma cidade");

                    // Se temos uma cidade salva, define como selecionada
                    if (cidadeSelecionada) {
                        setChoiceValue($cidadeUsuario, cidadeSelecionada);
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Erro",
                        text: "Erro ao carregar cidades. Por favor, tente novamente.",
                        confirmButtonText: "<i class='fas fa-check'></i> OK"
                    });
                }
            });
        }

        // Busca os dados do usuário logado ao iniciar
        function carregarDadosUsuario() {
            $.ajax({
                type: "GET",
                url: perfilShowUrl, // URL definida no Blade
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    if (response.success) {
                        userProfileData = response.dados;

                        // Preenche os campos de texto
                        $("#nomeUsuario").val(userProfileData.nome_usuario);
                        $("#emailUsuario").val(userProfileData.email_usuario);
                        $("#cpfUsuario").val(userProfileData.cpf_usuario);
                        $("#celularUsuario").val(userProfileData.celular_usuario);
                        $("#dataNascimentoUsuario").val(userProfileData.data_nascimento_usuario);
                        $("#oabUsuario").val(userProfileData.oab_usuario);

                        // Mostra a foto (ou imagem padrão)
                        $("#fotoPreview").attr("src", userProfileData.foto_usuario);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Erro",
                            text: response.message || "Erro ao carregar os dados do usuário.",
                            confirmButtonText: "<i class='fas fa-check'></i> OK"
                        });
                    }

                    // Após obter os dados do usuário (userProfileData), carregamos os estados.
                    // Isso garantirá que possamos definir o estado e a cidade salvos corretamente.
                    carregarEstados();
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Erro",
                        text: "Erro ao carregar os dados. Tente novamente.",
                        confirmButtonText: "<i class='fas fa-check'></i> OK"
                    });
                }
            });
        }

        // Evento de mudança no select de estado do usuário
        $estadoUsuario.on("change", function () {
            const estadoSelecionado = $(this).val();
            carregarCidades(estadoSelecionado);
        });

        // Atualiza a pré-visualização da foto antes do upload
        $("#fotoUsuario").change(function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $("#fotoPreview").attr("src", e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Máscaras e formatações
        $("#cpfUsuario").mask("000.000.000-00");
        $("#celularUsuario").mask("(00) 00000-0000");
        $("#oabUsuario").mask("00000000");

        // Evento de salvar os dados do formulário
        $("#buttonSalvarDadosUsuarios").click(function (e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append("_method", "PUT"); // Define explicitamente o método PUT
            formData.append("nome_usuario", $("#nomeUsuario").val());
            formData.append("email_usuario", $("#emailUsuario").val());
            formData.append("cpf_usuario", $("#cpfUsuario").val());
            formData.append("celular_usuario", $("#celularUsuario").val());
            formData.append("data_nascimento_usuario", $("#dataNascimentoUsuario").val());
            formData.append("estado_usuario", $estadoUsuario.val());
            formData.append("cidade_usuario", $cidadeUsuario.val());
            formData.append("oab_usuario", $("#oabUsuario").val());
            formData.append("estado_oab_usuario", $estadoOabUsuario.val());

            // Adiciona a foto ao FormData se o usuário selecionou uma
            const fotoFile = $("#fotoUsuario")[0].files[0];
            if (fotoFile) {
                formData.append("foto_usuario", fotoFile);
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

            let requestStartTime = new Date().getTime(); // Marca o tempo de início da requisição
            let minWaitTime = 1500; // Tempo mínimo de exibição do spinner (1,5 segundos)
            let maxWaitTime = 10000; // Tempo máximo de espera (10 segundos)
            let timeoutReached = false; // Controle do timeout

            // Define um timeout para forçar um erro após 10 segundos
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

            $.ajax({
                url: perfilUpdateUrl, // URL gerada dinamicamente no Blade
                type: "POST", // Enviar como POST e forçar PUT via _method
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    "X-CSRF-TOKEN": csrfToken
                },
                success: function (response) {
                    clearTimeout(timeout); // Cancela o timeout caso a requisição seja bem-sucedida
                    if (timeoutReached) return; // Se o timeout foi atingido, ignora a resposta

                    let requestEndTime = new Date().getTime(); // Marca o tempo de finalização da requisição
                    let elapsedTime = requestEndTime - requestStartTime; // Calcula o tempo de execução

                    setTimeout(() => {
                        Swal.close(); // Fecha o alerta de carregamento após o tempo mínimo

                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Sucesso!",
                                text: "Dados atualizados com sucesso.",
                                confirmButtonText: "<i class='fas fa-check'></i> OK"
                            });

                            // Obtém a nova URL da foto do usuário
                            let novaFoto = response.dados.foto_usuario;

                            // Atualiza a imagem no perfil (onde o usuário editou)
                            $("#fotoPreview").attr("src", novaFoto);

                            // Atualiza a imagem do usuário no sidebar sem precisar recarregar a página
                            $(".sidebar .image img").attr("src", novaFoto);

                            atualizarHistoricoAlteracoes();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Erro",
                                text: response.message || "Houve um erro ao atualizar os dados.",
                                confirmButtonText: "<i class='fas fa-check'></i> OK"
                            });
                        }
                    }, Math.max(minWaitTime - elapsedTime, 0)); // Garante tempo mínimo de 1,5 segundos
                },
                error: function (xhr) {
                    clearTimeout(timeout); // Cancela o timeout caso a requisição falhe
                    if (timeoutReached) return; // Se o timeout foi atingido, ignora a resposta

                    let requestEndTime = new Date().getTime();
                    let elapsedTime = requestEndTime - requestStartTime;

                    setTimeout(() => {
                        Swal.close(); // Fecha o alerta de carregamento após tempo mínimo

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = "Ocorreram os seguintes erros:\n";
                            Object.keys(errors).forEach(function (key) {
                                errorMessage += `• ${errors[key]}\n`;
                            });

                            Swal.fire({
                                icon: "error",
                                title: "Erro na validação",
                                text: errorMessage,
                                confirmButtonText: "<i class='fas fa-check'></i> OK"
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Erro",
                                text: "Erro ao salvar os dados. Tente novamente.",
                                confirmButtonText: "<i class='fas fa-check'></i> OK"
                            });
                        }
                    }, Math.max(minWaitTime - elapsedTime, 0)); // Garante tempo mínimo de 1,5 segundos
                }
            });
        });

        // Inicia carregando os dados do usuário; ao final, carregaremos os estados.
        carregarDadosUsuario();
    });
