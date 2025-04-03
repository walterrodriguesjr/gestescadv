// Evento para edição com jQuery e AJAX
$('#tabelaTipoServicos').on('click', '.editar-servico', function () {
    const id = $(this).data('id');

    $.ajax({
        url: `/buscar_tipo_servicos/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (resp) {
            if (resp.success && resp.data) {
                const nomeAtual = resp.data.nome_servico;

                Swal.fire({
                    title: 'Editar Tipo de Serviço',
                    html: $('<div>').append(
                        $('<p>').html(`Todos os serviços inicializados ou concluídos com o tipo de serviço <strong>"${nomeAtual}"</strong> também serão atualizados.`),
                        $('<input>')
                            .attr({
                                type: 'text',
                                id: 'inputNovoNome',
                                class: 'swal2-input',
                                placeholder: 'Novo nome'
                            })
                            .val(nomeAtual)
                            .on('input', function () {
                                let valor = $(this).val().toLowerCase();
                                $(this).val(valor.charAt(0).toUpperCase() + valor.slice(1));
                            })
                    ),
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-check"></i> Atualizar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    buttonsStyling: false,
                    reverseButtons: true, // <-- isso inverte a ordem
                    customClass: {
                        confirmButton: 'btn btn-success ml-2',
                        cancelButton: 'btn btn-secondary'
                    },
                    focusConfirm: false,
                    preConfirm: () => {
                        const novoNome = $('#inputNovoNome').val().trim();
                        if (!novoNome) {
                            Swal.showValidationMessage('O nome não pode estar vazio.');
                        }
                        return novoNome;
                    }
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        atualizarTipoServico(id, result.value);
                    }
                });

            } else {
                Swal.fire('Erro', resp.message || 'Não foi possível carregar os dados.', 'error');
            }
        },
        error: function (xhr) {
            Swal.fire('Erro', xhr.responseJSON?.message || 'Erro ao buscar tipo de serviço.', 'error');
        }
    });
});

// Função AJAX para PUT (atualizar)
function atualizarTipoServico(id, novoNome) {
    Swal.fire({
        title: 'Atualizando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    $.ajax({
        url: `/atualizar_tipo_servico/${id}`,
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { nome_servico: novoNome },
        success: function (resp) {
            Swal.fire('Sucesso!', resp.message || 'Serviço atualizado com sucesso.', 'success');
            $('#tabelaTipoServicos').DataTable().ajax.reload();
        },
        error: function (xhr) {
            let msg = 'Erro ao atualizar.';
            if (xhr.status === 422 && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).join("<br>");
            } else if (xhr.responseJSON?.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire('Erro!', msg, 'error');
        }
    });
}
