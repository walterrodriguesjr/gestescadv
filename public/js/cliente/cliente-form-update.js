////////////////////////////////////////////////////////////////////////////////
// (A) EVENTO: BOTÃO .btn-editar => Abre o Swal principal
////////////////////////////////////////////////////////////////////////////////
$(document).on("click", ".btn-editar", async function() {
    const id = $(this).data("id");
    const tipo = $(this).data("tipo");

    try {
        // 1) Buscar lista de clientes do tipo (PF ou PJ)
        let response = await $.ajax({
            url: `/clientes/${tipo}`,
            type: "GET",
            data: { escritorio_id: escritorioId }
        });

        // 2) Achar o cliente pela ID
        let cliente = response.data.find(c => c.id == id);
        if (!cliente) {
            console.error("Cliente não encontrado");
            return;
        }

        // 3) Gerar HTML do formulário de edição
        let formHtml = gerarFormularioEdicao(tipo, cliente);

        // 4) Exibir Swal principal com esse HTML
        Swal.fire({
            title: "Editar Cliente",
            html: formHtml,
            width: "80%",
            showConfirmButton: false,
            showCancelButton: false,
            allowOutsideClick: false
        });

        // 5) Iniciar máscaras e validação
        inicializarMascaras(tipo);
        inicializarValidacaoFormulario(tipo);

        // 6) Iniciar selects de Estado e Cidade com Choices.js + IBGE
        await inicializarSelectEstadoCidade(tipo);

        // 7) Se já tiver estado/cidade no cliente, setar no select
        if (cliente.estado) {
            setSelectedValueEstado(tipo, cliente.estado);
            // Carrega cidades
            await carregarCidades(cliente.estado, getSelectCidade(tipo), "Selecione a cidade");
        }
        if (cliente.cidade) {
            setSelectedValueCidade(tipo, cliente.cidade);
        }

        // 8) Ao digitar CEP => auto-preenche
        inicializarAutoFillCEP(tipo);

        // 9) Se PJ => ao digitar CNPJ => auto-preenche
        if (tipo === "pessoa_juridica") {
            inicializarAutoFillCNPJ();
        }

        // 10) Botão “Salvar”
        $("#atualizarEdicaoCliente").on("click", function() {
            if (!$("#formEditarCliente").valid()) {
                // Form inválido => exibe erros e não fecha
                return;
            }
            atualizarCliente(); // PUT
        });

        // 11) Botão “Fechar” => fecha sem salvar
        $("#fecharEdicaoCliente").on("click", function() {
            Swal.close();
        });

    } catch (err) {
        console.error("Erro ao abrir modal de edição:", err);
    }
});


////////////////////////////////////////////////////////////////////////////////
// (B) GERA O FORMULÁRIO (PF ou PJ) COM MENSAGENS INLINE DE CEP/CNPJ
////////////////////////////////////////////////////////////////////////////////
function gerarFormularioEdicao(tipo, cliente) {

    // Helper para criar <input>
    function inputField(label, name, value = "", required = true) {
        return `
            <div class="col-md-6 mb-3">
                <label>${label}</label>
                <input type="text" class="form-control" name="${name}" 
                       value="${value}" ${required ? "required" : ""}>
            </div>
        `;
    }

    if (tipo === "pessoa_fisica") {
        return `
        <form id="formEditarCliente">
          <input type="hidden" name="id" value="${cliente.id}">
          <input type="hidden" name="tipo_cliente" value="pessoa_fisica">

          <div class="row">
            ${inputField("Nome", "nome", cliente.nome)}
            ${inputField("CPF", "cpf", cliente.cpf)}
            ${inputField("E-mail", "email", cliente.email)}
            ${inputField("Celular", "celular", cliente.celular, true)}
            ${inputField("Telefone", "telefone", cliente.telefone || "", false)}

            ${inputField("CEP", "cep", cliente.cep || "")}
            <!-- CEP status inline (PF) -->
            <small id="cepStatusPF" class="col-12 mb-2"></small>

            ${inputField("Logradouro", "logradouro", cliente.logradouro || "")}
            ${inputField("Número", "numero", cliente.numero || "", false)}
            ${inputField("Bairro", "bairro", cliente.bairro || "", false)}

            <div class="col-md-6 mb-3">
                <label>Estado</label>
                <select id="estadoSelectPF" class="form-control" name="estado" required>
                    <option value="">Selecione o estado</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Cidade</label>
                <select id="cidadeSelectPF" class="form-control" name="cidade" required>
                    <option value="">Selecione a cidade</option>
                </select>
            </div>
          </div>

          <div class="text-end mt-3">
            <button type="button" class="btn btn-secondary" id="fecharEdicaoCliente">
              <i class="fas fa-times"></i> Fechar
            </button>
            <button type="button" class="btn btn-success" id="atualizarEdicaoCliente">
              <i class="fas fa-save"></i> Atualizar
            </button>
          </div>
        </form>
        `;
    } else {
        // pessoa_juridica
        return `
        <form id="formEditarCliente">
          <input type="hidden" name="id" value="${cliente.id}">
          <input type="hidden" name="tipo_cliente" value="pessoa_juridica">

          <div class="row">
            ${inputField("Razão Social", "razao_social", cliente.razao_social)}
            ${inputField("Nome Fantasia", "nome_fantasia", cliente.nome_fantasia || "", false)}
            
            ${inputField("CNPJ", "cnpj", cliente.cnpj)}
            <small id="cnpjStatusPJ" class="col-12 mb-2"></small> <!-- Mensagem do CNPJ logo abaixo do input -->
            
            ${inputField("E-mail", "email", cliente.email)}
            ${inputField("Telefone", "telefone", cliente.telefone || "", false)}
            ${inputField("Celular", "celular", cliente.celular || "", true)}

            ${inputField("CEP", "cep", cliente.cep || "")}
            <!-- CEP status inline (PJ) -->
            <small id="cepStatusPJ" class="col-12 mb-2"></small>

            ${inputField("Logradouro", "logradouro", cliente.logradouro || "")}
            ${inputField("Número", "numero", cliente.numero || "", false)}
            ${inputField("Bairro", "bairro", cliente.bairro || "", false)}

            <div class="col-md-6 mb-3">
                <label>Estado</label>
                <select id="estadoSelectPJ" class="form-control" name="estado" required>
                    <option value="">Selecione o estado</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Cidade</label>
                <select id="cidadeSelectPJ" class="form-control" name="cidade" required>
                    <option value="">Selecione a cidade</option>
                </select>
            </div>
          </div>

          <div class="text-end mt-3">
            <button type="button" class="btn btn-secondary" id="fecharEdicaoCliente">
              <i class="fas fa-times"></i> Fechar
            </button>
            <button type="button" class="btn btn-success" id="atualizarEdicaoCliente">
              <i class="fas fa-save"></i> Atualizar
            </button>
          </div>
        </form>
        `;
    }
}


////////////////////////////////////////////////////////////////////////////////
// (C) MÁSCARAS e VALIDAÇÃO
////////////////////////////////////////////////////////////////////////////////
function inicializarMascaras(tipo) {
    $("input[name='cpf']").mask("000.000.000-00");
    $("input[name='cnpj']").mask("00.000.000/0000-00");
    $("input[name='cep']").mask("00000-000");
    $("input[name='telefone']").mask("(00) 0000-0000");
    $("input[name='celular']").mask("(00) 00000-0000");
}

function inicializarValidacaoFormulario(tipo) {
    let $form = $("#formEditarCliente");

    const rulesPF = {
        nome: { required: true },
        cpf: { required: true },
        celular: { required: true },
    };
    const rulesPJ = {
        razao_social: { required: true },
        cnpj: { required: true },
        celular: { required: true },
    };

    $form.validate({
        rules: (tipo === "pessoa_fisica") ? rulesPF : rulesPJ,
        messages: {
            nome: { required: "Informe o nome." },
            cpf: { required: "Informe o CPF." },
            
            razao_social: { required: "Informe a Razão Social." },
            cnpj: { required: "Informe o CNPJ." },
            celular: { required: "Informe o celular." },
        },
        errorClass: "text-danger small",
        errorElement: "span",
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        }
    });
}


////////////////////////////////////////////////////////////////////////////////
// (D) ESTADO e CIDADE via IBGE + Choices.js
////////////////////////////////////////////////////////////////////////////////
async function inicializarSelectEstadoCidade(tipo) {
    if (!window.listaEstadosCache) {
        let res = await $.getJSON("https://servicodados.ibge.gov.br/api/v1/localidades/estados");
        res.sort((a, b) => (a.nome > b.nome) ? 1 : -1);
        window.listaEstadosCache = res.map(uf => ({ sigla: uf.sigla, nome: uf.nome }));
    }

    let $estado = getSelectEstado(tipo);
    let $cidade = getSelectCidade(tipo);

    $estado.empty().append('<option value="">Selecione o estado</option>');
    window.listaEstadosCache.forEach(uf => {
        $estado.append(`<option value="${uf.sigla}">${uf.nome}</option>`);
    });

    criarChoices($estado);
    criarChoices($cidade);

    // Ao mudar estado => carrega cidades
    $estado.on("change", async function() {
        let uf = $(this).val();
        await carregarCidades(uf, $cidade, "Selecione a cidade");
    });
}

function getSelectEstado(tipo) {
    return tipo === "pessoa_fisica" ? $("#estadoSelectPF") : $("#estadoSelectPJ");
}
function getSelectCidade(tipo) {
    return tipo === "pessoa_fisica" ? $("#cidadeSelectPF") : $("#cidadeSelectPJ");
}

function criarChoices($select) {
    if ($select.data("choicesInstance")) {
        $select.data("choicesInstance").destroy();
    }
    let inst = new Choices($select[0], {
        shouldSort: false,
        searchPlaceholderValue: "Buscar...",
        noResultsText: "Nenhum resultado encontrado",
        noChoicesText: "Nenhuma opção disponível"
    });
    $select.data("choicesInstance", inst);
}

async function carregarCidades(uf, $cidadeSelect, placeholder = "Selecione a cidade") {
    let inst = $cidadeSelect.data("choicesInstance");
    if (inst) inst.destroy();

    $cidadeSelect.empty().append(`<option value="">${placeholder}</option>`);
    if (!uf) {
        criarChoices($cidadeSelect);
        return;
    }
    try {
        let res = await $.getJSON(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`);
        res.sort((a, b) => (a.nome > b.nome) ? 1 : -1);
        res.forEach(c => {
            $cidadeSelect.append(`<option value="${c.nome}">${c.nome}</option>`);
        });
    } catch (err) {
        console.error("Erro ao carregar cidades:", err);
    }
    criarChoices($cidadeSelect);
}

/** Ajusta valor do select (Estado) */
function setSelectedValueEstado(tipo, siglaUf) {
    let $estado = getSelectEstado(tipo);
    $estado.val(siglaUf);

    let inst = $estado.data("choicesInstance");
    if (inst) {
        inst.destroy();
        criarChoices($estado);
        $estado.val(siglaUf).trigger("change");
    }
}

/** Ajusta valor do select (Cidade) */
function setSelectedValueCidade(tipo, city) {
    let $cidade = getSelectCidade(tipo);
    $cidade.val(city);

    let inst = $cidade.data("choicesInstance");
    if (inst) {
        inst.destroy();
        criarChoices($cidade);
        $cidade.val(city).trigger("change");
    }
}


////////////////////////////////////////////////////////////////////////////////
// (E) BUSCA CEP => MENSAGEM INLINE (VERDE com ícone de check / VERMELHO com X)
////////////////////////////////////////////////////////////////////////////////
function inicializarAutoFillCEP(tipo) {
    if (tipo === "pessoa_fisica") {
        $(document).on("input", "input[name='cep']", async function() {
            let cep = $(this).val().replace(/\D/g, "");
            if (cep.length === 8) {
                await buscarCepPF(cep);
            }
        });
    } else {
        $(document).on("input", "input[name='cep']", async function() {
            let cep = $(this).val().replace(/\D/g, "");
            if (cep.length === 8) {
                await buscarCepPJ(cep);
            }
        });
    }
}

async function buscarCepPF(cep) {
    let $cepStatus = $("#cepStatusPF");
    $cepStatus.removeClass("text-success text-danger")
              .html('<i class="fas fa-spinner fa-spin"></i> Buscando CEP...');

    try {
        let data = await $.getJSON(`https://viacep.com.br/ws/${cep}/json/`);
        if (!data.erro) {
            $("input[name='logradouro']").val(data.logradouro);
            $("input[name='bairro']").val(data.bairro);

            setSelectedValueEstado("pessoa_fisica", data.uf);
            await carregarCidades(data.uf, $("#cidadeSelectPF"));
            setSelectedValueCidade("pessoa_fisica", data.localidade);

            $cepStatus.removeClass("text-danger").addClass("text-success")
                      .html('<i class="fas fa-check"></i> CEP localizado!');
        } else {
            $cepStatus.removeClass("text-success").addClass("text-danger")
                      .html('<i class="fas fa-times"></i> CEP inválido. Tente outro.');
        }
    } catch (err) {
        $cepStatus.removeClass("text-success").addClass("text-danger")
                  .html('<i class="fas fa-times"></i> Erro ao buscar CEP. Tente novamente.');
        console.error("Erro CEP PF:", err);
    }
}

async function buscarCepPJ(cep) {
    let $cepStatus = $("#cepStatusPJ");
    $cepStatus.removeClass("text-success text-danger")
              .html('<i class="fas fa-spinner fa-spin"></i> Buscando CEP...');

    try {
        let data = await $.getJSON(`https://viacep.com.br/ws/${cep}/json/`);
        if (!data.erro) {
            $("input[name='logradouro']").val(data.logradouro);
            $("input[name='bairro']").val(data.bairro);

            setSelectedValueEstado("pessoa_juridica", data.uf);
            await carregarCidades(data.uf, $("#cidadeSelectPJ"));
            setSelectedValueCidade("pessoa_juridica", data.localidade);

            $cepStatus.removeClass("text-danger").addClass("text-success")
                      .html('<i class="fas fa-check"></i> CEP localizado!');
        } else {
            $cepStatus.removeClass("text-success").addClass("text-danger")
                      .html('<i class="fas fa-times"></i> CEP inválido. Tente outro.');
        }
    } catch (err) {
        $cepStatus.removeClass("text-success").addClass("text-danger")
                  .html('<i class="fas fa-times"></i> Erro ao buscar CEP. Tente novamente.');
        console.error("Erro CEP PJ:", err);
    }
}


////////////////////////////////////////////////////////////////////////////////
// (F) BUSCA CNPJ => MENSAGEM INLINE (VERDE com check / VERMELHO com X)
////////////////////////////////////////////////////////////////////////////////
function inicializarAutoFillCNPJ() {
    // Vamos exibir mensagem no #cnpjStatusPJ
    $(document).on("input", "input[name='cnpj']", async function() {
        let cnpj = $(this).val().replace(/\D/g, "");
        if (cnpj.length === 14) {
            await buscarCnpjPJ(cnpj);
        }
    });
}

async function buscarCnpjPJ(cnpj) {
    let $cnpjStatus = $("#cnpjStatusPJ");
    $cnpjStatus.removeClass("text-success text-danger")
               .html('<i class="fas fa-spinner fa-spin"></i> Buscando CNPJ...');

    try {
        let dataBrasil = await $.getJSON(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`);
        // Se não deu erro => CNPJ localizado
        await preencherCnpjBrasil(dataBrasil);

        $cnpjStatus.removeClass("text-danger").addClass("text-success")
                   .html('<i class="fas fa-check"></i> CNPJ localizado!');
    } catch (err1) {
        // Tenta openCNPJ
        try {
            let dataOpen = await $.getJSON(`https://open.cnpja.com/office/${cnpj}`);
            await preencherCnpjOpen(dataOpen);

            $cnpjStatus.removeClass("text-danger").addClass("text-success")
                       .html('<i class="fas fa-check"></i> CNPJ localizado!');
        } catch (err2) {
            $cnpjStatus.removeClass("text-success").addClass("text-danger")
                       .html('<i class="fas fa-times"></i> CNPJ inválido ou API fora do ar.');
            console.warn("CNPJ não encontrado ou inválido");
        }
    }
}

/** Preencher com dados da BrasilAPI */
async function preencherCnpjBrasil(data) {
    if (data.erro) {
        console.warn("CNPJ inválido (BrasilAPI).");
        return;
    }
    $("input[name='razao_social']").val(data.razao_social || "");
    $("input[name='nome_fantasia']").val(data.nome_fantasia || "");
    if (data.cep) {
        $("input[name='cep']").val(data.cep);
        await buscarCepPJ(data.cep.replace(/\D/g, ""));
    }
    if (data.ddd_telefone_1) {
        let ddd = data.ddd_telefone_1.slice(0, 2);
        let num = data.ddd_telefone_1.slice(2);
        $("input[name='telefone']").val(`(${ddd}) ${num}`);
    }
    if (data.email) {
        $("input[name='email']").val(data.email);
    }
}

/** Preencher com dados do openCNPJ */
async function preencherCnpjOpen(data) {
    if (!data || data.error) {
        console.warn("CNPJ inválido (openCNPJ)");
        return;
    }
    if (data.address && data.address.zip) {
        $("input[name='cep']").val(data.address.zip);
        await buscarCepPJ(data.address.zip.replace(/\D/g, ""));
    }
    if (data.address) {
        $("input[name='logradouro']").val(data.address.street || "");
        $("input[name='bairro']").val(data.address.district || "");
        $("input[name='numero']").val(data.address.number || "");
    }
    if (data.company && data.company.name) {
        $("input[name='razao_social']").val(data.company.name);
    }
    if (data.alias) {
        $("input[name='nome_fantasia']").val(data.alias);
    }
    if (data.emails && data.emails.length > 0) {
        $("input[name='email']").val(data.emails[0].address || "");
    }
    if (data.phones && data.phones.length > 0) {
        let fone = data.phones[0];
        $("input[name='telefone']").val(`(${fone.area}) ${fone.number}`);
    }
}


////////////////////////////////////////////////////////////////////////////////
// (G) PUT => ATUALIZA => EXIBIR “Atualizando...” por min. 1.5s / max. 10s
////////////////////////////////////////////////////////////////////////////////
async function atualizarCliente() {
    let formData = $("#formEditarCliente").serialize();
    let id = $("input[name='id']").val();
    let csrfToken = $('meta[name="csrf-token"]').attr("content");

    // 1) Exibe Swal de "Atualizando..." com loading
    Swal.fire({
        title: "Atualizando...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    // 2) Tempo mínimo de 1.5s antes de fechar
    const tempoMinimo = new Promise(resolve => setTimeout(resolve, 1500));

    $.ajax({
        url: `/clientes/${id}`,
        type: "PUT",
        headers: { "X-CSRF-TOKEN": csrfToken },
        data: formData,

        success: async function(resp) {
            await tempoMinimo; // aguarda 1.5s
            // Exibe sucesso, até no máx. 10s
            Swal.fire({
                title: "Sucesso!",
                text: resp.message || "Cliente atualizado com sucesso!",
                icon: "success",
                timer: 10000,
                timerProgressBar: true
            }).then(() => {
                // Reload DataTable
                $("#tabelaClientes").DataTable().ajax.reload();
            });
        },

        error: async function(xhr) {
            await tempoMinimo;
            let errorMsg = "Erro ao atualizar cliente.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire({
                title: "Erro!",
                text: errorMsg,
                icon: "error",
                timer: 10000,
                timerProgressBar: true
            });
            console.error(errorMsg);
        }
    });
}
