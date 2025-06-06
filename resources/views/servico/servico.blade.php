@extends('layouts.main')

@section('title', 'Serviços')

@section('content')

    {{-- collapse cadastrar novo servico --}}
    <div id="cardNovoServico" class="col-md-12">
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header d-flex align-items-center card-toggle-header">
                <h3 class="card-title mb-0">Cadastrar Novo Tipo de Serviço</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body" style="display:none;">
                <form id="formNovoServico">
                    @csrf
                    <div class="form-group">
                        <label for="nomeServico">Nome do Tipo de Serviço</label>
                        <input type="text" class="form-control" id="nomeServico" name="nome_servico"
                            placeholder="Informe o nome do serviço" required>
                    </div>

                    <button type="button" class="btn btn-primary float-right" id="salvarTipoServico">
                        <i class="fas fa-save"></i> Cadastrar
                    </button>
                </form>

                <!-- DataTables (exemplo) -->
                <h4 class="mt-5">Tipos de Serviços Cadastrados para o seu Escritório</h4>
                <table id="tabelaTipoServicos" class="table table-bordered table-hover" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Nome do Serviço</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dados preenchidos via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- collapse iniciar servico --}}
    <div id="cardIniciarServico" class="col-md-12">
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header d-flex align-items-center card-toggle-header">
                <h3 class="card-title mb-0">Iniciar Serviço</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body" style="display:none;">
                <form id="formIniciarServico" enctype="multipart/form-data">
                    @csrf

                    <!-- Linha com os 3 selects -->
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="tipoServico">Tipo de Serviço</label>
                            <select class="form-control" id="tipoServico" name="tipo_servico_id" required>
                                <option value="">Selecione</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="tipoCliente">Tipo de Cliente</label>
                            <select class="form-control" id="tipoCliente" name="tipo_cliente" required>
                                <option value="">Selecione</option>
                                <option value="pf">Pessoa Física</option>
                                <option value="pj">Pessoa Jurídica</option>
                            </select>
                        </div>

                        <div class="form-group col-md-5">
                            <label for="clienteServico">Cliente</label>
                            <select class="form-control" id="clienteServico" name="cliente_id" required disabled>
                                <option value="">Selecione</option>
                            </select>
                        </div>
                    </div>

                    <!-- Número do Processo (opcional) -->
                    <div class="form-group">
                        <label for="numeroProcesso">
                            Número do processo <small class="text-muted">(CNJ, opcional, poderá ser inserido posteriomente)</small>
                        </label>
                        <input type="text"
                               class="form-control processo-mask"
                               id="numeroProcesso"
                               name="numero_processo"
                               placeholder="0000000-00.0000.0.00.0000">
                    </div>

                    <!-- Data -->
                    <div class="form-group">
                        <label for="dataInicio">Data de Início</label>
                        <input type="date" class="form-control" id="dataInicio" name="data_inicio" required>
                    </div>

                    <!-- Observações -->
                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"
                                  placeholder="Descreva detalhes iniciais, observações, etc..."></textarea>
                    </div>

                    <!-- Upload de Arquivos -->
                    <div class="form-group">
                        <label for="arquivosServico">Anexos</label>
                        <input type="file" class="form-control" id="arquivosServico" name="anexos[]" multiple
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                        <small class="form-text text-muted">Você pode anexar documentos, imagens, PDFs, etc.</small>

                        <ul id="listaArquivosSelecionados" class="mt-2"></ul>
                    </div>

                    <!-- Botão -->
                    <button type="submit" class="btn btn-primary float-right" id="btnIniciarServico">
                        <i class="fas fa-play"></i> Iniciar Serviço
                    </button>
                </form>
            </div>
        </div>
    </div>


    <!-- Collapse: Acompanhar Serviços -->
    <div id="cardListarServicos" class="col-md-12">
        <div class="card card-outline card-primary collapsed-card"> <!-- collapsed por padrão -->
            <div class="card-header d-flex align-items-center card-toggle-header">
                <h3 class="card-title mb-0">Acompanhar Serviços</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body" style="display: none;">
                <!-- Filtros de Tipo de Cliente -->
                <div class="d-flex justify-content-start gap-2 mb-3">
                    <button id="btnFiltroPF" class="btn btn-outline-primary">
                        <i class="fas fa-user"></i> Pessoa Física
                    </button>
                    <button id="btnFiltroPJ" class="btn btn-outline-success">
                        <i class="fas fa-building"></i> Pessoa Jurídica
                    </button>
                </div>

                <!-- Tabela de Serviços -->
                <div class="table-responsive">
                    <table id="tabelaServicos" class="table table-striped table-bordered w-100">
                        <thead class="thead-light">
                            <tr>
                                <th>Nome</th>
                                <th>CPF / CNPJ</th>
                                <th>Celular</th>
                                <th>Etapa Atual</th>
                                <th style="width: 100px;">Ações</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection



@push('scripts')
    <!-- ID do Escritório para uso no JS -->
    <script>
        const escritorioId = '{{ Auth::user()->escritorio->id }}';
    </script>

    <!-- Scripts de Tipos de Serviços (exemplos) -->
    <script src="{{ asset('js/servico/tipo_servico/tipo-servico-form-show.js') }}"></script>
    <script src="{{ asset('js/servico/tipo_servico/tipo-servico-form-store.js') }}"></script>
    <script src="{{ asset('js/servico/tipo_servico/tipo-servico-form-update.js') }}"></script>
    <script src="{{ asset('js/servico/tipo_servico/tipo-servico-form-delete.js') }}"></script>

    <!-- Script com toda a lógica de iniciar serviço, anexos etc. -->
    <script src="{{ asset('js/servico/novo_servico/novo-servico-form-store.js') }}"></script>

    {{-- Script de Listar Serviços --}}
    <script src="{{ asset('js/servico/lista_servico/lista-servico-form-show.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Collapse toggle
            $('.card-toggle-header').on('click', function(e) {
                e.preventDefault();
                let $card = $(this).closest('.card');
                let $cardBody = $card.find('.card-body');

                $cardBody.slideToggle();
                $card.toggleClass('collapsed-card');
            });

            // Cadastro rápido de novo tipo de serviço
            $("#formNovoServico").on("submit", function(e) {
                e.preventDefault();
                let nomeServico = $("#nomeServico").val();

                Swal.fire({
                    title: "Cadastrando...",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: '/servicos',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        nome_servico: nomeServico
                    },
                    success: function(resp) {
                        Swal.fire("Sucesso", resp.message || "Serviço cadastrado com sucesso!",
                            "success");
                        $("#formNovoServico")[0].reset();
                        if (window.tabelaTipoServicos) tabelaTipoServicos.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire("Erro", xhr.responseJSON?.message ||
                            "Erro ao cadastrar serviço.", "error");
                    }
                });
            });
        });
    </script>
@endpush
