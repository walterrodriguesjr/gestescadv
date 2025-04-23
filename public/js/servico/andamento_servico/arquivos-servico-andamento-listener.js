let paginaAtual = 0;
let carregando = false;
let fim = false;

function carregarMaisAndamentos() {
    if (carregando || fim) return;

    carregando = true;
    $('#carregando-indicador').removeClass('d-none');

    paginaAtual++;

    $.get(`/andamentos/${servicoId}/scroll?page=${paginaAtual}`, function (res) {
        if (!res.data || res.data.length === 0) {
            fim = true;
            $('#carregando-indicador').html('<div class="text-muted text-center mt-3">✔️ Todos os andamentos deste serviço foram exibidos.</div>');
            return;
        }

        res.data.forEach(andamento => {
            $('#timeline-conteudo').append(`
                <div>
                    <i class="fas fa-circle ${andamento.icone_cor}"></i>
                    <div class="timeline-item">
                        <span class="time">
                            <i class="far fa-clock"></i> ${andamento.data_hora}
                        </span>
                        <h3 class="timeline-header d-flex justify-content-between align-items-center">
                            <strong>${andamento.etapa}</strong>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-secondary" onclick="abrirArquivosSwal(${servicoId}, ${clienteId}, '${tipoCliente}', '${andamento.id}')">
                                    <i class="fas fa-folder-open"></i> Arquivos
                                </button>
                                <button class="btn btn-sm btn-primary" onclick="abrirObservacaoSwal(${servicoId}, '${andamento.etapa}', '${moment(andamento.data_hora, 'DD/MM/YYYY HH:mm').format('YYYY-MM-DD HH:mm:ss')}')">
                                    <i class="fas fa-comment-dots"></i> Observações
                                </button>
                            </div>
                        </h3>
                        <div class="timeline-body">
                            ${andamento.descricao}
                            ${andamento.tipo === 'agenda' && andamento.data_hora_fim ? `
                                <div class="mt-2 text-muted small">
                                    Início:&nbsp;${andamento.data_hora}<br>
                                    Término:&nbsp;${andamento.data_hora_fim}
                                </div>` : ''}
                        </div>
                    </div>
                </div>
            `);
        });

        carregando = false;
        $('#carregando-indicador').addClass('d-none');
    }).fail(() => {
        $('#carregando-indicador').html('<div class="text-danger text-center mt-3">❌ Erro ao carregar andamentos.</div>');
    });
}

$(document).ready(() => carregarMaisAndamentos());

$('.timeline-container').on('scroll', function () {
    const container = $(this);
    if (container.scrollTop() + container.innerHeight() >= container[0].scrollHeight - 100) {
        carregarMaisAndamentos();
    }
});
