// public/js/despesa/despesa-form-update.js

$(function () {
    let choicesTipoDespesaEdit = null;

    // Ao clicar em Editar
    $(document).on('click', '.btn-editar-despesa', function () {
        const id = $(this).data('id');

        // AJAX direto, sem loader visível na tela!
        $.ajax({
            url: `/despesas/${id}/edit`,
            method: 'GET',
            success: function (resp) {
                // Monta HTML do modal
                let opcoes = '';
                resp.tiposDespesa.forEach(tipo => {
                    opcoes += `<option value="${tipo.id}" ${tipo.id == resp.despesa.tipo_despesa_id ? 'selected' : ''}>
                        ${tipo.titulo}
                    </option>`;
                });

                let valorFormatado = parseFloat(resp.despesa.valor).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                // Data (YYYY-MM-DD)
                let dataVencimento = resp.despesa.data_vencimento.split('T')[0];

                Swal.fire({
                    title: 'Editar Despesa',
                    html: `
                        <form id="formEditarDespesa" autocomplete="off">
                            <div class="mb-3">
                                <label for="tipo_despesa_id_edit" class="form-label">Tipo de Despesa</label>
                                <select id="tipo_despesa_id_edit" name="tipo_despesa_id" class="form-select" required>
                                    ${opcoes}
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="valor_edit" class="form-label">Valor (R$)</label>
                                <input type="text" id="valor_edit" name="valor" class="form-control" required value="${valorFormatado}">
                            </div>
                            <div class="mb-3">
                                <label for="data_vencimento_edit" class="form-label">Data de Vencimento</label>
                                <input type="date" id="data_vencimento_edit" name="data_vencimento" class="form-control" required value="${dataVencimento}">
                            </div>
                        </form>
                    `,
                    showCancelButton: true,
                    focusConfirm: false,
                    reverseButtons: true,
                    confirmButtonText: '<i class="fas fa-check"></i> Salvar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-primary ms-2',
                        cancelButton: 'btn btn-secondary me-2'
                    },
                    buttonsStyling: false,
                    didOpen: () => {
                        if (choicesTipoDespesaEdit) choicesTipoDespesaEdit.destroy();
                        choicesTipoDespesaEdit = new Choices('#tipo_despesa_id_edit', {
                            searchEnabled: true,
                            itemSelectText: '',
                            shouldSort: false
                        });
                        $('#valor_edit').mask('#.##0,00', { reverse: true });
                    },
                    preConfirm: () => {
                        const tipoId = $('#tipo_despesa_id_edit').val();
                        const valor = $('#valor_edit').val();
                        const data = $('#data_vencimento_edit').val();
                        let camposErro = [];

                        if (!tipoId) camposErro.push('Tipo de despesa');
                        if (!valor || !/^\d{1,3}(\.\d{3})*,\d{2}$|^\d+,\d{2}$/.test(valor)) camposErro.push('Valor válido');
                        if (!data) camposErro.push('Data de vencimento');

                        if (camposErro.length > 0) {
                            Swal.showValidationMessage(
                                `Preencha corretamente: <br> <ul class="text-left mt-2">
                                ${camposErro.map(campo => `<li><i class='fas fa-exclamation-circle text-danger mr-1'></i> ${campo}</li>`).join('')}
                                </ul>`
                            );
                            return false;
                        }
                        return {
                            tipo_despesa_id: tipoId,
                            valor: valor.replace(/\./g, '').replace(',', '.'),
                            data_vencimento: data
                        };
                    }
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        let podeFechar = false;
                        const tempoMinimo = 1500;
                        const tempoMaximo = 10000;
                        const inicio = Date.now();

                        // Agora SIM, mostra o loader!
                        Swal.fire({
                            title: 'Salvando...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                                setTimeout(() => { podeFechar = true; }, tempoMinimo);
                                setTimeout(() => { if (Swal.isVisible()) Swal.close(); }, tempoMaximo);
                            }
                        });

                        // PATCH para update
                        $.ajax({
                            url: `/despesas/${id}`,
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: result.value,
                            success: function (resp) {
                                const tempoDecorrido = Date.now() - inicio;
                                const atraso = tempoMinimo - tempoDecorrido;
                                const mostrarSwalSucesso = () => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Sucesso!',
                                        text: 'Despesa atualizada com sucesso.',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false
                                    });
                                    if (window.tabelaDespesas) window.tabelaDespesas.ajax.reload();
                                };
                                if (podeFechar) {
                                    Swal.close();
                                    mostrarSwalSucesso();
                                } else {
                                    setTimeout(() => {
                                        Swal.close();
                                        mostrarSwalSucesso();
                                    }, atraso > 0 ? atraso : 0);
                                }
                            },
                            error: function (xhr) {
                                const mostrarSwalErro = () => {
                                    let msg = xhr.responseJSON?.message || 'Erro ao atualizar despesa.';
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erro',
                                        text: msg,
                                        customClass: {
                                            confirmButton: 'btn btn-danger'
                                        },
                                        buttonsStyling: false
                                    });
                                };
                                if (podeFechar) {
                                    Swal.close();
                                    mostrarSwalErro();
                                } else {
                                    setTimeout(() => {
                                        Swal.close();
                                        mostrarSwalErro();
                                    }, tempoMinimo);
                                }
                            }
                        });
                    }
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: xhr.responseJSON?.message || 'Erro ao buscar despesa.',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });
            }
        });
    });
});
