$(document).ready(function () {
    window.abrirModalNumeroProcesso = function (servicoId, numeroAtual) {
        const isEdicao = !!numeroAtual;

        Swal.fire({
            title: isEdicao ? 'Editar Número do Processo' : 'Inserir Número do Processo',
            input: 'text',
            inputLabel: 'Número do processo (formato CNJ):',
            inputPlaceholder: '0000000-00.0000.0.00.0000',
            inputValue: numeroAtual || '',
            inputAttributes: {
                maxlength: 25,
                style: 'text-transform:uppercase',
            },
            showCancelButton: true,
            confirmButtonText: isEdicao ? '<i class="fas fa-sync-alt"></i> Atualizar' : '<i class="fas fa-save"></i> Salvar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            reverseButtons: true,
            customClass: {
                confirmButton: isEdicao ? 'btn btn-success ml-2' : 'btn btn-primary ml-2',
                cancelButton: 'btn btn-secondary'
            },
            didOpen: () => {
                const input = Swal.getInput();
                $(input).mask('0000000-00.0000.0.00.0000');
                input.focus();
            },
            preConfirm: (numero) => {
                if (!numero || !numero.trim()) {
                    Swal.showValidationMessage('Informe o número do processo.');
                    return false;
                }

                const tempoMinimo = 1500;
                const inicio = Date.now();

                return new Promise((resolve, reject) => {
                    Swal.fire({
                        title: isEdicao ? 'Atualizando...' : 'Salvando...',
                        text: 'Aguarde enquanto o número é processado.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: `/servicos/${servicoId}/numero-processo`,
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            numero_processo: numero
                        },
                        success: () => {
                            const tempo = Date.now() - inicio;
                            const atraso = tempoMinimo - tempo;

                            setTimeout(() => {
                                resolve();
                            }, atraso > 0 ? atraso : 0);
                        },
                        error: (xhr) => {
                            const msg = xhr.responseJSON?.message || 'Erro ao salvar número do processo.';
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
                    text: isEdicao ? 'Número do processo atualizado com sucesso.' : 'Número do processo salvo com sucesso.'
                }).then(() => location.reload());
            }
        });
    };
});
