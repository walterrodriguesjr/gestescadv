@extends('layouts.main')

@section('title', 'Serviços')

@section('content')

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

                    <!-- Tipo de Serviço -->
                    <div class="form-group">
                        <label for="tipoServico">Tipo de Serviço</label>
                        <select class="form-control" id="tipoServico" name="tipo_servico_id" required>
                            <option value="">Selecione um tipo de serviço</option>
                        </select>
                    </div>

                    <!-- Tipo de Cliente -->
                    <div class="form-group">
                        <label for="tipoCliente">Tipo de Cliente</label>
                        <select class="form-control" id="tipoCliente" name="tipo_cliente" required>
                            <option value="">Selecione o tipo</option>
                            <option value="pf">Pessoa Física</option>
                            <option value="pj">Pessoa Jurídica</option>
                        </select>
                    </div>

                    <!-- Cliente -->
                    <div class="form-group">
                        <label for="clienteServico">Cliente</label>
                        <select class="form-control" id="clienteServico" name="cliente_id" required disabled>
                            <option value="">Selecione um cliente</option>
                            <!-- Preenchido via AJAX no JS -->
                        </select>
                    </div>

                    <!-- Data de Início -->
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

                        <!-- Lista de arquivos selecionados (JS preenche) -->
                        <ul id="listaArquivosSelecionados" class="mt-2"></ul>
                    </div>

                    <!-- Botão de envio -->
                    <button type="submit" class="btn btn-primary float-right" id="btnIniciarServico">
                        <i class="fas fa-play"></i> Iniciar Serviço
                    </button>
                </form>
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

    <script>
        $(document).ready(function() {
            // Controle do Collapse (abrir/fechar card)
            $('.card-toggle-header').on('click', function(e) {
                e.preventDefault();
                let $card = $(this).closest('.card');
                let $cardBody = $card.find('.card-body');

                $cardBody.slideToggle();
                $card.toggleClass('collapsed-card');
            });

            // Exemplo: cadastro rápido de "Novo Tipo de Serviço"
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
                        Swal.fire("Sucesso!", resp.message || "Serviço cadastrado com sucesso!", "success");
                        $("#formNovoServico")[0].reset();

                        // Exemplo: se tiver DataTables para exibir "Tipos de Serviços"
                        tabelaTipoServicos.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire("Erro!", xhr.responseJSON?.message || "Erro ao cadastrar serviço.", "error");
                    }
                });
            });
        });
    </script>
@endpush
