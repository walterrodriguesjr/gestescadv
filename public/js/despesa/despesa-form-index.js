// public/js/despesa/despesa-form-index.js

$(function () {
    function extrairMes(dataStr) {
        if (!dataStr) return 0;
        let partes = dataStr.split('-');
        if (partes.length < 3) return 0;
        return parseInt(partes[1], 10);
    }
    function extrairAno(dataStr) {
        if (!dataStr) return 0;
        let partes = dataStr.split('-');
        if (partes.length < 3) return 0;
        return parseInt(partes[0], 10);
    }

    let hoje = new Date();
    let mesAtual = hoje.getMonth() + 1;
    let anoAtual = hoje.getFullYear();
    let mesSelecionado = mesAtual;
    let anoSelecionado = anoAtual;

    $(`#filtroMesesDespesa .btn-mes-despesa[data-mes="${mesAtual}"]`).addClass('active');

    window.tabelaDespesas = $('#tabelaDespesas').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.location.pathname,
            type: 'GET',
            dataSrc: 'data'
        },
        language: {
            "sEmptyTable": "Nenhuma despesa cadastrada ainda.",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ despesas",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 despesas",
            "sInfoFiltered": "(filtrado de _MAX_ despesas)",
            "sLengthMenu": "Mostrar _MENU_ despesas",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sSearch": "Buscar:",
            "sZeroRecords": "Nenhuma despesa encontrada.",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sLast": "Último",
                "sNext": "Próximo",
                "sPrevious": "Anterior"
            }
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
                title: 'Data de Vencimento',
                render: function (data) {
                    if (!data) return '-';
                    let dataLimpa = data.split('T')[0];
                    const [y, m, d] = dataLimpa.split('-');
                    return `${d.padStart(2, '0')}/${m.padStart(2, '0')}/${y}`;
                }
            },
            {
                data: 'situacao',
                title: 'Situação',
                render: function (data) {
                    return data
                        ? '<span class="badge bg-success">Pago</span>'
                        : '<span class="badge bg-warning text-dark">Em aberto</span>';
                }
            },
            {
                data: null,
                title: 'Status',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-xs btn-primary btn-alterar-status px-2 py-1"
                                data-id="${row.id}" data-status="${row.situacao ? 1 : 0}" style="font-size: 0.85rem;">
                            <i class="fas fa-sync-alt"></i> Alterar
                        </button>
                    `;
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
                        <button class="btn btn-sm btn-success btn-editar-despesa" data-id="${row.id}">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-danger btn-excluir-despesa" data-id="${row.id}" data-valor="${row.valor}">
                            <i class="fas fa-trash"></i> Deletar
                        </button>
                    `;
                }
            }
        ],
        drawCallback: function () {
            atualizarSelectAno(this.api());
        }
    });

    // Filtro de meses e anos
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData) {
        if (settings.nTable.id !== 'tabelaDespesas') return true;
        // Filtro mês
        if (mesSelecionado !== 0) {
            const dataVenc = rowData.data_vencimento || (data[2] && data[2].split('/').reverse().join('-'));
            const mesDespesa = extrairMes(dataVenc);
            if (mesDespesa !== mesSelecionado) return false;
        }
        // Filtro ano
        const dataVenc = rowData.data_vencimento || (data[2] && data[2].split('/').reverse().join('-'));
        const anoDespesa = extrairAno(dataVenc);
        if (anoDespesa !== anoSelecionado) return false;
        return true;
    });

    $(document).on('click', '.btn-mes-despesa', function () {
        $('.btn-mes-despesa').removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
        mesSelecionado = parseInt($(this).data('mes'), 10);
        window.tabelaDespesas.draw();
    });

    $(document).on('change', '#selectAnoDespesa', function () {
        anoSelecionado = parseInt($(this).val(), 10);
        window.tabelaDespesas.draw();
    });

    // Atualiza o select de anos SEM chamar .draw()
    function atualizarSelectAno(dtApi) {
        let anos = [];
        dtApi.rows().data().each(function (row) {
            if (row.data_vencimento) {
                const ano = extrairAno(row.data_vencimento.split('T')[0]);
                if (ano && !anos.includes(ano)) anos.push(ano);
            }
        });
        if (!anos.includes(anoAtual)) anos.push(anoAtual);
        anos = anos.sort((a, b) => b - a);

        const select = $('#selectAnoDespesa');
        const valorAnterior = select.val();
        select.empty();
        anos.forEach(ano => {
            select.append(`<option value="${ano}">${ano}</option>`);
        });
        // Mantém o ano selecionado se possível, senão seleciona o atual
        if (anos.includes(Number(valorAnterior))) {
            select.val(valorAnterior);
            anoSelecionado = Number(valorAnterior);
        } else {
            select.val(anoAtual);
            anoSelecionado = anoAtual;
        }
    }

    // Botão status
    $(document).on('click', '.btn-alterar-status', function () {
        const id = $(this).data('id');
        const statusAtual = $(this).data('status');
        const novoStatus = statusAtual ? 0 : 1;

        Swal.fire({
            icon: 'question',
            title: novoStatus ? 'Marcar como Pago?' : 'Marcar como Em Aberto?',
            html: novoStatus
                ? 'Deseja marcar esta despesa como <b>Pago</b>?'
                : 'Deseja marcar esta despesa como <b>Em Aberto</b>?',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> Confirmar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-primary ms-2',
                cancelButton: 'btn btn-secondary me-2'
            },
            reverseButtons: true,
            focusConfirm: false,
        }).then((result) => {
            if (result.isConfirmed) {
                let podeFechar = false;
                const tempoMinimo = 1500;
                const tempoMaximo = 10000;
                const inicio = Date.now();

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

                $.ajax({
                    url: `/despesas/${id}/alterar-status`,
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { situacao: novoStatus },
                    success: function (resp) {
                        const tempoDecorrido = Date.now() - inicio;
                        const atraso = tempoMinimo - tempoDecorrido;

                        const mostrarSwalSucesso = () => {
                            Swal.fire('Sucesso!', 'Status da despesa alterado.', 'success');
                            window.tabelaDespesas.ajax.reload();
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
                            let msg = xhr.responseJSON?.message || 'Erro ao alterar status.';
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
});
