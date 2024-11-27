// INÍCIO click para abrir modal de cadastrar cliente
$("#abrirModalCadastrarCliente").click(function (e) {
    e.preventDefault();
    $("#clienteModalCadastrar").modal("show");
    carregarEstados();

    //INICIO carregamento de cidades com base no estado selecionado
    $("#clienteEstado").on("change", function () {
        let estado = $(this).val();
        carregarCidades(estado);
    });
    // FIM carregamento de cidades com base no estado selecionado
});
// FIM click para abrir modal de cadastrar cliente


// INÍCIO preenchimento dinâmico dos dados de endereço com base no cep digitado, consulta API viacep
$("#clienteCep").change(function (e) {
    e.preventDefault();
    let cep = $(this).val().replace(/\D/g, '');

    if (cep.length === 8) {
        $.ajax({
            type: "GET",
            url: `https://viacep.com.br/ws/${cep}/json/`,
            dataType: "json",
            success: function (data) {
                if (data.erro) {
                    alert("CEP não localizado.");
                } else {
                    console.log("dados", data);
                    $("#clienteRua").val(data.logradouro);
                    $("#clienteBairro").val(data.bairro);

                    let estado = data.uf;
                    let estadoSelect = $("#clienteEstado");
                    estadoSelect.val(estado).trigger('change'); // Seleciona o estado automaticamente

                    let cidade = data.localidade;
                    let cidadeSelect = $("#clienteCidade");
                    cidadeSelect.empty(); // Limpa o select antes de adicionar
                    cidadeSelect.append(`<option value="${cidade}" selected>${cidade}</option>`);
                }
            },
            error: function () {
                alert("Erro ao buscar o CEP. Tente novamente mais tarde.");
            }
        });
    } else {
        alert("CEP inválido. Por favor, insira um CEP válido.");
    }
});
// FIM preenchimento dinâmico dos dados de endereço com base no cep digitado, consulta API viacep


// INÍCIO configuração do Select2 para estado e cidade
$('#clienteEstado').select2({
    placeholder: 'Selecione o Estado',
    allowClear: true,
    minimumResultsForSearch: 0,
    width: '100%'
});

$('#clienteCidade').select2({
    placeholder: 'Selecione a Cidade',
    allowClear: true,
    minimumResultsForSearch: 0,
    width: '100%',
    disabled: true // Inicialmente desabilitado
});
// FIM configuração do Select2 para estado e cidade


// INÍCIO carregamento de estados via API do IBGE
function carregarEstados() {
    $.ajax({
        type: "GET",
        url: "https://servicodados.ibge.gov.br/api/v1/localidades/estados",
        dataType: "json",
        success: function (data) {
            let estadoSelect = $("#clienteEstado");
            estadoSelect.empty(); // Limpa o select antes de adicionar
            estadoSelect.append('<option value="" disabled selected>Selecione o Estado</option>');
            data.forEach(function (estado) {
                estadoSelect.append(`<option value="${estado.sigla}">${estado.nome}</option>`);
            });
        },
        error: function () {
            alert("Erro ao carregar os estados. Tente novamente mais tarde.");
        }
    });
};
// FIM carregamento de estados via API do IBGE


// INÍCIO carregamento de cidades com base no estado selecionado
function carregarCidades(estado) {
    let cidadeSelect = $("#clienteCidade");

    if (estado) {
        cidadeSelect.prop('disabled', true).empty().append('<option>Carregando...</option>');

        $.ajax({
            type: "GET",
            url: `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estado}/municipios`,
            dataType: "json",
            success: function (data) {
                cidadeSelect.prop('disabled', false).empty().append('<option value="" disabled selected>Selecione a Cidade</option>');
                data.forEach(function (cidade) {
                    cidadeSelect.append(`<option value="${cidade.nome}">${cidade.nome}</option>`);
                });
            },
            error: function () {
                alert("Erro ao carregar as cidades. Tente novamente mais tarde.");
                cidadeSelect.prop('disabled', false);
            }
        });
    } else {
        cidadeSelect.prop('disabled', true).empty().append('<option value="" disabled selected>Selecione a Cidade</option>');
    }
};
// SIM carregamento de cidades com base no estado selecionado



