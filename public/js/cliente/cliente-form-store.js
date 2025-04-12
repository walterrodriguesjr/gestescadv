$(document).ready(function () {


    // Função genérica para inicializar popovers
    function inicializarPopover($input, mensagem) {
        $input.popover({
            trigger: 'manual',
            html: true,
            placement: 'top',
            content: `
            <div>
                ${mensagem}
                <button type="button" class="close close-popover ml-2" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>`
        });

        // Exibe o popover ao passar o mouse após ter fechado
        $input.on('mouseenter', function () {
            $(this).popover('show');
        }).on('mouseleave', function () {
            $(this).popover('hide');
        });
    }

    //--------------------------------------------------
    // VARIÁVEIS GLOBAIS / ESTADOS E CHOICES
    //--------------------------------------------------
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Cache para lista de estados (ex.: [{sigla:"AC", nome:"Acre"}...])
    let listaEstados = [];

    // Armazenamos as instâncias de Choices para PF e PJ
    let choicesTipoCliente;
    let choicesEstadoCliente, choicesCidadeCliente;
    let choicesEstadoJuridico, choicesCidadeJuridico;

    //--------------------------------------------------
    // FUNÇÕES AUXILIARES
    //--------------------------------------------------
    // 1) Inicializa o Choices
    function initializeChoices($select, placeholder, removeItemButton = false) {
        if ($select.data("choicesInstance")) {
            $select.data("choicesInstance").destroy();
        }
        const inst = new Choices($select[0], {
            searchPlaceholderValue: placeholder,
            placeholderValue: placeholder,
            removeItemButton: removeItemButton,
            shouldSort: false,
            noResultsText: "Nenhum resultado encontrado",
            noChoicesText: "Nenhuma opção disponível"
        });
        $select.data("choicesInstance", inst);
        return inst;
    }

    // 2) Ajustar valor no Choices
    function setChoiceValue($select, value) {
        const instance = $select.data("choicesInstance");
        if (instance && value) {
            instance.setChoiceByValue(value);
        }
    }

    // 3) Aplica ou reseta validações via jQuery Validate
    function resetFormAndValidation($form) {
        $form[0].reset(); // limpa campos
        // se já existe validador, limpa mensagens
        if ($form.data("validator")) {
            $form.validate().resetForm();
            $form.find(".text-danger, .error, .valid").removeClass("text-danger error valid");
        }
    }

    //--------------------------------------------------
    // 0) Inicia o select "tipoCliente" com Choices
    //--------------------------------------------------
    choicesTipoCliente = initializeChoices($("#tipoCliente"), "Selecione o tipo");

    //--------------------------------------------------
    // 1) Trocar PF/PJ
    //--------------------------------------------------
    // Evento para fechar popover
    $(document).on('click', '.close-popover', function () {
        $('#cepCliente').popover('hide');
    });

    // Inicializa os popovers para os 3 inputs
    inicializarPopover($('#cepCliente'), 'Digite um CEP válido para preencher os dados automaticamente.');
    inicializarPopover($('#cepJuridico'), 'Digite um CEP válido para preencher os dados automaticamente.');
    inicializarPopover($('#cnpjCliente'), 'Digite um CNPJ válido para preencher os dados automaticamente.');


    // Fechar popover corretamente (ajustado para Bootstrap)
    $(document).on('click', '.close-popover', function () {
        const popoverId = $(this).closest('.popover').attr('id');
        $(`[aria-describedby="${popoverId}"]`).popover('hide');
    });



    // Exibe e ajusta popovers conforme o tipo de cliente selecionado
    $("#tipoCliente").on("change", function () {
        let tipo = $(this).val();
        $(".cliente-form").addClass("d-none");

        resetFormAndValidation($("#formPessoaFisica"));
        resetFormAndValidation($("#formPessoaJuridica"));

        $("#estadoCliente, #cidadeCliente, #estadoJuridico, #cidadeJuridico")
            .empty().append('<option value="">Selecione</option>');

        choicesEstadoCliente = initializeChoices($("#estadoCliente"), "Selecione o estado (PF)");
        choicesCidadeCliente = initializeChoices($("#cidadeCliente"), "Selecione a cidade (PF)");
        choicesEstadoJuridico = initializeChoices($("#estadoJuridico"), "Selecione o estado (PJ)");
        choicesCidadeJuridico = initializeChoices($("#cidadeJuridico"), "Selecione a cidade (PJ)");

        if (!listaEstados.length) {
            carregarListaEstados().then(() => {
                popularSelectEstado($("#estadoCliente"), listaEstados);
                popularSelectEstado($("#estadoJuridico"), listaEstados);
            });
        } else {
            popularSelectEstado($("#estadoCliente"), listaEstados);
            popularSelectEstado($("#estadoJuridico"), listaEstados);
        }

        // Controla a exibição dos popovers e forms corretamente
        if (tipo === "pessoa_fisica") {
            $("#formPessoaFisica").removeClass("d-none");

            $('#cepJuridico, #cnpjCliente').popover('hide');
            setTimeout(() => {
                $('#cepCliente').popover('show');
            }, 250);

        } else if (tipo === "pessoa_juridica") {
            $("#formPessoaJuridica").removeClass("d-none");

            $('#cepCliente').popover('hide');
            setTimeout(() => {
                $('#cepJuridico').popover('show');
                $('#cnpjCliente').popover('show');
            }, 250);

        } else {
            $('#cepCliente, #cepJuridico, #cnpjCliente').popover('hide');
        }
    });


    //--------------------------------------------------
    // 2) Máscaras
    //--------------------------------------------------
    $(".cpf-mask").mask("000.000.000-00");
    $(".cnpj-mask").mask("00.000.000/0000-00");
    $(".telefone-mask").mask("(00) 0000-0000");
    $(".celular-mask").mask("(00) 00000-0000");
    $(".cep-mask").mask("00000-000");

    //--------------------------------------------------
    // 3) Lista de Estados / IBGE
    //--------------------------------------------------
    async function carregarListaEstados() {
        // se já existe, não recarrega
        if (listaEstados.length) return;

        try {
            const response = await $.getJSON("https://servicodados.ibge.gov.br/api/v1/localidades/estados");
            // Ordena alfabeticamente
            response.sort((a, b) => (a.nome > b.nome) ? 1 : -1);
            // Cada item => { sigla, nome }
            listaEstados = response.map(uf => ({ sigla: uf.sigla, nome: uf.nome }));
        } catch (err) {
            console.error("Erro ao carregar lista de estados:", err);
        }
    }

    // Popula o <select> de estados e re-inicializa o Choices
    function popularSelectEstado($select, lista) {
        // limpa + "Selecione"
        $select.empty().append('<option value="">Selecione</option>');
        lista.forEach(uf => {
            $select.append(`<option value="${uf.sigla}">${uf.nome}</option>`);
        });
        // re-inicializa
        const placeholder = $select.is("#estadoCliente")
            ? "Selecione o estado (PF)"
            : $select.is("#estadoJuridico")
                ? "Selecione o estado (PJ)"
                : "Selecione o estado";
        initializeChoices($select, placeholder);
    }

    //--------------------------------------------------
    // 4) Ao escolher um estado => carrega cidades
    //--------------------------------------------------
    $("#estadoCliente").on("change", function () {
        let uf = $(this).val();
        carregarCidades(uf, $("#cidadeCliente"), "Selecione a cidade (PF)");
    });
    $("#estadoJuridico").on("change", function () {
        let uf = $(this).val();
        carregarCidades(uf, $("#cidadeJuridico"), "Selecione a cidade (PJ)");
    });

    async function carregarCidades(uf, $selectCidade, placeholder) {
        $selectCidade.empty().append('<option value="">Selecione</option>');
        if (!uf) {
            // re-inicializa e sai
            initializeChoices($selectCidade, placeholder);
            return;
        }
        try {
            const response = await $.getJSON(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`);
            // Ordena cidades
            response.sort((a, b) => (a.nome > b.nome) ? 1 : -1);

            response.forEach(c => {
                $selectCidade.append(`<option value="${c.nome}">${c.nome}</option>`);
            });
            initializeChoices($selectCidade, placeholder);
        } catch (error) {
            console.error("Erro ao carregar cidades:", error);
        }
    }

    //--------------------------------------------------
    // 5) BUSCA CEP (ViaCEP)
    //--------------------------------------------------
    // PF
    $("#cepCliente").on("input", function () {
        let cep = $(this).val().replace(/\D/g, "");
        if (cep.length === 8) {
            buscarCepPF(cep);
        }
    });
    function buscarCepPF(cep) {
        // Exibe loading antes da requisição
        Swal.fire({
            title: 'Buscando CEP...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            timer: 10000, // tempo máximo total (10s)
            timerProgressBar: true
        });

        const tempoMinimo = new Promise(resolve => setTimeout(resolve, 1500)); // 1.5s mínimo
        const requestCep = $.getJSON(`https://viacep.com.br/ws/${cep}/json/`);

        Promise.all([requestCep, tempoMinimo])
            .then(([data]) => {
                Swal.close(); // Fecha o loading
                if (!data.erro) {
                    $("#logradouroCliente").val(data.logradouro);
                    $("#bairroCliente").val(data.bairro);

                    // Ajusta estado
                    $("#estadoCliente").val(data.uf);
                    setChoiceValue($("#estadoCliente"), data.uf);
                    $("#estadoCliente").trigger("change");

                    // Ajusta cidade após carregar
                    setTimeout(() => {
                        $("#cidadeCliente").val(data.localidade);
                        setChoiceValue($("#cidadeCliente"), data.localidade);
                    }, 400);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "CEP Inválido",
                        text: "Verifique o CEP digitado."
                    });
                }
            })
            .catch(() => {
                Swal.close(); // Fecha o loading
                Swal.fire({
                    icon: "warning",
                    title: "Base de dados fora do ar",
                    text: "Não foi possível buscar o CEP. Insira os dados manualmente."
                });
            });
    }


    // PJ
    $("#cepJuridico").on("input", function () {
        let cep = $(this).val().replace(/\D/g, "");
        if (cep.length === 8) {
            buscarCepPJ(cep);
        }
    });
    function buscarCepPJ(cep) {
        Swal.fire({
            title: 'Buscando CEP...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            timer: 10000, // tempo máximo de 10s
            timerProgressBar: true
        });

        const tempoMinimo = new Promise(resolve => setTimeout(resolve, 1500)); // mínimo de 1.5s
        const requestCep = $.getJSON(`https://viacep.com.br/ws/${cep}/json/`);

        Promise.all([requestCep, tempoMinimo])
            .then(([data]) => {
                Swal.close();
                if (!data.erro) {
                    $("#logradouroJuridico").val(data.logradouro);
                    $("#bairroJuridico").val(data.bairro);

                    $("#estadoJuridico").val(data.uf);
                    setChoiceValue($("#estadoJuridico"), data.uf);
                    $("#estadoJuridico").trigger("change");

                    setTimeout(() => {
                        $("#cidadeJuridico").val(data.localidade);
                        setChoiceValue($("#cidadeJuridico"), data.localidade);
                    }, 400);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "CEP Inválido",
                        text: "Verifique o CEP digitado."
                    });
                }
            })
            .catch(() => {
                Swal.close();
                Swal.fire({
                    icon: "warning",
                    title: "Base de dados fora do ar",
                    text: "Não foi possível buscar o CEP. Insira os dados manualmente."
                });
            });
    }


    //--------------------------------------------------
    // 6) BUSCA CNPJ
    //--------------------------------------------------
    $("#cnpjCliente").on("input", async function () {
        let cnpj = $(this).val().replace(/\D/g, "");
        if (cnpj.length === 14) {
            // Exibe Swal de carregamento com tempo mínimo e máximo
            let swalLoading = Swal.fire({
                title: "Buscando CNPJ...",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                timer: 10000, // Tempo máximo de 10s
                timerProgressBar: true
            });

            let startTime = Date.now(); // Marca o tempo inicial

            try {
                let dataBrasil = await $.getJSON(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`);
                let elapsedTime = Date.now() - startTime; // Calcula o tempo decorrido

                // Aguarda pelo menos 1500ms antes de fechar o Swal
                setTimeout(() => {
                    preencherCnpjBrasil(dataBrasil);
                    Swal.close();
                }, Math.max(0, 1500 - elapsedTime));

            } catch (eBrasil) {
                console.warn("BrasilAPI falhou. Tentando openCNPJ...", eBrasil);
                try {
                    let dataOpen = await $.getJSON(`https://open.cnpja.com/office/${cnpj}`);
                    let elapsedTime = Date.now() - startTime;

                    // Aguarda pelo menos 1500ms antes de fechar o Swal
                    setTimeout(() => {
                        preencherCnpjOpen(dataOpen);
                        Swal.close();
                    }, Math.max(0, 1500 - elapsedTime));

                } catch (eOpen) {
                    console.error("Ambas as APIs falharam:", eOpen);
                    Swal.fire({
                        icon: "error",
                        title: "CNPJ Inválido",
                        text: "Não foi possível validar este CNPJ."
                    });
                }
            }
        }
    });



    function preencherCnpjBrasil(data) {
        if (data.erro) {
            Swal.fire({ icon: "error", title: "CNPJ Inválido", text: "Verifique o CNPJ digitado." });
            return;
        }
        $("#razaoSocial").val(data.razao_social || "");
        $("#nomeFantasia").val(data.nome_fantasia || "");
        // se veio cep => aciona a busca CEP
        if (data.cep) {
            $("#cepJuridico").val(data.cep).trigger("input");
        }
        // Preenche telefone
        if (data.ddd_telefone_1) {
            let ddd = data.ddd_telefone_1.slice(0, 2);
            let num = data.ddd_telefone_1.slice(2);
            $("#telefoneJuridico").val(`(${ddd}) ${num}`);
        }
        if (data.email) {
            $("#emailJuridico").val(data.email);
        }
        if (data.logradouro) {
            $("#logradouroJuridico").val(data.logradouro);
        }
        if (data.bairro) {
            $("#bairroJuridico").val(data.bairro);
        }
        if (data.numero) {
            $("#numeroJuridico").val(data.numero);
        }
        if (data.uf) {
            $("#estadoJuridico").val(data.uf);
            setChoiceValue($("#estadoJuridico"), data.uf);
            $("#estadoJuridico").trigger("change");
        }
        if (data.municipio) {
            // com pequeno delay
            setTimeout(() => {
                $("#cidadeJuridico").val(data.municipio);
                setChoiceValue($("#cidadeJuridico"), data.municipio);
            }, 400);
        }
    }

    function preencherCnpjOpen(data) {
        if (!data || data.error) {
            Swal.fire({
                icon: "error",
                title: "CNPJ Inválido",
                text: "Não foi possível validar este CNPJ (openCNPJ)."
            });
            return;
        }
        // Se retornar address + zip => aciona busca CEP
        if (data.address && data.address.zip) {
            $("#cepJuridico").val(data.address.zip).trigger("input");
        }
        if (data.address) {
            $("#logradouroJuridico").val(data.address.street || "");
            $("#bairroJuridico").val(data.address.district || "");
            $("#numeroJuridico").val(data.address.number || "");
            if (data.address.state) {
                $("#estadoJuridico").val(data.address.state);
                setChoiceValue($("#estadoJuridico"), data.address.state);
                $("#estadoJuridico").trigger("change");
            }
            if (data.address.city) {
                setTimeout(() => {
                    $("#cidadeJuridico").val(data.address.city);
                    setChoiceValue($("#cidadeJuridico"), data.address.city);
                }, 400);
            }
        }
        if (data.company && data.company.name) {
            $("#razaoSocial").val(data.company.name);
        }
        if (data.alias) {
            $("#nomeFantasia").val(data.alias);
        }
        if (data.emails && data.emails.length > 0) {
            $("#emailJuridico").val(data.emails[0].address || "");
        }
        if (data.phones && data.phones.length > 0) {
            let fone = data.phones[0];
            $("#telefoneJuridico").val(`(${fone.area}) ${fone.number}`);
        }
    }

    //--------------------------------------------------
    // 7) Form PF - Validação e Botão Salvar
    //--------------------------------------------------
    $("#formPessoaFisica").validate({
        rules: {
            nome: { required: true },
            cpf: { required: true },
            celular: { required: true },
        },
        messages: {
            nome: { required: "Informe o nome do cliente." },
            cpf: { required: "Informe o CPF." },
            celular: { required: "Informe o celular." },
        },
        errorClass: "text-danger small",
        errorElement: "span",
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        }
    });
    $("#salvarPessoaFisica").on("click", function () {
        if (!$("#formPessoaFisica").valid()) {
            Swal.fire({
                icon: "warning",
                title: "Atenção",
                text: "Preencha os campos obrigatórios corretamente."
            });
            return;
        }

        let formData = {
            tipo_cliente: "pessoa_fisica",
            nome: $("#nomeCliente").val(),
            cpf: $("#cpfCliente").val(),
            email: $("#emailCliente").val() || null,
            celular: $("#celularCliente").val() || null,
            cep: $("#cepCliente").val() || null,
            logradouro: $("#logradouroCliente").val() || null,
            numero: $("#numeroCliente").val() || null,
            bairro: $("#bairroCliente").val() || null,
            estado: $("#estadoCliente").val() || null,
            cidade: $("#cidadeCliente").val() || null
        };

        let podeFechar = false;
        const tempoMinimo = 1500;
        const tempoMaximo = 10000;

        Swal.fire({
            title: 'Salvando...',
            text: 'Aguarde enquanto cadastramos o cliente...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                setTimeout(() => { podeFechar = true; }, tempoMinimo);
                setTimeout(() => { if (Swal.isVisible()) Swal.close(); }, tempoMaximo);
            }
        });

        $.ajax({
            url: "/clientes",
            type: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            data: formData,
            success: function (resp) {
                const mostrarSucesso = () => {
                    Swal.fire("Sucesso!", resp.message || "Cliente PF cadastrado!", "success");
                    $("#formPessoaFisica")[0].reset();
                    $(document).trigger("clienteCadastrado", { tipo: "pessoa_fisica" });
                };

                if (podeFechar) {
                    Swal.close();
                    mostrarSucesso();
                } else {
                    setTimeout(() => {
                        Swal.close();
                        mostrarSucesso();
                    }, tempoMinimo);
                }
            },
            error: function (xhr) {
                const mostrarErro = () => {
                    let errorMsg = "Falha ao cadastrar cliente (PF).";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire("Erro!", errorMsg, "error");
                    console.error(xhr.responseText);
                };

                if (podeFechar) {
                    Swal.close();
                    mostrarErro();
                } else {
                    setTimeout(() => {
                        Swal.close();
                        mostrarErro();
                    }, tempoMinimo);
                }
            }
        });
    });


    //--------------------------------------------------
    // 8) Form PJ - Validação e Botão Salvar
    //--------------------------------------------------
    $("#formPessoaJuridica").validate({
        rules: {
            razao_social: { required: true },
            cnpj: { required: true },
            celular: { required: true }
        },
        messages: {
            razao_social: { required: "Informe a Razão Social." },
            cnpj: { required: "Informe o CNPJ." },
            celular: { required: "Informe o celular." }
        },
        errorClass: "text-danger small",
        errorElement: "span",
        errorPlacement: function (error, el) {
            error.insertAfter(el);
        }
    });

    $("#salvarPessoaJuridica").on("click", function () {
        if (!$("#formPessoaJuridica").valid()) {
            Swal.fire({
                icon: "warning",
                title: "Atenção",
                text: "Preencha os campos obrigatórios corretamente."
            });
            return;
        }

        let formData = {
            tipo_cliente: "pessoa_juridica",
            razao_social: $("#razaoSocial").val(),
            nome_fantasia: $("#nomeFantasia").val() || null,
            cnpj: $("#cnpjCliente").val(),
            telefone: $("#telefoneJuridico").val() || null,
            celular: $("#celularJuridico").val(),
            email: $("#emailJuridico").val() || null,
            cep: $("#cepJuridico").val() || null,
            logradouro: $("#logradouroJuridico").val() || null,
            numero: $("#numeroJuridico").val() || null,
            bairro: $("#bairroJuridico").val() || null,
            estado: $("#estadoJuridico").val() || null,
            cidade: $("#cidadeJuridico").val() || null
        };

        let podeFechar = false;
        const tempoMinimo = 1500;
        const tempoMaximo = 10000;

        Swal.fire({
            title: 'Salvando...',
            text: 'Aguarde enquanto cadastramos o cliente...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                setTimeout(() => { podeFechar = true; }, tempoMinimo);
                setTimeout(() => { if (Swal.isVisible()) Swal.close(); }, tempoMaximo);
            }
        });

        $.ajax({
            url: "/clientes",
            type: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            data: formData,
            success: function (resp) {
                const mostrarSucesso = () => {
                    Swal.fire("Sucesso!", resp.message || "Cliente PJ cadastrado!", "success");
                    $("#formPessoaJuridica")[0].reset();
                };

                if (podeFechar) {
                    Swal.close();
                    mostrarSucesso();
                } else {
                    setTimeout(() => {
                        Swal.close();
                        mostrarSucesso();
                    }, tempoMinimo);
                }
            },
            error: function (xhr) {
                const mostrarErro = () => {
                    let errorMsg = "Falha ao cadastrar cliente (PJ).";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire("Erro!", errorMsg, "error");
                    console.error(xhr.responseText);
                };

                if (podeFechar) {
                    Swal.close();
                    mostrarErro();
                } else {
                    setTimeout(() => {
                        Swal.close();
                        mostrarErro();
                    }, tempoMinimo);
                }
            }
        });
    });
});

$(function () {
    // 1) Quando qualquer card for expandido:
    //    fecha os demais (caso tenha vários).
    $('.card[data-card-widget="collapse"]').on('expanded.lte.cardwidget', function () {
        var cardAberto = this;
        $('.card[data-card-widget="collapse"]').each(function () {
            if (this !== cardAberto) {
                // Fecha os outros
                $(this).CardWidget('collapse');
            }
        });
    });

    // 2) Quando o card de "Novo Cliente" for fechado,
    //    voltamos o select tipoCliente para "Selecione".
    $('#cardNovoCliente .card[data-card-widget="collapse"]').on('collapsed.lte.cardwidget', function () {
        // Se você usa Choices.js:
        const instance = $('#tipoCliente').data('choicesInstance');
        if (instance) {
            instance.setChoiceByValue('');
        } else {
            // Se não tiver Choices, basta resetar com jQuery normal
            $('#tipoCliente').val('');
        }

        // Esconde os dois forms, se quiser zerar tela
        $('.cliente-form').addClass('d-none');
        // Poderia também resetar ambos, se desejar
        // $('#formPessoaFisica, #formPessoaJuridica')[0].reset();
    });

    // 3) Quando o card de "Lista de Clientes" for fechado,
    //    voltamos o select tipoClienteListagem para "Selecione".
    $('#cardListarClientes .card[data-card-widget="collapse"]').on('collapsed.lte.cardwidget', function () {
        // Se você usa Choices.js:
        const instanceListagem = $('#tipoClienteListagem').data('choicesInstance');
        if (instanceListagem) {
            instanceListagem.setChoiceByValue('');
        } else {
            // Se não tiver Choices, basta resetar com jQuery
            $('#tipoClienteListagem').val('');
        }

        // Se quiser, pode limpar a tabela ou fazer outro comportamento...
    });
});



