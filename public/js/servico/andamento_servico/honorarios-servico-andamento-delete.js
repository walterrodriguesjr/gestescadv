$(document).ready(function () {
    $('#tabelaHonorarios').on('click', '.excluir-honorario', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Confirma exclusão?',
            text: 'Esta ação removerá o honorário e todos os dados vinculados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> Sim, excluir',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            buttonsStyling: false,
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger ms-2',
                cancelButton: 'btn btn-secondary me-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Excluindo...",
                    text: "Aguarde enquanto o honorário está sendo removido.",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    timer: 10000,
                    timerProgressBar: true,
                    didOpen: () => Swal.showLoading()
                });

                const inicio = Date.now();

                $.ajax({
                    url: `/honorarios/${id}/excluir`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (resp) {
                        const tempoDecorrido = Date.now() - inicio;
                        const atraso = 1500 - tempoDecorrido;

                        setTimeout(() => {
                            Swal.fire('Excluído!', resp.message || 'Honorário removido com sucesso.', 'success');

                            // remove a linha da tabela manualmente
                            $(`.excluir-honorario[data-id="${id}"]`).closest('tr').remove();

                        }, atraso > 0 ? atraso : 0);
                    },
                    error: function (xhr) {
                        const tempoDecorrido = Date.now() - inicio;
                        const atraso = 1500 - tempoDecorrido;

                        setTimeout(() => {
                            let msg = 'Erro ao excluir honorário.';
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                msg = Object.values(xhr.responseJSON.errors).join("<br>");
                            } else if (xhr.responseJSON?.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire('Erro!', msg, 'error');
                        }, atraso > 0 ? atraso : 0);
                    }
                });
            }
        });
    });
});
