$(document).on("click", "#abrirModalVisualizarCliente", function (e) {
    e.preventDefault();

    const clienteId = $(this).data("id"); // Obtém o ID do cliente

    // Faz a requisição AJAX para buscar os dados do cliente
    $.ajax({
        type: "GET",
        url: `/cliente/${clienteId}`,
        success: function (response) {
            // Preenche os campos do modal com os dados do cliente
            preencherCamposModal(response);

            // Atualiza o ícone e o link do WhatsApp
            atualizarWhatsAppLink(response.cliente_celular);

            // Atualiza o link do Google Maps
            atualizarGoogleMapsLink(response);

            // Abre o modal
            $("#clienteModalVisualizar").modal("show");
        },
        error: function () {
            toastr.error("Erro ao buscar os dados do cliente. Tente novamente.");
        },
    });
});

// Função para preencher os campos do modal
function preencherCamposModal(cliente) {
    $("#clienteNomeCompletoVisualizar").val(cliente.cliente_nome_completo);
    $("#clienteCpfVisualizar").val(cliente.cliente_cpf);
    $("#clienteEmailVisualizar").val(cliente.cliente_email);
    $("#clienteCelularVisualizar").val(cliente.cliente_celular);
    $("#clienteTelefoneVisualizar").val(cliente.cliente_telefone);
    $("#clienteCepVisualizar").val(cliente.cliente_cep);
    $("#clienteRuaVisualizar").val(cliente.cliente_rua);
    $("#clienteNumeroVisualizar").val(cliente.cliente_numero);
    $("#clienteBairroVisualizar").val(cliente.cliente_bairro);
    $("#clienteEstadoVisualizar").val(cliente.cliente_estado);
    $("#clienteCidadeVisualizar").val(cliente.cliente_cidade);
}

// Função para atualizar o link do WhatsApp
function atualizarWhatsAppLink(celular) {
    const whatsappLink = $("#whatsappLink");

    if (celular) {
        const numeroFormatado = celular.replace(/\D/g, ""); // Remove caracteres não numéricos
        if (numeroFormatado.length >= 10) { // Verifica se é um número válido
            const whatsappUrl = `https://wa.me/55${numeroFormatado}`; // Link do WhatsApp com o número formatado
            whatsappLink.attr("href", whatsappUrl); // Atualiza o link do href
            whatsappLink.removeClass("d-none"); // Mostra o ícone do WhatsApp
        } else {
            whatsappLink.addClass("d-none"); // Esconde o ícone do WhatsApp se o número for inválido
        }
    } else {
        whatsappLink.addClass("d-none"); // Esconde o ícone se o celular estiver vazio
    }
}

// Função para atualizar o link do Google Maps
function atualizarGoogleMapsLink(cliente) {
    const googleMapsLink = $("#googleMapsLink");
    const endereco = `${cliente.cliente_rua}, ${cliente.cliente_numero}, ${cliente.cliente_bairro}, ${cliente.cliente_cidade}, ${cliente.cliente_estado}, ${cliente.cliente_cep}`;

    if (endereco.trim()) {
        const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(endereco)}`;
        googleMapsLink.attr("href", googleMapsUrl); // Atualiza o link do href
        googleMapsLink.removeClass("d-none"); // Mostra o link do Google Maps
    } else {
        googleMapsLink.addClass("d-none"); // Esconde o link se o endereço for inválido
    }
}

// Atualiza o link do WhatsApp ao editar manualmente o campo do celular
$(document).on("input", "#clienteCelularVisualizar", function () {
    const celular = $(this).val();
    atualizarWhatsAppLink(celular);
});
