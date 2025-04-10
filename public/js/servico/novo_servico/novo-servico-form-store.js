// public/js/servico/novo_servico/novo-servico-form-store.js

// Variáveis principais
let tipoServicoChoices;
let clienteChoices;
let tipoClienteChoices;
let agendamentoConsulta = null;
let arquivosSelecionados = [];

// (A) Atualiza a lista de arquivos na <ul id="listaArquivosSelecionados">
function atualizarListaArquivos() {
    const listaEl = $('#listaArquivosSelecionados');
    listaEl.empty();

    if (arquivosSelecionados.length === 0) {
        return;
    }

    const ul = $('<ul class="list-group"></ul>');

    arquivosSelecionados.forEach((arquivo, index) => {
        const li = $(`
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="fas fa-file mr-2"></i> ${arquivo.name}</span>
                <button type="button" class="btn btn-sm btn-danger remover-arquivo" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            </li>
        `);
        ul.append(li);
    });

    listaEl.append(ul);
}

// (B) Carregar Tipos de Serviço via AJAX e instanciar Choices no #tipoServico
function carregarTiposDeServico() {
    if (tipoServicoChoices) tipoServicoChoices.destroy();

    $.ajax({
        url: `/listar_tipo_servico/${escritorioId}`,
        method: 'GET',
        success: function(resp) {
            if (resp.success && resp.data && resp.data.length) {
                const $select = $('#tipoServico');
                $select.empty().append('<option value="">Selecione um tipo de serviço</option>');

                resp.data.forEach(servico => {
                    const selected = servico.nome_servico === 'Ainda não definido' ? 'selected' : '';
                    $select.append(`<option value="${servico.id}" ${selected}>${servico.nome_servico}</option>`);
                });

                tipoServicoChoices = new Choices('#tipoServico', {
                    placeholder: true,
                    placeholderValue: 'Selecione um tipo de serviço',
                    searchPlaceholderValue: 'Buscar...',
                    removeItemButton: true,
                    shouldSort: false,
                    noResultsText: "Nenhum resultado encontrado",
                    noChoicesText: "Nenhum serviço disponível"
                });
            } else {
                Swal.fire("Aviso", "Nenhum tipo de serviço foi encontrado.", "warning");
            }
        },
        error: function(xhr) {
            Swal.fire("Erro", xhr.responseJSON?.message || "Erro ao carregar tipos de serviço.", "error");
        }
    });
}

// (C) Inicializar Tipo de Cliente (select e checkbox "Cliente Novo?")
function inicializarTipoCliente() {
    const $tipoClienteSelect = $('#tipoCliente');
    if ($tipoClienteSelect.length) {
        tipoClienteChoices = new Choices($tipoClienteSelect[0], {
            searchEnabled: false,
            itemSelectText: '',
            shouldSort: false,
            placeholder: true,
            placeholderValue: 'Selecione o tipo'
        });
    }

    // Se não existe o checkbox "Cliente novo?", cria
    if (!$('#labelNovoCliente').length) {
        const checkboxHtml = `
            <div class="form-check d-inline-block ml-3" id="labelNovoCliente">
                <input type="checkbox" class="form-check-input" id="novoClienteCheckbox">
                <label class="form-check-label" for="novoClienteCheckbox">Cliente novo?</label>
            </div>`;
        $('label[for="clienteServico"]').after(checkboxHtml);
    }

    // Evento: ao clicar no checkbox "Cliente novo?"
    $(document).on('change', '#novoClienteCheckbox', function() {
        const tipoClienteSelecionado = $('#tipoCliente').val();

        if (!tipoClienteSelecionado) {
            Swal.fire("Atenção", "Selecione o tipo de cliente antes de continuar.", "warning");
            $(this).prop('checked', false);
            return;
        }

        if (this.checked) {
            $('#clienteServico').prop('disabled', true);
            const tipo = (tipoClienteSelecionado === 'pf') ? 'pessoa_fisica' : 'pessoa_juridica';

            let htmlForm = '';
            if (tipo === 'pessoa_fisica') {
                htmlForm = `
                    <form id="formNovoCliente">
                        <input type="hidden" name="tipo_cliente" value="pessoa_fisica">
                        <input type="text" name="nome" class="swal2-input" placeholder="Nome completo" required>
                        <input type="text" name="cpf" class="swal2-input cpf-mask" placeholder="CPF" required>
                        <input type="text" name="celular" class="swal2-input celular-mask" placeholder="Celular" required>
                        <div id="mensagemSeguranca" class="alert alert-info mt-2">
                            <i class="fas fa-info-circle"></i> Posteriormente você poderá incluir os dados complementares na área CLIENTES.
                        </div>
                    </form>
                `;
            } else {
                htmlForm = `
                    <form id="formNovoCliente">
                        <input type="hidden" name="tipo_cliente" value="pessoa_juridica">
                        <input type="text" name="razao_social" class="swal2-input" placeholder="Razão Social" required>
                        <input type="text" name="cnpj" class="swal2-input cnpj-mask" placeholder="CNPJ" required>
                        <input type="text" name="celular" class="swal2-input celular-mask" placeholder="Celular" required>
                        <div id="mensagemSeguranca" class="alert alert-info mt-2">
                            <i class="fas fa-info-circle"></i> Posteriormente você poderá incluir os dados complementares na área CLIENTES.
                        </div>
                    </form>
                `;
            }

            Swal.fire({
                title: 'Cadastro rápido de novo cliente',
                html: htmlForm,
                showCancelButton: true,
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                confirmButtonText: '<i class="fas fa-check"></i> Salvar',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                focusConfirm: false,
                didOpen: () => {
                    // Máscaras
                    $('.cpf-mask').mask('000.000.000-00');
                    $('.cnpj-mask').mask('00.000.000/0000-00');
                    $('.celular-mask').mask('(00) 00000-0000');
                },
                preConfirm: () => {
                    const form = $('#formNovoCliente');
                    const formData = form.serialize();
                    const dados = Object.fromEntries(new URLSearchParams(formData));

                    for (const campo in dados) {
                        if (!dados[campo]) {
                            Swal.showValidationMessage('Preencha todos os campos obrigatórios.');
                            return false;
                        }
                    }
                    return dados;
                }
            }).then(result => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        title: 'Salvando...',
                        text: 'Aguarde, estamos cadastrando o novo cliente.',
                        allowOutsideClick: false,
                        timerProgressBar: true,
                        didOpen: () => Swal.showLoading(),
                        timer: 10000
                    });

                    const tempoMinimo = 1500;
                    const inicio = Date.now();

                    // Cria o novo cliente
                    $.ajax({
                        url: '/clientes',
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: result.value,
                        success: function(resp) {
                            const tempoDecorrido = Date.now() - inicio;
                            const atraso = tempoMinimo - tempoDecorrido;
                            const tipoConvertido = result.value.tipo_cliente;

                            setTimeout(() => {
                                Swal.close();
                                Swal.fire("Sucesso", "Cliente cadastrado com sucesso!", "success");

                                // Agora puxa a lista de clientes do mesmo tipo
                                $.ajax({
                                    url: `/clientes/${tipoConvertido}`,
                                    method: 'GET',
                                    data: { escritorio_id: escritorioId },
                                    success: function(response) {
                                        const clientes = response.data || [];
                                        const $clienteSelect = $('#clienteServico');

                                        if (clienteChoices) {
                                            clienteChoices.destroy();
                                            $clienteSelect.removeAttr('data-choice');
                                        }

                                        $clienteSelect.empty();

                                        clientes.forEach(cliente => {
                                            let texto;
                                            if (cliente.tipo_cliente === 'pessoa_fisica') {
                                                texto = `${cliente.nome} - ${cliente.cpf}`;
                                            } else {
                                                texto = `${cliente.razao_social} - ${cliente.cnpj}`;
                                            }
                                            $clienteSelect.append(`<option value="${cliente.id}">${texto}</option>`);
                                        });

                                        clienteChoices = new Choices($clienteSelect[0], {
                                            searchPlaceholderValue: 'Buscar cliente...',
                                            placeholderValue: 'Selecione um cliente',
                                            noResultsText: "Nenhum resultado encontrado",
                                            noChoicesText: "Nenhum cliente disponível",
                                            shouldSort: false
                                        });

                                        // Tenta selecionar o cliente recém-incluído
                                        const clienteCadastrado = [...$clienteSelect[0].options].find(
                                            opt => opt.text.includes(result.value.nome || result.value.razao_social)
                                        );
                                        if (clienteCadastrado) {
                                            clienteChoices.setChoiceByValue(clienteCadastrado.value);
                                            $clienteSelect.prop('disabled', true);
                                            $('#novoClienteCheckbox').prop('checked', true);
                                        }
                                    }
                                });
                            }, atraso > 0 ? atraso : 0);
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire("Erro", xhr.responseJSON?.message || "Erro ao cadastrar cliente.", "error");
                            $('#clienteServico').prop('disabled', false);
                            $('#novoClienteCheckbox').prop('checked', false);
                        }
                    });
                } else {
                    $('#clienteServico').prop('disabled', false);
                    $('#novoClienteCheckbox').prop('checked', false);
                }
            });
        } else {
            $('#clienteServico').prop('disabled', false);
        }
    });
}

// (D) Ao mudar select #tipoCliente, carrega lista de clientes do tipo PF/PJ
$(document).on('change', '#tipoCliente', function() {
    const tipoSelecionado = $(this).val();

    if (!tipoSelecionado) {
        $('#clienteServico').prop('disabled', true);
        if (clienteChoices) {
            clienteChoices.destroy();
            $('#clienteServico').empty();
        }
        return;
    }

    const tipoConvertido = (tipoSelecionado === 'pf') ? 'pessoa_fisica' : 'pessoa_juridica';

    $.ajax({
        url: `/clientes/${tipoConvertido}`,
        method: 'GET',
        data: { escritorio_id: escritorioId },
        success: function(response) {
            const clientes = response.data || [];
            const $clienteSelect = $('#clienteServico');

            if (clienteChoices) {
                clienteChoices.destroy();
                $clienteSelect.removeAttr('data-choice');
            }

            $clienteSelect.empty().append('<option value="">Selecione um cliente</option>');

            clientes.forEach(cliente => {
                let texto;
                if (cliente.tipo_cliente === 'pessoa_fisica') {
                    texto = `${cliente.nome} - ${cliente.cpf}`;
                } else {
                    texto = `${cliente.razao_social} - ${cliente.cnpj}`;
                }
                $clienteSelect.append(`<option value="${cliente.id}">${texto}</option>`);
            });

            $clienteSelect.prop('disabled', false);

            clienteChoices = new Choices($clienteSelect[0], {
                searchPlaceholderValue: 'Buscar cliente...',
                placeholderValue: 'Selecione um cliente',
                noResultsText: "Nenhum resultado encontrado",
                noChoicesText: "Nenhum cliente disponível",
                shouldSort: false
            });
        },
        error: function(xhr) {
            Swal.fire("Erro", xhr.responseJSON?.message || "Erro ao carregar clientes.", "error");
        }
    });
});

// (E) Eventos para anexos: no "change" do input e "click" no botão de remover
$(document).on('change', '#arquivosServico', function(e) {
    const novosArquivos = Array.from(e.target.files);
    // Anexa no array
    arquivosSelecionados = [...arquivosSelecionados, ...novosArquivos];
    // Se quiser remover duplicatas por nome + size, faça aqui.

    // Limpa o input para permitir selecionar o mesmo arquivo novamente
    $(this).val('');
    atualizarListaArquivos();
});

$(document).on('click', '.remover-arquivo', function () {
    const index = $(this).data('index');
    arquivosSelecionados.splice(index, 1);
    atualizarListaArquivos();
});

// (F) Ao marcar "Deseja agendar consulta?"
$(document).on('change', '#checkboxAgendarConsulta', function () {
    if (this.checked) {
        const agora = new Date().toISOString().slice(0, 16);
        const daquiUmaHora = new Date(Date.now() + 3600000).toISOString().slice(0, 16);

        Swal.fire({
            title: 'Agendamento de Consulta',
            html: `
                <input type="datetime-local" id="agendamentoDataHoraInicio" class="swal2-input" value="${agora}">
                <input type="datetime-local" id="agendamentoDataHoraFim" class="swal2-input" value="${daquiUmaHora}">
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> Salvar agendamento',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
            focusConfirm: false,
            preConfirm: () => {
                const inicio = $('#agendamentoDataHoraInicio').val();
                const fim = $('#agendamentoDataHoraFim').val();

                if (!inicio || !fim) {
                    Swal.showValidationMessage('Preencha todos os campos de data e hora.');
                    return false;
                }
                return { inicio, fim };
            }
        }).then(result => {
            if (result.isConfirmed) {
                agendamentoConsulta = result.value;
            } else {
                agendamentoConsulta = null;
                $('#checkboxAgendarConsulta').prop('checked', false);
            }
        });
    } else {
        agendamentoConsulta = null;
    }
});

// (G) Quando a página carrega
$(document).ready(function() {
    // Coloca data de hoje no campo #dataInicio
    const hoje = new Date();
    const ano = hoje.getFullYear();
    const mes = String(hoje.getMonth() + 1).padStart(2, '0');
    const dia = String(hoje.getDate()).padStart(2, '0');
    const dataAtual = `${ano}-${mes}-${dia}`;
    $('#dataInicio').val(dataAtual);

    // Carrega listas
    carregarTiposDeServico();
    inicializarTipoCliente();

    // (Opcional) insere o checkbox de agendar consulta no formulário
    const htmlCheckbox = `
        <div class="form-group form-check mt-3">
            <input type="checkbox" class="form-check-input" id="checkboxAgendarConsulta">
            <label class="form-check-label" for="checkboxAgendarConsulta">
                Deseja também agendar uma consulta para este serviço sendo inicializado?
            </label>
        </div>
    `;
    $('#arquivosServico').closest('.form-group').after(htmlCheckbox);

    // Botão "Iniciar Serviço"
    $('#btnIniciarServico').on('click', function(e) {
        e.preventDefault();

        // Valida campos obrigatórios
        const camposObrigatorios = [
            { campo: '#tipoServico', nome: 'Tipo de serviço' },
            { campo: '#tipoCliente', nome: 'Tipo de cliente' },
            { campo: '#clienteServico', nome: 'Cliente' },
            { campo: '#dataInicio', nome: 'Data de início' },
        ];

        const camposNaoPreenchidos = camposObrigatorios
            .filter(item => !$(item.campo).val())
            .map(item => `<li><i class="fas fa-exclamation-circle text-danger mr-1"></i>${item.nome}</li>`)
            .join('');

        if (camposNaoPreenchidos) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                html: `Preencha os seguintes campos obrigatórios antes de salvar:
                       <ul class="text-left mt-2">${camposNaoPreenchidos}</ul>`,
            });
            return;
        }

        console.log(`Arquivos selecionados: ${arquivosSelecionados.length}`);

        // Se o select do cliente estava desabilitado (checkbox "Cliente novo?" marcado), habilita para pegar valor
        const clienteSelect = $('#clienteServico');
        const estavaDesabilitado = clienteSelect.prop('disabled');
        if (estavaDesabilitado) clienteSelect.prop('disabled', false);

        // Monta o FormData
        const formData = new FormData();
        const form = $('#formIniciarServico')[0];
        for (let i = 0; i < form.elements.length; i++) {
            const field = form.elements[i];
            if (field.name && field.name !== 'anexos[]' && field.type !== 'file') {
                formData.append(field.name, field.value);
            }
        }

        // Anexa arquivos
        arquivosSelecionados.forEach(arquivo => {
            formData.append('anexos[]', arquivo);
        });

        // Se tem agendamento, manda junto
        if (agendamentoConsulta) {
            formData.append('agendar_consulta', '1');
            formData.append('data_hora_inicio', agendamentoConsulta.inicio);
            formData.append('data_hora_fim', agendamentoConsulta.fim);
            formData.append('motivo_agenda_id', '1');
        }

        // Restaura estado do select
        if (estavaDesabilitado) clienteSelect.prop('disabled', true);

        // Mostra alerta de "Salvando..."
        let podeFechar = false;
        const tempoMinimo = 1500;
        const tempoMaximo = 10000;

        Swal.fire({
            title: 'Salvando...',
            text: 'Aguarde, estamos criando o serviço.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                setTimeout(() => { podeFechar = true; }, tempoMinimo);
                setTimeout(() => { if (Swal.isVisible()) Swal.close(); }, tempoMaximo);
            }
        });

        // Envia Ajax
        $.ajax({
            url: '/servicos',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            processData: false,
            contentType: false,
            data: formData,
            success: function(resp) {
                // Fechar SweetAlert e abrir "Sucesso"
                const mostrarSwalSucesso = () => {
                    Swal.fire('Sucesso', 'Serviço iniciado com sucesso!', 'success');
                    $('#formIniciarServico')[0].reset();

                    // Coloca data de hoje novamente
                    const hoje = new Date();
                    const ano = hoje.getFullYear();
                    const mes = String(hoje.getMonth() + 1).padStart(2, '0');
                    const dia = String(hoje.getDate()).padStart(2, '0');
                    const dataAtual = `${ano}-${mes}-${dia}`;
                    $('#dataInicio').val(dataAtual);

                    // Reconstruir selects
                    if (tipoServicoChoices) tipoServicoChoices.destroy();
                    $('#tipoServico').removeAttr('data-choice');
                    carregarTiposDeServico();

                    const $tipoCliente = $('#tipoCliente');
                    if (tipoClienteChoices) tipoClienteChoices.destroy();
                    $tipoCliente.removeAttr('data-choice');
                    $tipoCliente.empty().append(`
                        <option value="">Selecione o tipo</option>
                        <option value="pf">Pessoa Física</option>
                        <option value="pj">Pessoa Jurídica</option>
                    `);
                    tipoClienteChoices = new Choices($tipoCliente[0], {
                        searchEnabled: false,
                        itemSelectText: '',
                        shouldSort: false,
                        placeholderValue: 'Selecione o tipo'
                    });

                    const $clienteSelect = $('#clienteServico');
                    if (clienteChoices) clienteChoices.destroy();
                    $clienteSelect.removeAttr('data-choice');
                    $clienteSelect.empty().append('<option value="">Selecione um cliente</option>');
                    $clienteSelect.prop('disabled', true);

                    // Zera variáveis
                    agendamentoConsulta = null;
                    arquivosSelecionados = [];
                    $('#arquivosServico').val('');
                    atualizarListaArquivos();
                };

                if (podeFechar) {
                    Swal.close();
                    mostrarSwalSucesso();
                } else {
                    setTimeout(() => {
                        Swal.close();
                        mostrarSwalSucesso();
                    }, tempoMinimo);
                }
            },
            error: function(xhr) {
                const mostrarErro = () => {
                    Swal.fire('Erro', xhr.responseJSON?.message || 'Erro ao iniciar serviço.', 'error');
                };

                if (podeFechar) {
                    Swal.close();
                    mostrarErro();
                } else {
                    setTimeout(() => {
                        Swal.close();
                        mostrarErro();
                    }, tempoMinimo);
                }
            }
        });
    });

    // Logo de cara, atualiza lista (caso o array não esteja vazio)
    atualizarListaArquivos();
});
