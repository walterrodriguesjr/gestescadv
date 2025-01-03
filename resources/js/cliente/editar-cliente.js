// Abre o modal de edição
$(document).on("click", "#abrirModalEditarCliente", function (e) {
    e.preventDefault();

    const clienteId = $(this).data("id"); // Captura o ID do cliente do botão
    $("#clienteIdEditar").val(clienteId); // Armazena o ID no campo oculto

    // Faz a requisição AJAX para buscar os dados do cliente
    $.ajax({
        type: "GET",
        url: `/cliente/${clienteId}`,
        success: function (response) {
            // Preenche os campos do modal com os dados do cliente
            $("#clienteNomeCompletoEditar").val(response.cliente_nome_completo);
            $("#clienteCpfEditar").val(response.cliente_cpf); // CPF permanece desativado
            $("#clienteEmailEditar").val(response.cliente_email);
            $("#clienteCelularEditar").val(response.cliente_celular);
            $("#clienteTelefoneEditar").val(response.cliente_telefone);
            $("#clienteCepEditar").val(response.cliente_cep);
            $("#clienteRuaEditar").val(response.cliente_rua);
            $("#clienteNumeroEditar").val(response.cliente_numero);
            $("#clienteBairroEditar").val(response.cliente_bairro);

            // Carrega estados e, ao terminar, preenche o estado do cliente
            carregarEstados(() => {
                $("#clienteEstadoEditar").val(response.cliente_estado).trigger("change");

                // Carrega cidades e, ao terminar, preenche a cidade do cliente
                carregarCidades(response.cliente_estado, () => {
                    $("#clienteCidadeEditar").val(response.cliente_cidade).trigger("change");
                });
            });

            // Abre o modal
            $("#clienteModalEditar").modal("show");
        },
        error: function () {
            toastr.error("Erro ao buscar os dados do cliente. Tente novamente.");
        },
    });
});

// Clique no botão de salvar as alterações
$("#salvarClienteEditar").click(function (e) {
    e.preventDefault();

    const clienteId = $("#clienteIdEditar").val(); // Recupera o ID do campo oculto

    const dadosAtualizados = {
        nome_completo: $("#clienteNomeCompletoEditar").val(),
        email: $("#clienteEmailEditar").val(),
        celular: $("#clienteCelularEditar").val(),
        telefone: $("#clienteTelefoneEditar").val(),
        cep: $("#clienteCepEditar").val(),
        rua: $("#clienteRuaEditar").val(),
        numero: $("#clienteNumeroEditar").val(),
        bairro: $("#clienteBairroEditar").val(),
        estado: $("#clienteEstadoEditar").val(),
        cidade: $("#clienteCidadeEditar").val(),
        _token: $('meta[name="csrf-token"]').attr("content"), // CSRF Token
    };

    // Exibe o spinner
    $("#editarSpinner").removeClass("d-none");

    // Faz a requisição PUT para atualizar os dados do cliente
    $.ajax({
        type: "PUT",
        url: `/cliente/${clienteId}`, // Passa o ID na URL
        data: dadosAtualizados,
        success: function () {
            toastr.success("Cliente atualizado com sucesso!");

            // Fecha o modal e atualiza a tabela
            $("#clienteModalEditar").modal("hide");
            listarClientes();
            // Remove o spinner após 1 segundos
            setTimeout(() => {
                $("#editarSpinner").addClass("d-none");
            }, 1000);
        },
        error: function (xhr) {
            $("#editarSpinner").addClass("d-none");
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                for (const key in errors) {
                    if (errors.hasOwnProperty(key)) {
                        exibirErro(`#${key}`, errors[key][0]);
                    }
                }
            } else {
                toastr.error("Erro ao atualizar os dados do cliente. Tente novamente.");
            }
        },
    });
});

// Preenchimento dinâmico dos dados de endereço com base no CEP digitado
$("#clienteCepEditar").change(function (e) {
    e.preventDefault();
    let cep = $(this).val().replace(/\D/g, "");

    if (cep.length === 8) {
        $.ajax({
            type: "GET",
            url: `https://viacep.com.br/ws/${cep}/json/`,
            dataType: "json",
            success: function (data) {
                if (data.erro) {
                    toastr.error("CEP não localizado.");
                } else {
                    $("#clienteRuaEditar").val(data.logradouro);
                    $("#clienteBairroEditar").val(data.bairro);

                    // Preencher Estado e Cidade
                    let estado = data.uf;
                    let cidade = data.localidade;

                    $("#clienteEstadoEditar").val(estado).trigger("change");

                    carregarCidades(estado, function () {
                        $("#clienteCidadeEditar").val(cidade).trigger("change");
                    });
                }
            },
            error: function () {
                toastr.error("Erro ao buscar o CEP. Tente novamente mais tarde.");
            },
        });
    } else {
        toastr.error("CEP inválido. Por favor, insira um CEP válido.");
    }
});

// Configuração do Select2 para estado e cidade
$("#clienteEstadoEditar").select2({
    placeholder: "Selecione o Estado",
    allowClear: true,
    width: "100%",
}).on("change", function () {
    let estado = $(this).val();
    carregarCidades(estado); // Carrega cidades quando o estado muda
});

$("#clienteCidadeEditar").select2({
    placeholder: "Selecione a Cidade",
    allowClear: true,
    width: "100%",
    disabled: true, // Inicialmente desabilitado
});

// Carrega os estados no select
function carregarEstados(callback) {
    $.ajax({
        type: "GET",
        url: "https://servicodados.ibge.gov.br/api/v1/localidades/estados",
        dataType: "json",
        success: function (data) {
            const estadoSelect = $("#clienteEstadoEditar");
            estadoSelect.empty(); // Limpa o select antes de adicionar
            estadoSelect.append('<option value="" disabled selected>Selecione o Estado</option>');
            data.forEach(function (estado) {
                estadoSelect.append(`<option value="${estado.sigla}">${estado.nome}</option>`);
            });

            if (callback) callback(); // Executa o callback se definido
        },
        error: function () {
            toastr.error("Erro ao carregar os estados. Tente novamente mais tarde.");
        },
    });
}

// Carrega as cidades no select com base no estado selecionado
function carregarCidades(estado, callback) {
    const cidadeSelect = $("#clienteCidadeEditar");

    if (estado) {
        cidadeSelect.prop("disabled", true).empty().append('<option>Carregando...</option>');

        $.ajax({
            type: "GET",
            url: `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estado}/municipios`,
            dataType: "json",
            success: function (data) {
                cidadeSelect.prop("disabled", false).empty().append('<option value="" disabled selected>Selecione a Cidade</option>');
                data.forEach(function (cidade) {
                    cidadeSelect.append(`<option value="${cidade.nome}">${cidade.nome}</option>`);
                });

                if (callback) callback(); // Executa o callback se definido
            },
            error: function () {
                toastr.error("Erro ao carregar as cidades. Tente novamente mais tarde.");
                cidadeSelect.prop("disabled", false);
            },
        });
    } else {
        cidadeSelect.prop("disabled", true).empty().append('<option value="" disabled selected>Selecione a Cidade</option>');
    }
}

// INÍCIO uso de máscaras e manipulações nos inputs do modal de editar cliente
$(document).ready(function () {
    // Transformar texto para que cada palavra comece com letra maiúscula
    $('#clienteNomeCompletoEditar').on('input', function () {
        const valorAtual = $(this).val();
        const transformado = valorAtual.replace(/\w\S*/g, function (texto) {
            return texto.charAt(0).toUpperCase() + texto.slice(1).toLowerCase();
        });
        $(this).val(transformado);
    });

    $('#clienteRuaEditar').on('input', function () {
        const valorAtual = $(this).val();
        const transformado = valorAtual.replace(/\w\S*/g, function (texto) {
            return texto.charAt(0).toUpperCase() + texto.slice(1).toLowerCase();
        });
        $(this).val(transformado);
    });

    // Permitir apenas números no campo de número
    $('#clienteNumeroEditar').on('keypress', function (e) {
        if (e.which < 48 || e.which > 57) {
            e.preventDefault();
        }
    });

    // Aplicação de máscaras nos campos específicos
    $('#clienteCpfEditar').mask('000.000.000-00', { reverse: true });
    $('#clienteCelularEditar').mask('(00) 00000-0000');
    $('#clienteTelefoneEditar').mask('(00) 0000-0000');
    $('#clienteCepEditar').mask('00000-000');
});
// FIM uso de máscaras e manipulações nos inputs do modal de editar cliente

