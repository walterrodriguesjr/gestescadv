$(document).ready(function () {
    window.abrirObservacaoSwal = function (servicoId, etapa, dataHora) {
        const dataFormatada = moment(dataHora).format("YYYY-MM-DD HH:mm:ss");

        $.ajax({
            url: `/andamentos/${servicoId}/buscar-observacoes`,
            method: 'GET',
            data: {
                etapa: etapa,
                data_hora: dataFormatada
            },
            success: function (resposta) {
                Swal.fire({
                    title: `Observações - ${etapa}`,
                    input: 'textarea',
                    inputLabel: 'Digite suas observações:',
                    inputPlaceholder: 'Ex: Detalhes da reunião, exames solicitados, etc.',
                    inputValue: resposta.descricao || '',
                    inputAttributes: {
                        'aria-label': 'Digite suas observações aqui'
                    },
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-check"></i> Salvar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    buttonsStyling: false,
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-primary ml-2',
                        cancelButton: 'btn btn-secondary'
                    },
                    preConfirm: (descricao) => {
                        if (!descricao.trim()) {
                            Swal.showValidationMessage('Você precisa digitar algo.');
                            return false;
                        }

                        const tempoMinimo = 1500;
                        const inicio = Date.now();

                        return new Promise((resolve, reject) => {
                            Swal.fire({
                                title: 'Salvando...',
                                text: 'Aguarde enquanto salvamos a observação.',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => Swal.showLoading()
                            });

                            $.ajax({
                                url: `/andamentos/${servicoId}/atualizar-observacoes`,
                                method: 'PUT',
                                contentType: 'application/json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: JSON.stringify({
                                    etapa: etapa,
                                    descricao: descricao,
                                    data_hora: dataFormatada
                                }),
                                success: () => {
                                    const tempo = Date.now() - inicio;
                                    const atraso = tempoMinimo - tempo;

                                    setTimeout(() => {
                                        resolve();
                                    }, atraso > 0 ? atraso : 0);
                                },
                                error: (xhr) => {
                                    let msg = xhr.responseJSON?.message || 'Erro ao salvar observação.';
                                    Swal.showValidationMessage(msg);
                                    reject();
                                }
                            });
                        });
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: 'A observação foi atualizada com sucesso.'
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao carregar observações anteriores.',
                    confirmButtonText: 'Fechar',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            }
        });
    };
});
