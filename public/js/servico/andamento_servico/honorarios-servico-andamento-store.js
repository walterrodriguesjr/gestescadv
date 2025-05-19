// Arquivo: public/js/servico/andamento_servico/honorarios-servico-andamento-store.js

$(document).ready(function () {
    window.abrirModalHonorarios = function (servicoId) {
        $('#formCadastrarHonorario')[0].reset();
        $('#modalHonorarios').modal('show');
        $('#valorHonorario').mask('000.000.000,00', { reverse: true });
        $('#dataRecebimentoHonorario').val(moment().format('DD/MM/YYYY'));
        $('#formCadastrarHonorario').data('servico-id', servicoId);
        carregarHonorariosTabela(servicoId);
    };

    $('#formCadastrarHonorario').on('submit', function (e) {
        e.preventDefault();

        const valor = $('#valorHonorario').val().trim();
        const dataRecebimento = $('#dataRecebimentoHonorario').val().trim();
        const arquivo = $('#arquivoComprovante')[0].files[0];
        const observacoes = $('#observacoesHonorario').val().trim();
        const servicoId = $(this).data('servico-id');

        if (!valor || !dataRecebimento) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                text: 'Preencha todos os campos obrigatórios: valor e data de recebimento.',
                confirmButtonText: '<i class="fas fa-times"></i> Fechar',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });
            return;
        }

        const formData = new FormData();
        formData.append('valor', valor);
        formData.append('data_recebimento', dataRecebimento);
        formData.append('servico_id', servicoId);
        formData.append('cliente_id', clienteId);
        formData.append('escritorio_id', escritorioId);
        formData.append('observacoes', observacoes);
        if (arquivo) {
            formData.append('comprovante', arquivo);
        }

        Swal.fire({
            title: 'Salvando...',
            text: 'Aguarde enquanto salvamos o honorário...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            timerProgressBar: true,
            didOpen: () => Swal.showLoading()
        });

        const inicio = Date.now();

        $.ajax({
            url: '/honorarios',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            data: formData,
            success: function () {
                const atraso = 1500 - (Date.now() - inicio);
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Honorário cadastrado com sucesso.',
                        confirmButtonText: '<i class="fas fa-times"></i> Fechar',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    });


                    $('#formCadastrarHonorario')[0].reset();
                    carregarHonorariosTabela(servicoId);
                }, atraso > 0 ? atraso : 0);
            },
            error: function (xhr) {
                const atraso = 1500 - (Date.now() - inicio);
                setTimeout(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao salvar',
                        text: xhr.responseJSON?.message || 'Erro ao salvar honorário.',
                        confirmButtonText: '<i class="fas fa-times"></i> Fechar',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    });
                }, atraso > 0 ? atraso : 0);
            }
        });
    });


    function carregarHonorariosTabela(servicoId) {
        $.ajax({
            url: `/honorarios/${servicoId}/listar`,
            method: 'GET',
            success: function (data) {
                const tbody = $('#tabelaHonorarios tbody');
                tbody.empty();

                if (!data.length) {
                    tbody.append('<tr><td colspan="5" class="text-center text-muted">Nenhum honorário cadastrado.</td></tr>');
                    return;
                }

                data.forEach(honorario => {
                    const comprovanteBtn = honorario.comprovante_url
                        ? `<button class="btn btn-sm btn-dark" onclick="abrirComprovanteHonorario('${honorario.comprovante_url}')">Visualizar</button>`
                        : '—';

                    const observacoesBtn = honorario.observacoes
                        ? `<button class="btn btn-sm btn-dark" onclick="verObservacoesHonorario('${honorario.observacoes.replace(/'/g, "\\'")}')">Visualizar</button>`
                        : '—';

                    const dataRecebimento = honorario.data_recebimento || '—';

                    const linha = `
                <tr>
                    <td>R$ ${honorario.valor_formatado}</td>
                    <td>${comprovanteBtn}</td>
                    <td>${observacoesBtn}</td>
                    <td>${dataRecebimento}</td>
                    <td>
                        <button class="btn btn-sm btn-success"
                                onclick="abrirSwalEditarHonorario(${honorario.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger excluir-honorario" data-id="${honorario.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;

                    tbody.append(linha);
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao carregar os honorários.',
                    confirmButtonText: 'Fechar',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });
            }
        });
    }
});

function verObservacoesHonorario(observacoes) {
    Swal.fire({
        title: 'Observações',
        html: `<p class="text-start">${observacoes}</p>`,
        icon: 'info',
        confirmButtonText: '<i class="fas fa-times"></i> Fechar',
        customClass: {
            confirmButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    });
}

function abrirComprovanteHonorario(url) {
    Swal.fire({
        title: 'Comprovante',
        html: `<iframe src="${url}" width="100%" height="500px" style="border:none;"></iframe>`,
        confirmButtonText: '<i class="fas fa-times"></i> Fechar',
        customClass: {
            confirmButton: 'btn btn-secondary'
        },
        buttonsStyling: false,
        width: '80%'
    });
}


