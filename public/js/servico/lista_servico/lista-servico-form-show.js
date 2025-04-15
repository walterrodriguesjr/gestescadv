let tabelaServicos;
let tipoClienteAtual = null;

$(document).ready(function () {
    // Inicializa DataTable sem dados
    tabelaServicos = $('#tabelaServicos').DataTable({
        data: [], // começa vazio
        columns: [
            { data: 'nome', title: 'Nome / Razão Social' },
            { data: 'cpf_cnpj', title: 'CPF / CNPJ' },
            {
                data: 'celular',
                title: 'Celular',
                render: function (data) {
                    if (!data) return 'Não informado';
                    let numero = data.replace(/\D/g, '');
                    return `
                        <a href="https://wa.me/55${numero}" target="_blank" class="text-success text-decoration-none">
                            <i class="fab fa-whatsapp fa-lg"></i> ${data}
                        </a>`;
                }
            },
            { data: 'status', title: 'Etapa Atual' },
            {
                data: 'id',
                title: 'Ações',
                orderable: false,
                searchable: false,
                render: function (id) {
                    return `
                        <a href="/servicos/${id}/detalhes" class="btn btn-sm btn-primary" title="Visualizar">
                            <i class="fas fa-arrow-right"></i>
                        </a>`;
                }
            }
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

    // Controle do ícone do card collapse
    $('.card-toggle-header').on('click', function () {
        const $card = $(this).closest('.card');
        const $body = $card.find('.card-body');
        const $icon = $(this).find('i');

        if ($card.hasClass('collapsed-card')) {
            $card.removeClass('collapsed-card');
            $body.slideDown();
            $icon.removeClass('fa-plus').addClass('fa-minus');
        } else {
            $card.addClass('collapsed-card');
            $body.slideUp();
            $icon.removeClass('fa-minus').addClass('fa-plus');
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
