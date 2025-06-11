let tabelaTipoServicos;

$(document).ready(function () {
    tabelaTipoServicos = $('#tabelaTipoServicos').DataTable({
        ajax: {
            url: `/tipo_servicos/${escritorioId}`,
            method: 'GET',
            dataSrc: function (json) {
                return json.data || [];
            },
            error: function (xhr) {
                Swal.fire('Erro', xhr.responseJSON?.message || 'Erro ao carregar serviços.', 'error');
            }
        },
        columns: [
            { data: 'nome_servico', title: 'Nome do Serviço' },
            {
                data: null,
                title: 'Ações',
                orderable: false,
                searchable: false,
                render: function (data) {
                    return `
                        <button class="btn btn-sm btn-success editar-servico" data-id="${data.id}">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-danger excluir-servico" data-id="${data.id}">
                            <i class="fas fa-trash"></i> Deletar
                        </button>
                    `;
                }

            }
        ],
        language: {
            url: "/lang/datatables/pt-BR.json" // ✅ correto
        }
    });

    // Evento para exclusão
    $('#tabelaTipoServicos').on('click', '.excluir-servico', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Confirma exclusão?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/tipo_servicos/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (resp) {
                        Swal.fire('Excluído!', resp.message || 'Serviço excluído.', 'success');
                        tabelaTipoServicos.ajax.reload();
                    },
                    error: function (xhr) {
                        Swal.fire('Erro!', xhr.responseJSON?.message || 'Erro ao excluir.', 'error');
                    }
                });
            }
        });
    });
});
