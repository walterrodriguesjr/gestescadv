// public/js/despesa/despesa-form-index.js

$(function () {
    // Função auxiliar: obtém mês da data (YYYY-MM-DD)
    function extrairMes(dataStr) {
        if (!dataStr) return 0;
        // dataStr: "2024-07-20"
        const partes = dataStr.split('-');
        return parseInt(partes[1], 10); // "07" -> 7
    }

    // DataTables
    window.tabelaDespesas = $('#tabelaDespesas').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.location.pathname,
            type: 'GET',
            dataSrc: 'data'
        },
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        },
        columns: [
            { 
                data: 'tipo_despesa.titulo', 
                title: 'Tipo', 
                defaultContent: '-' 
            },
            { 
                data: 'valor', 
                title: 'Valor',
                render: function (data) {
                    return parseFloat(data).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                }
            },
            { 
                data: 'data_vencimento', 
                title: 'Data',
                render: function (data) {
                    if (!data) return '-';
                    const [y, m, d] = data.split('-');
                    return `${d}/${m}/${y}`;
                }
            },
            { 
                data: 'situacao', 
                title: 'Situação',
                render: function (data) {
                    return data ? '<span class="badge bg-success">Pago</span>' : '<span class="badge bg-warning text-dark">Em aberto</span>';
                }
            },
            {
                data: null,
                title: 'Ações',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info btn-editar-despesa" data-id="${row.id}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btn-excluir-despesa" data-id="${row.id}" data-valor="${row.valor}"><i class="fas fa-trash"></i></button>
                    `;
                }
            }
        ]
    });

    // ---------------------------
    // FILTRO DE MESES
    // ---------------------------

    // Ativa o mês atual por padrão
    const hoje = new Date();
    const mesAtual = hoje.getMonth() + 1; // Janeiro=0, mas nosso botão é 1 para Jan
    $(`#filtroMesesDespesa .btn-mes-despesa[data-mes="${mesAtual}"]`).addClass('active');

    // Função de filtro por mês
    let mesSelecionado = mesAtual;

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData) {
        if (settings.nTable.id !== 'tabelaDespesas') return true; // Filtra só nossa tabela
        if (mesSelecionado === 0) return true; // 0 == "Todos"
        const dataVenc = rowData.data_vencimento || (data[2] && data[2].split('/').reverse().join('-'));
        const mesDespesa = extrairMes(dataVenc);
        return mesDespesa === mesSelecionado;
    });

    // Click filtro meses
    $(document).on('click', '.btn-mes-despesa', function () {
        $('.btn-mes-despesa').removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-primary');

        mesSelecionado = parseInt($(this).data('mes'), 10);
        window.tabelaDespesas.draw();
    });

    // Botão excluir (ajax)
    $(document).on('click', '.btn-excluir-despesa', function () {
        const id = $(this).data('id');
        const valor = $(this).data('valor');
        Swal.fire({
            title: 'Excluir Despesa',
            html: `Tem certeza que deseja excluir a despesa de valor <strong>R$ ${parseFloat(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash"></i> Excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/despesas/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (resp) {
                        Swal.fire('Sucesso!', 'Despesa excluída com sucesso.', 'success');
                        window.tabelaDespesas.ajax.reload();
                    },
                    error: function (xhr) {
                        let msg = xhr.responseJSON?.message || 'Erro ao excluir despesa.';
                        Swal.fire('Erro', msg, 'error');
                    }
                });
            }
        });
    });

    // Botão editar (ajuste conforme modal ou redirect)
    $(document).on('click', '.btn-editar-despesa', function () {
        const id = $(this).data('id');
        window.location.href = `/despesas/${id}/edit`;
    });
});
