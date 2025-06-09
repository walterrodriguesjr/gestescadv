// public/js/despesa/despesa-form-delete.js

$(function () {
    $(document).on('click', '.btn-excluir-despesa', function () {
        const id = $(this).data('id');
        const valor = $(this).data('valor');
        Swal.fire({
            title: 'Excluir Despesa',
            html: `Tem certeza que deseja excluir a despesa de valor <strong>R$ ${parseFloat(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash"></i> Excluir',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            customClass: {
                confirmButton: 'btn btn-danger ms-2',
                cancelButton: 'btn btn-secondary me-2'
            },
            buttonsStyling: false,
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                let podeFechar = false;
                const tempoMinimo = 1500;
                const tempoMaximo = 10000;
                const inicio = Date.now();

                Swal.fire({
                    title: 'Excluindo...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                        setTimeout(() => { podeFechar = true; }, tempoMinimo);
                        setTimeout(() => { if (Swal.isVisible()) Swal.close(); }, tempoMaximo);
                    }
                });

                $.ajax({
                    url: `/despesas/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (resp) {
                        const tempoDecorrido = Date.now() - inicio;
                        const atraso = tempoMinimo - tempoDecorrido;

                        const mostrarSwalSucesso = () => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: 'Despesa excluída com sucesso.',
                                customClass: { confirmButton: 'btn btn-primary' },
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
                            let msg = xhr.responseJSON?.message || 'Erro ao excluir despesa.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: msg,
                                customClass: { confirmButton: 'btn btn-danger' },
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
    });
});
