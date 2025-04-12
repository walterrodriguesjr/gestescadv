$(document).ready(function () {
    $('#tabelaTipoServicos').on('click', '.excluir-servico', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Confirma exclusão?',
            text: 'Esta ação removerá o tipo de serviço e afetará todos os registros relacionados.',
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
                // Loader padrão com tempo mínimo de 1.5s
                Swal.fire({
                    title: "Excluindo...",
                    text: "Aguarde enquanto o tipo de serviço está sendo removido.",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    timer: 10000,
                    timerProgressBar: true,
                    didOpen: () => Swal.showLoading()
                });

                const inicio = Date.now();

                $.ajax({
                    url: `/deletar_tipo_servico/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (resp) {
                        const tempoDecorrido = Date.now() - inicio;
                        const atraso = 1500 - tempoDecorrido;

                        setTimeout(() => {
                            Swal.fire('Excluído!', resp.message || 'Tipo de serviço removido com sucesso.', 'success');
                            $('#tabelaTipoServicos').DataTable().ajax.reload();
                        }, atraso > 0 ? atraso : 0);
                    },
                    error: function (xhr) {
                        const tempoDecorrido = Date.now() - inicio;
                        const atraso = 1500 - tempoDecorrido;

                        setTimeout(() => {
                            let msg = 'Erro ao excluir tipo de serviço.';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
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
