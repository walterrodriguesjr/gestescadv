function abrirSwalEditarHonorario(honorarioId) {
    const $modalBootstrap = $('#modalHonorarios');

    $modalBootstrap.modal('hide');

    $modalBootstrap.one('hidden.bs.modal', () => {
        exibirSwalEdicao(honorarioId, $modalBootstrap);
    });
}

function exibirSwalEdicao(honorarioId, $modalBootstrap) {
    $.get(`/honorarios/${honorarioId}/editar`, resp => {
        const {
            valor,
            data_recebimento,
            observacoes,
            comprovante_url,
            comprovante_nome
        } = resp;

        let comprovanteHtml = '';
        if (comprovante_url) {
            comprovanteHtml = `
                <div class="form-group text-left">
                    <label>Comprovante Atual</label>
                    <div class="d-flex align-items-center justify-content-between border rounded p-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-pdf text-danger mr-2" style="font-size:1.5rem;"></i>
                            <a href="${comprovante_url}" target="_blank" class="font-weight-bold text-dark">
                                ${comprovante_nome}
                            </a>
                        </div>
                        <a href="${comprovante_url}" target="_blank" class="btn btn-sm btn-outline-secondary ml-2">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>`;
        }

        Swal.fire({
            title: 'Editar Honorário',
            html: `
                <form id="formAtualizarHonorario" enctype="multipart/form-data">
                    <input type="hidden" name="honorario_id" value="${honorarioId}">
                    <div class="form-group text-left">
                        <label>Valor</label>
                        <input type="text" name="valor" class="form-control" value="${valor || ''}">
                    </div>
                    <div class="form-group text-left">
                        <label>Data de Recebimento</label>
                        <input type="text" name="data_recebimento" class="form-control"
                               value="${data_recebimento || ''}" placeholder="dd/mm/aaaa" maxlength="10">
                    </div>
                    <div class="form-group text-left">
                        <label>Observações</label>
                        <textarea name="observacoes" class="form-control" rows="3">${observacoes || ''}</textarea>
                    </div>
                    ${comprovanteHtml}
                    <div class="form-group text-left mt-3">
                        <label>Substituir Comprovante</label>
                        <input type="file" name="comprovante" class="form-control-file">
                    </div>
                </form>
            `,
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> Atualizar',
            cancelButtonText: '<i class="fas fa-times"></i> Fechar',
            buttonsStyling: false,
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary me-2'
            },

            didOpen: () => {
                $('input[name="data_recebimento"]').mask('00/00/0000');
                $('input[name="valor"]').mask('#.##0,00', { reverse: true });
            },

            preConfirm: () => {
                const tempoMinimo = 1500;
                const inicio = Date.now();

                const formData = new FormData($('#formAtualizarHonorario')[0]);

                Swal.fire({
                    title: 'Atualizando...',
                    text: 'Aguarde enquanto o honorário é atualizado.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    didOpen: () => Swal.showLoading()
                });

                return $.ajax({
                    url: `/honorarios/${honorarioId}/atualizar`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'PUT'
                    }
                }).then(response => {
                    const tempoDecorrido = Date.now() - inicio;
                    const atraso = tempoMinimo - tempoDecorrido;

                    return new Promise(resolve => {
                        setTimeout(() => {
                            Swal.fire('Sucesso!', 'Honorário atualizado com sucesso.', 'success');
                            resolve(response);
                        }, atraso > 0 ? atraso : 0);
                    });
                }).catch(xhr => {
                    const tempoDecorrido = Date.now() - inicio;
                    const atraso = tempoMinimo - tempoDecorrido;

                    let msg = 'Erro ao atualizar honorário.';
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).join("<br>");
                    } else if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }

                    return new Promise((_, reject) => {
                        setTimeout(() => {
                            Swal.fire('Erro!', msg, 'error');
                            reject();
                        }, atraso > 0 ? atraso : 0);
                    });
                });
            },

            willClose: () => {
                $modalBootstrap.modal('show');
            }
        });

    }).fail(() => {
        $modalBootstrap.modal('show');
        Swal.fire('Erro', 'Não foi possível carregar os dados do honorário.', 'error');
    });
}
