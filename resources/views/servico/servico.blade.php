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

                <!-- DataTables -->
                <h4 class="mt-5">Tipos de Serviços Cadastrados para o seu Escritório</h4>
                <table id="tabelaTipoServicos" class="table table-bordered table-hover" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Nome do Serviço</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dados preenchidos por AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const escritorioId = '{{ Auth::user()->escritorio->id }}';
    </script>

    <script src="{{ asset('js/servico/tipo_servico/tipo-servico-form-show.js') }}"></script>
    <script src="{{ asset('js/servico/tipo_servico/tipo-servico-form-store.js') }}"></script>
    <script src="{{ asset('js/servico/tipo_servico/tipo-servico-form-update.js') }}"></script>
    <script src="{{ asset('js/servico/tipo_servico/tipo-servico-form-delete.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Controle do Collapse
            $('.card-toggle-header').on('click', function(e) {
                e.preventDefault();
                let $card = $(this).closest('.card');
                let $cardBody = $card.find('.card-body');

                $cardBody.slideToggle();
                $card.toggleClass('collapsed-card');
            });

            // Cadastro novo serviço
            $("#formNovoServico").on("submit", function(e) {
                e.preventDefault();
                let nomeServico = $("#nomeServico").val();

                Swal.fire({ title: "Cadastrando...", allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                $.ajax({
                    url: '/servicos',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { nome_servico: nomeServico },
                    success: function(resp) {
                        Swal.fire("Sucesso!", resp.message || "Serviço cadastrado com sucesso!", "success");
                        $("#formNovoServico")[0].reset();
                        tabelaTipoServicos.ajax.reload(); // atualiza DataTables após cadastro
                    },
                    error: function(xhr) {
                        Swal.fire("Erro!", xhr.responseJSON?.message || "Erro ao cadastrar serviço.", "error");
                    }
                });
            });
        });
    </script>
@endpush
