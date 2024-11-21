// INÍCIO click para abrir modal de cadastrar cliente
$("#abrirModalCadastrarCliente").click(function (e) {
    e.preventDefault();
    $("#clienteModalCadastrar").modal("show");
});
// FIM click para abrir modal de cadastrar cliente


//INÍCIO preenchimento dinâmico dos dados de endereço com base no cep digitado, consulta API viacep
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
                    estadoSelect.empty();
                    estadoSelect.append(`<option value="${estado}" selected>${estado}</option>`);

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
//FIM preenchimento dinâmico dos dados de endereço com base no cep digitado, consulta API viacep

