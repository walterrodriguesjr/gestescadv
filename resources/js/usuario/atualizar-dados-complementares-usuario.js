
//INICIO acao salvar dados complementares
$(document).ready(function () {
    $('#btn-salvar-dados-complementares').on('click', function () {
        // Obter os dados do formulário
        const csrfToken = $('meta[name="csrf-token"]').attr('content'); // Token CSRF
        const cpf = $('#cpf').val();
        const celular = $('#celular').val();

        // Enviar dados via AJAX
        $.ajax({
            url: '/usuario-user-data', // URL da rota
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken, // Token CSRF para proteção
            },
            data: {
                cpf: cpf,
                celular: celular,
            },
            success: function (response) {
                if (response.success) {
                    // Atualizar os campos com os dados salvos
                    $('#cpf').val(response.data.user_cpf);
                    $('#celular').val(response.data.user_celular);

                    window.dispatchEvent(new Event('salvo'));
                } else {
                    // Exibir mensagem de erro
                    alert('Erro: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                // Exibir erros do servidor
                alert('Erro ao salvar os dados: ' + xhr.responseJSON.message);
            },
        });
    });
});
//FIM acao salvar dados complementares
