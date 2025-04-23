function abrirFormularioNovoAndamento(servicoId) {
    let podeFechar = false;
    const tempoMinimo = 1500;
    const tempoMaximo = 10000;

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
                <input type="text" id="descricao" class="form-control" placeholder="Informe uma descrição" required>
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
        showCloseButton: true,
        allowEscapeKey: true,
        confirmButtonText: '<i class="fas fa-check"></i> Salvar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        buttonsStyling: false,
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-primary ms-2',
            cancelButton: 'btn btn-secondary me-2'
        },
        focusConfirm: false,
        didOpen: () => {
            const etapaChoices = new Choices('#etapa', {
                shouldSort: false,
                searchEnabled: true,
                itemSelectText: '',
            });

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

            let agora = new Date().toISOString().slice(0, 16);
            $('#data_hora').val(agora);
        },
        preConfirm: () => {
            const etapa = $('#etapa').val();
            const descricao = $('#descricao').val();
            const observacoes = $('#observacoes').val();
            const dataHora = $('#data_hora').val();

            if (!etapa || !descricao || !dataHora) {
                Swal.showValidationMessage('Etapa, descrição e data/hora são obrigatórios.');
                return false;
            }

            Swal.fire({
                title: 'Salvando...',
                text: 'Aguarde enquanto o andamento é cadastrado...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCloseButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    setTimeout(() => { podeFechar = true; }, tempoMinimo);
                    setTimeout(() => { if (Swal.isVisible()) Swal.close(); }, tempoMaximo);
                }
            });

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
                    data_hora: dataHora
                }
            }).then(resp => {
                return new Promise(resolve => {
                    const esperar = setInterval(() => {
                        if (podeFechar) {
                            clearInterval(esperar);
                            resolve(resp);
                        }
                    }, 100);
                });
            }).catch(xhr => {
                let mensagem = xhr.responseJSON?.message || 'Erro ao salvar andamento.';
                Swal.showValidationMessage(mensagem);
            });
        }
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: 'Andamento salvo com sucesso.',
                confirmButtonText: 'OK'
            }).then(() => location.reload());
        }
    });
}
