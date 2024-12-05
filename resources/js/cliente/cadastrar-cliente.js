
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
                    $("#clienteRua").val(data.logradouro);
                    $("#clienteBairro").val(data.bairro);

                    // Preencher Estado e Cidade
                    let estado = data.uf;
                    let cidade = data.localidade;

                    // Atualiza o Estado no Select2
                    let estadoSelect = $("#clienteEstado");
                    estadoSelect.val(estado).trigger('change'); // Seleciona o estado automaticamente no select2

                    // Aguarda o carregamento das cidades antes de selecionar
                    carregarCidades(estado, function () {
                        let cidadeSelect = $("#clienteCidade");
                        cidadeSelect.empty().append(`<option value="${cidade}" selected>${cidade}</option>`);
                        cidadeSelect.val(cidade).trigger('change'); // Atualiza o Select2
                    });
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
}).on('change', function () {
    let estado = $(this).val();
    carregarCidades(estado); // Carrega cidades quando o estado muda
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
function carregarCidades(estado, callback) {
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

                if (callback) callback(); // Executa callback se definido
            },
            error: function () {
                alert("Erro ao carregar as cidades. Tente novamente mais tarde.");
                cidadeSelect.prop('disabled', false);
            }
        });
    } else {
        cidadeSelect.prop('disabled', true).empty().append('<option value="" disabled selected>Selecione a Cidade</option>');
    }
}
// FIM carregamento de cidades com base no estado selecionado


//INICIO salvar dados novo cliente

$("#buttonSalvarDadosNovoCliente").click(function (e) {
    e.preventDefault();
    
});

//FIM salvar dados novo cliente

//INICIO uso de mascaras nos inputs do modal de cadastrar novo cliente
$(document).ready(function () {
    $('#clienteNomeCompleto').on('input', function () {
        const valorAtual = $(this).val();
        const transformado = valorAtual.replace(/\w\S*/g, function (texto) {
            return texto.charAt(0).toUpperCase() + texto.slice(1).toLowerCase();
        });
        $(this).val(transformado);
    });

    $('#clienteRua').on('input', function () {
        const valorAtual = $(this).val();
        const transformado = valorAtual.replace(/\w\S*/g, function (texto) {
            return texto.charAt(0).toUpperCase() + texto.slice(1).toLowerCase();
        });
        $(this).val(transformado);
    });

    $('#clienteNumero').on('keypress', function (e) {
        if (e.which < 48 || e.which > 57) {
            e.preventDefault();
        }
    });
    
        $('#clienteCpf').mask('000.000.000-00', { reverse: true });
        $('#clienteCelular').mask('(00) 00000-0000');
        $('#clienteTelefone').mask('(00) 0000-0000');
        $('#clienteCep').mask('00000-000');
        
    });
    
    //FIM uso de mascaras nos inputs do modal de cadastrar novo cliente




