@extends('layouts.main')

@section('title', 'Cadastro de Clientes')

@section('content')
    <div id="cardNovoCliente" class="col-md-12">
        <!-- Removemos data-card-widget="collapse" -->
        <div class="card card-outline card-primary collapsed-card">
            <!-- Adicionamos uma classe para clique, ex: "card-toggle-header" -->
            <div class="card-header d-flex align-items-center card-toggle-header">
                <h3 class="card-title mb-0">Novo Cliente</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Escolha do Tipo de Cliente -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tipoCliente">Selecione o Tipo de Cliente</label>
                        <select class="form-control" id="tipoCliente">
                            <option value="">Selecione</option>
                            <option value="pessoa_fisica">Pessoa Física</option>
                            <option value="pessoa_juridica">Pessoa Jurídica</option>
                        </select>
                    </div>
                </div>

                <!-- Formulário Pessoa Física -->
                <form id="formPessoaFisica" class="cliente-form d-none">
                    @csrf
                    <h4>Dados do Cliente (Pessoa Física)</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="nomeCliente">Nome</label>
                            <input type="text" class="form-control" id="nomeCliente" name="nome"
                                placeholder="Nome Completo" required>
                        </div>
                        <div class="col-md-4">
                            <label for="emailCliente">E-mail</label>
                            <input type="email" class="form-control" id="emailCliente" name="email"
                                placeholder="exemplo@email.com" required>
                        </div>
                        <div class="col-md-2">
                            <label for="cpfCliente">CPF</label>
                            <input type="text" class="form-control cpf-mask" id="cpfCliente" name="cpf"
                                placeholder="000.000.000-00" required>
                        </div>
                        <div class="col-md-2">
                            <label for="celularCliente">Celular</label>
                            <input type="text" class="form-control celular-mask" id="celularCliente" name="celular"
                                placeholder="(00) 00000-0000" required>
                        </div>
                    </div>

                    <h5 class="mt-4">Endereço</h5>
                    <div class="row">
                        <div class="col-md-2 position-relative">
                            <label for="cepCliente">CEP</label>
                            <input type="text" class="form-control cep-mask" id="cepCliente" name="cep"
                                placeholder="00000-000" data-toggle="popover" data-placement="top">
                        </div>

                        <div class="col-md-5">
                            <label for="logradouroCliente">Logradouro</label>
                            <input type="text" class="form-control" id="logradouroCliente" name="logradouro"
                                placeholder="Rua, Avenida, etc.">
                        </div>
                        <div class="col-md-1">
                            <label for="numeroCliente">Número</label>
                            <input type="text" class="form-control" id="numeroCliente" name="numero" placeholder="Nº">
                        </div>
                        <div class="col-md-4">
                            <label for="bairroCliente">Bairro</label>
                            <input type="text" class="form-control" id="bairroCliente" name="bairro"
                                placeholder="Bairro">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label for="estadoCliente">Estado</label>
                            <select class="form-control" id="estadoCliente" name="estado">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="cidadeCliente">Cidade</label>
                            <select class="form-control" id="cidadeCliente" name="cidade">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary mt-3 float-right" id="salvarPessoaFisica">
                        <i class="fas fa-save"></i> Cadastrar Cliente
                    </button>
                </form>

                <!-- Formulário Pessoa Jurídica -->
                <form id="formPessoaJuridica" class="cliente-form d-none">
                    @csrf
                    <h4>Dados do Cliente (Pessoa Jurídica)</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="razaoSocial">Razão Social</label>
                            <input type="text" class="form-control" id="razaoSocial" name="razao_social"
                                placeholder="Razão Social" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nomeFantasia">Nome Fantasia</label>
                            <input type="text" class="form-control" id="nomeFantasia" name="nome_fantasia"
                                placeholder="Nome Fantasia">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3 position-relative">
                            <label for="cnpjCliente">CNPJ</label>
                            <input type="text" class="form-control cnpj-mask" id="cnpjCliente" name="cnpj"
                                placeholder="00.000.000/0000-00" required data-toggle="popover" data-placement="top">
                        </div>
                        <div class="col-md-3">
                            <label for="telefoneJuridico">Telefone</label>
                            <input type="text" class="form-control telefone-mask" id="telefoneJuridico"
                                name="telefone" placeholder="(00) 0000-0000">
                        </div>
                        <div class="col-md-3">
                            <label for="celularJuridico">Celular</label>
                            <input type="text" class="form-control celular-mask" id="celularJuridico" name="celular"
                                placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-md-3">
                            <label for="emailJuridico">E-mail</label>
                            <input type="email" class="form-control" id="emailJuridico" name="email"
                                placeholder="exemplo@email.com" required>
                        </div>
                    </div>

                    <h5 class="mt-4">Endereço</h5>
                    <div class="row">
                        <div class="col-md-4 position-relative">
                            <label for="cepJuridico">CEP</label>
                            <input type="text" class="form-control cep-mask" id="cepJuridico" name="cep"
                                placeholder="00000-000" data-toggle="popover" data-placement="top">
                        </div>
                        <div class="col-md-6">
                            <label for="logradouroJuridico">Logradouro</label>
                            <input type="text" class="form-control" id="logradouroJuridico" name="logradouro"
                                placeholder="Rua, Avenida, etc.">
                        </div>
                        <div class="col-md-2">
                            <label for="numeroJuridico">Número</label>
                            <input type="text" class="form-control" id="numeroJuridico" name="numero"
                                placeholder="Nº">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="bairroJuridico">Bairro</label>
                            <input type="text" class="form-control" id="bairroJuridico" name="bairro"
                                placeholder="Bairro">
                        </div>
                        <div class="col-md-3">
                            <label for="estadoJuridico">Estado</label>
                            <select class="form-control" id="estadoJuridico" name="estado">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="cidadeJuridico">Cidade</label>
                            <select class="form-control" id="cidadeJuridico" name="cidade">
                                <option value="">Selecione</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary mt-3 float-right" id="salvarPessoaJuridica">
                        <i class="fas fa-save"></i> Cadastrar Cliente
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="cardListaClientes" class="col-md-12">
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header d-flex align-items-center card-toggle-header">
                <h3 class="card-title mb-0">Lista de Clientes</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tipoClienteListagem">Selecione o Tipo de Cliente</label>
                        <select id="tipoClienteListagem" class="form-control">
                            <option value="">Selecione</option>
                            <option value="pessoa_fisica">Pessoa Física</option>
                            <option value="pessoa_juridica">Pessoa Jurídica</option>
                        </select>
                    </div>
                </div>

                <table id="tabelaClientes" class="table table-bordered table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <!-- Cabeçalhos gerados dinamicamente -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dados via AJAX -->
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

    <script src="{{ asset('js/cliente/cliente-form-show.js') }}"></script>
    <script src="{{ asset('js/cliente/cliente-form-store.js') }}"></script>
    <script src="{{ asset('js/cliente/cliente-form-update.js') }}"></script>
    <script src="{{ asset('js/cliente/cliente-form-delete.js') }}"></script>
    <script src="{{ asset('js/cliente/cliente-form-documentos.js') }}"></script>

    <script>
        $(document).ready(function() {

            // Ao clicar no header (ou no botão) do card, abrimos/fechamos manualmente
            // ... (dentro de $(document).ready(...))
            $(document).on('click', '.card-toggle-header', function(e) {
                e.preventDefault();

                let $card = $(this).closest('.card');
                let $cardBody = $card.find('.card-body').first();

                if ($cardBody.is(':visible')) {
                    // === FECHAR O CARD ===
                    $cardBody.slideUp();
                    $card.addClass('collapsed-card');

                    // Se for o card de Novo Cliente (ID #cardNovoCliente)
                    if ($card.is('#cardNovoCliente, #cardNovoCliente .card')) {
                        // 1) Reseta o select "tipoCliente"
                        const instance = $('#tipoCliente').data('choicesInstance');
                        if (instance) {
                            instance.setChoiceByValue('');
                        } else {
                            $('#tipoCliente').val('');
                        }
                        // 2) Esconde formulários PF/PJ
                        $('.cliente-form').addClass('d-none');
                        // 3) Esconde popovers
                        $('#cepCliente, #cepJuridico, #cnpjCliente').popover('hide');
                    }

                    // Se for o card de Lista de Clientes
                    if ($card.is('#cardListaClientes, #cardListaClientes .card')) {
                        const instanceListagem = $('#tipoClienteListagem').data('choicesInstance');
                        if (instanceListagem) {
                            instanceListagem.setChoiceByValue('');
                        } else {
                            $('#tipoClienteListagem').val('');
                        }
                        // 3) Se quiser esconder popovers também, pode repetir se precisar
                        // $('#cepCliente, #cepJuridico, #cnpjCliente').popover('hide');
                    }

                } else {
                    // === ABRIR O CARD ===
                    $cardBody.slideDown();
                    $card.removeClass('collapsed-card');

                    // Fecha todos os OUTROS cards
                    $('.card.card-outline.card-primary').not($card).each(function() {
                        let $otherBody = $(this).find('.card-body').first();
                        if ($otherBody.is(':visible')) {
                            $otherBody.slideUp();
                            $(this).addClass('collapsed-card');

                            // Se for o card de Novo Cliente, também esconde popovers
                            if ($(this).is('#cardNovoCliente, #cardNovoCliente .card')) {
                                const inst = $('#tipoCliente').data('choicesInstance');
                                if (inst) {
                                    inst.setChoiceByValue('');
                                } else {
                                    $('#tipoCliente').val('');
                                }
                                $('.cliente-form').addClass('d-none');
                                $('#cepCliente, #cepJuridico, #cnpjCliente').popover('hide');
                            }
                            // Se for o card de Lista de Clientes, etc.
                            if ($(this).is('#cardListaClientes, #cardListaClientes .card')) {
                                const instList = $('#tipoClienteListagem').data('choicesInstance');
                                if (instList) {
                                    instList.setChoiceByValue('');
                                } else {
                                    $('#tipoClienteListagem').val('');
                                }
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
