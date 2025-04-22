function abrirFormularioNovoAndamento(servicoId) {
    Swal.fire({
        title: 'Novo Andamento',
        html: `
            <form id="formNovoAndamento">
                <div class="form-group text-left">
                    <label for="etapa">Etapa</label>
                    <select id="etapa" class="form-control" required>
                        <option value="">Carregando...</option>
                    </select>
                </div>

                <div class="form-group text-left mt-2">
                    <label for="descricao">Descrição</label>
                    <input type="text" id="descricao" class="form-control" placeholder="Informe uma descrição">
                </div>

                <div class="form-group text-left mt-2">
                    <label for="observacoes">Observações</label>
                    <textarea id="observacoes" class="form-control" rows="3" placeholder="Observações adicionais..."></textarea>
                </div>

                <div class="form-group text-left mt-2">
                    <label for="data_hora">Data e Hora</label>
                    <input type="datetime-local" id="data_hora" class="form-control" required>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> Salvar',
        cancelButtonText: 'Cancelar',
        focusConfirm: false,
        didOpen: () => {
            // Inicializa o select com Choices.js
            const etapaChoices = new Choices('#etapa', {
                shouldSort: false,
                searchEnabled: true,
                itemSelectText: '',
            });

            // Preencher com etapas vindas do backend
            // Preencher com etapas vindas do backend
            $.ajax({
                url: '/etapas-servico',
                method: 'GET',
                success: function (response) {
                    etapaChoices.clearStore();
                    response.data.forEach(etapa => {
                        etapaChoices.setChoices([{
                            value: etapa.nome,
                            label: etapa.nome
                        }], 'value', 'label', false);
                    });
                },
                error: function () {
                    etapaChoices.clearStore();
                    etapaChoices.setChoices([{ value: '', label: 'Erro ao carregar etapas' }], 'value', 'label', false);
                }
            });


            // Define o valor atual no input de data/hora
            let agora = new Date().toISOString().slice(0, 16);
            $('#data_hora').val(agora);
        },
        preConfirm: () => {
            const etapa = $('#etapa').val();
            const descricao = $('#descricao').val();
            const observacoes = $('#observacoes').val();
            const honorario = $('#honorario').val();
            const dataHora = $('#data_hora').val();

            if (!etapa || !dataHora) {
                Swal.showValidationMessage('Etapa e data/hora são obrigatórios.');
                return false;
            }

            Swal.showLoading();

            return $.ajax({
                url: `/andamentos/${servicoId}`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    etapa: etapa,
                    descricao: descricao,
                    observacoes: observacoes,
                    honorario: honorario,
                    data_hora: dataHora
                }
            }).then(resp => {
                return resp;
            }).catch(xhr => {
                let mensagem = xhr.responseJSON?.message || 'Erro ao salvar andamento.';
                Swal.showValidationMessage(mensagem);
            });
        }
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire('Sucesso!', 'Andamento salvo com sucesso.', 'success').then(() => {
                location.reload(); // recarrega a página para atualizar a timeline
            });
        }
    });
}
