// public/js/servico/lista_servico/lista-servico-from-show.js

$(document).ready(function () {
    // Botões de filtro
    let tipoClienteAtual = 'pessoa_fisica';

    // Ativar botão inicial
    $('#btnFiltroPF').addClass('active');

    // Inicializar DataTable
    const tabelaServicos = $('#tabelaServicos').DataTable({
        ajax: {
            url: '/servicos/listar',
            data: function (d) {
                d.tipo = tipoClienteAtual;
            },
            error: function (xhr, status, error) {
                console.error('Erro ao carregar serviços:', error);
                Swal.fire('Erro', 'Falha ao carregar serviços.', 'error');
            }
        },
        columns: [
            { data: 'nome', title: 'Nome / Razão Social' },
            { data: 'cpf_cnpj', title: 'CPF / CNPJ' },
            {
                data: 'celular',
                title: 'Celular',
                render: function (data) {
                    if (!data) return "Não informado";
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
                render: function (id) {
                    return `
                        <a href="/servicos/${id}/detalhes" class="btn btn-sm btn-primary">
                            <i class="fas fa-arrow-right"></i>
                        </a>`;
                }
            }
        ],
        responsive: true,
        processing: true,
        serverSide: false,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' }
    });

    // Clique nos filtros
    $('#btnFiltroPF').on('click', function () {
        tipoClienteAtual = 'pessoa_fisica';
        $('#btnFiltroPF').addClass('active');
        $('#btnFiltroPJ').removeClass('active');
        tabelaServicos.ajax.reload();
    });

    $('#btnFiltroPJ').on('click', function () {
        tipoClienteAtual = 'pessoa_juridica';
        $('#btnFiltroPJ').addClass('active');
        $('#btnFiltroPF').removeClass('active');
        tabelaServicos.ajax.reload();
    });
});
