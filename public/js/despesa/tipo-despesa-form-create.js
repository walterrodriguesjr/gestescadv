// public/js/despesa/tipo-despesa-form-create.js

$('#btnNovaDespesa').on('click', function () {
    Swal.fire({
        title: 'Novo Tipo de Despesa',
        html: `<input id="inputTituloDespesa" type="text" class="swal2-input" placeholder="Título do tipo de despesa" maxlength="100">`,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Salvar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        buttonsStyling: false,
        reverseButtons: true,
        focusConfirm: false,
        customClass: {
            confirmButton: 'btn btn-primary ms-2',
            cancelButton: 'btn btn-secondary me-2'
        },
        didOpen: () => {
            $('#inputTituloDespesa').on('input', function () {
                let texto = $(this).val();
                texto = texto.replace(/\s+/g, ' ')
                    .split(' ')
                    .map(w => w.charAt(0).toUpperCase() + w.substring(1).toLowerCase())
                    .join(' ');
                $(this).val(texto);
            });
            $('#inputTituloDespesa').focus();
        },
        preConfirm: () => {
            const valor = $('#inputTituloDespesa').val().trim();
            if (!valor) {
                Swal.showValidationMessage('Informe o título da despesa.');
                return false;
            }
            return valor;
        }
    }).then(function (result) {
        if (result.isConfirmed && result.value) {
            let podeFechar = false;
            const tempoMinimo = 1500;
            const tempoMaximo = 10000;

            Swal.fire({
                title: 'Salvando...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    setTimeout(() => { podeFechar = true; }, tempoMinimo);
                    setTimeout(() => { if (Swal.isVisible()) Swal.close(); }, tempoMaximo);
                }
            });

            const inicio = Date.now();

            $.ajax({
                url: urlTipoDespesaStore,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    titulo: result.value
                },
                success: function (resp) {
                    const tempoDecorrido = Date.now() - inicio;
                    const atraso = tempoMinimo - tempoDecorrido;

                    const mostrarSwalSucesso = () => {
                        Swal.fire('Sucesso!', 'Tipo de despesa criado com sucesso.', 'success');
                        // Chama função global para atualizar os choices e opções via AJAX
                        if (typeof window.recarregarTiposDespesaChoices === 'function') {
                            window.recarregarTiposDespesaChoices(resp.id);
                        }
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
                        let msg = xhr.responseJSON?.message || 'Erro ao criar tipo de despesa.';
                        Swal.fire('Erro', msg, 'error');
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
});
