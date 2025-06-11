$(document).ready(function () {
    window.abrirModalDocumentosServico = function (servicoId) {
        Swal.fire({
            title: 'Todos os Arquivos do Serviço',
            html: '<div class="text-center p-3"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Carregando arquivos...</div>',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                $.get(`/servicos/${servicoId}/cliente/${clienteId}/arquivos`, function (res) {
                    const arquivos = res.arquivos;

                    if (!arquivos.length) {
                        Swal.update({
                            html: `
                                <div class="text-center text-muted mt-3">
                                    <i class="fas fa-folder-open fa-lg text-warning mb-2"></i><br>
                                    Nenhum arquivo encontrado para este serviço.
                                </div>`,
                            showConfirmButton: true,
                            confirmButtonText: '<i class="fas fa-times"></i> Fechar',
                            customClass: {
                                confirmButton: 'btn btn-secondary mt-3'
                            }
                        });
                        return;
                    }

                    const getIcon = (nome) => {
                        const ext = nome.split('.').pop().toLowerCase();
                        if (['pdf'].includes(ext)) return '<i class="fas fa-file-pdf text-danger"></i>';
                        if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(ext)) return '<i class="fas fa-file-image text-info"></i>';
                        if (['doc', 'docx'].includes(ext)) return '<i class="fas fa-file-word text-primary"></i>';
                        if (['xls', 'xlsx'].includes(ext)) return '<i class="fas fa-file-excel text-success"></i>';
                        return '<i class="fas fa-file-alt text-muted"></i>';
                    };

                    let html = `
                        <div class="text-muted small mb-2">
                            📌 Clique no nome do arquivo para visualizar.
                        </div>
                        <div class="list-group text-start" style="max-height:60vh;overflow:auto">
                    `;

                    arquivos.forEach(arquivo => {
                        html += `
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    ${getIcon(arquivo.nome)}
                                    <a href="${arquivo.url}" target="_blank" class="text-decoration-none text-dark fw-bold">
                                        ${arquivo.nome}
                                    </a>
                                </div>
                                <a href="${arquivo.url}" download class="btn btn-sm btn-outline-secondary" title="Baixar">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        `;
                    });

                    html += '</div>';

                    Swal.update({
                        html: html,
                        showConfirmButton: true,
                        confirmButtonText: '<i class="fas fa-times"></i> Fechar',
                        customClass: {
                            confirmButton: 'btn btn-secondary mt-3'
                        }
                    });
                }).fail(() => {
                    Swal.update({
                        icon: 'error',
                        title: 'Erro!',
                        html: '<p class="text-danger">Não foi possível carregar os arquivos.</p>',
                        showConfirmButton: true,
                        confirmButtonText: '<i class="fas fa-times"></i> Fechar',
                        customClass: {
                            confirmButton: 'btn btn-danger mt-3'
                        }
                    });
                });
            }
        });
    };
});
