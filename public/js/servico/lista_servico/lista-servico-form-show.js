let tabelaServicos;
let tipoClienteAtual = null;

$(document).ready(function () {
    // Inicializa DataTable
    tabelaServicos = $('#tabelaServicos').DataTable({
        data: [],
        columns: [
            { data: 'nome', title: 'Nome / Razão Social' },
            {
                data: 'cpf_cnpj',
                title: 'CPF / CNPJ',
                render: function (data, type, row) {
                    if (!data || !row.tipo_documento) return '—';
                    if (row.tipo_documento === 'cpf') {
                        return data.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
                    } else if (row.tipo_documento === 'cnpj') {
                        return data.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5");
                    }
                    return data;
                }
            },
            {
                data: 'celular',
                title: 'Celular',
                render: function (data) {
                    if (!data) return 'Não informado';
                    let numero = data.replace(/\D/g, '');
                    let formatado = numero.length === 11
                        ? data.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3')
                        : data.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                    return `
                        <a href="https://wa.me/55${numero}" target="_blank" class="text-success text-decoration-none">
                            <i class="fab fa-whatsapp fa-lg"></i> ${formatado}
                        </a>`;
                }
            },
            {
                data: 'status',
                title: 'Etapa Atual',
                render: function (data) {
                    return `<span class="badge badge-primary">${data}</span>`;
                }
            },
            {
                data: 'id',
                title: 'Ações',
                orderable: false,
                searchable: false,
                render: function (id) {
                    return `
                        <a href="/andamentos/${id}" class="btn btn-sm btn-primary text-nowrap" title="Clique para ver o andamento do serviço">
                            Ver andamento
                        </a>`;
                }
            }
        ],
        columnDefs: [
            { targets: 0, width: '28%' }, // Nome
            { targets: 1, width: '18%' }, // CPF/CNPJ
            { targets: 2, width: '20%' }, // Celular
            { targets: 3, width: '20%' }, // Etapa
            { targets: 4, width: '14%', className: 'text-center' } // Ações
        ],
        responsive: true,
        serverSide: false,
        processing: true,
        paging: true,
        ordering: true,
        searching: true,
        language: {
            url: "/lang/datatables/pt-BR.json"
        }
    });

    function abrirCardServicos() {
        const $card = $('#cardListarServicos .card');
        const $body = $card.find('.card-body');
        const $icon = $card.find('.card-toggle-header i');

        if ($card.hasClass('collapsed-card')) {
            $card.removeClass('collapsed-card');
            $body.slideDown();
            $icon.removeClass('fa-plus').addClass('fa-minus');
        }
    }

    function carregarServicos() {
        if (!tipoClienteAtual) return;

        $.ajax({
            url: '/servicos/listar',
            method: 'GET',
            data: { tipo: tipoClienteAtual },
            success: function (response) {
                tabelaServicos.clear().rows.add(response.data).draw();
            },
            error: function (xhr) {
                Swal.fire('Erro', xhr.responseJSON?.message || 'Erro ao carregar serviços.', 'error');
            }
        });
    }

    // Botões de filtro
    $('#btnFiltroPF').on('click', function () {
        tipoClienteAtual = 'pessoa_fisica';
        $('#btnFiltroPF').addClass('active');
        $('#btnFiltroPJ').removeClass('active');
        abrirCardServicos();
        carregarServicos();
    });

    $('#btnFiltroPJ').on('click', function () {
        tipoClienteAtual = 'pessoa_juridica';
        $('#btnFiltroPJ').addClass('active');
        $('#btnFiltroPF').removeClass('active');
        abrirCardServicos();
        carregarServicos();
    });
});
