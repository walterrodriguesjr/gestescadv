@extends('layouts.main')

@section('title', 'Dados do Escritório')

@section('content')


    {{-- Escritório --}}
    <div class="col-md-12">
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header d-flex align-items-center" data-card-widget="collapse">
                <h3 class="card-title mb-0">Dados do Escritório</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="dados-escritorio-form" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="nomeEscritorio">Nome do Escritório</label>
                        <input type="text" class="form-control" id="nomeEscritorio" name="nome_escritorio"
                            placeholder="Digite o nome do escritório" required
                            @cannot('gerenciar-escritorio') disabled @endcannot>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cnpjEscritorio">CNPJ</label>
                                <input type="text" class="form-control" id="cnpjEscritorio" name="cnpj_escritorio"
                                    placeholder="Digite o CNPJ" @cannot('gerenciar-escritorio') disabled @endcannot>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telefoneEscritorio">Telefone</label>
                                <input type="text" class="form-control" id="telefoneEscritorio"
                                    name="telefone_escritorio" placeholder="(00) 0000-0000"
                                    @cannot('gerenciar-escritorio') disabled @endcannot>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="celularEscritorio">Celular</label>
                                <input type="text" class="form-control" id="celularEscritorio" name="celular_escritorio"
                                    placeholder="(00) 00000-0000" @cannot('gerenciar-escritorio') disabled @endcannot>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="emailEscritorio">Email</label>
                                <input type="email" class="form-control" id="emailEscritorio" name="email_escritorio"
                                    placeholder="Digite o email do escritório"
                                    @cannot('gerenciar-escritorio') disabled @endcannot>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cepEscritorio">CEP</label>
                                <input type="text" class="form-control" id="cepEscritorio" name="cep_escritorio"
                                    placeholder="Digite o CEP" @cannot('gerenciar-escritorio') disabled @endcannot>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="logradouroEscritorio">Logradouro</label>
                                <input type="text" class="form-control" id="logradouroEscritorio"
                                    name="logradouro_escritorio" placeholder="Digite o logradouro"
                                    @cannot('gerenciar-escritorio') disabled @endcannot>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="numeroEscritorio">Número</label>
                                <input type="text" class="form-control" id="numeroEscritorio" name="numero_escritorio"
                                    placeholder="Número" @cannot('gerenciar-escritorio') disabled @endcannot>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bairroEscritorio">Bairro</label>
                                <input type="text" class="form-control" id="bairroEscritorio" name="bairro_escritorio"
                                    placeholder="Digite o bairro" @cannot('gerenciar-escritorio') disabled @endcannot>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estadoEscritorio">Estado</label>
                                <select class="form-control" id="estadoEscritorio" name="estado_escritorio"
                                    @cannot('gerenciar-escritorio') disabled @endcannot>
                                    <option value="">Selecione um estado</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cidadeEscritorio">Cidade</label>
                                <select class="form-control" id="cidadeEscritorio" name="cidade_escritorio"
                                    @cannot('gerenciar-escritorio') disabled @endcannot>
                                    <option value="">Selecione uma cidade</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-right">
                            @can('gerenciar-escritorio')
                                <button type="button" class="btn btn-primary" id="buttonSalvarDadosEscritorio"
                                    style="{{ Auth::user()->escritorio ? 'display: none;' : '' }}">
                                    <i class="fas fa-save"></i> Cadastrar Escritório
                                </button>
                        
                                <button type="button" class="btn btn-success" id="buttonAtualizarDadosEscritorio"
                                    style="{{ Auth::user()->escritorio ? '' : 'display: none;' }}">
                                    <i class="fas fa-edit"></i> Atualizar Escritório
                                </button>
                            @endcan
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Membros do escritório --}}
    @php
        $usuario = Auth::user();
        $niveisAcesso = $usuario->permissoes()->with('nivelAcesso')->get()->pluck('nivelAcesso.nome');
        $acessoPermitido = $niveisAcesso->intersect(['Administrador', 'Gestor'])->count() > 0;
    @endphp

    @if ($acessoPermitido)
        <div class="col-md-12">
            <div class="card card-outline card-primary collapsed-card">
                <div class="card-header d-flex align-items-center" data-card-widget="collapse">
                    <h3 class="card-title mb-0">Membros do Escritório</h3>
                    <div class="card-tools ml-auto">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Formulário de Cadastro de Membro -->
                    <form id="dados-membro-escritorio-form" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nomeMembro">Nome do Membro</label>
                                    <input type="text" class="form-control" id="nomeMembro" name="nome_membro"
                                        placeholder="Digite o nome do membro" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cpfMembro">CPF</label>
                                    <input type="text" class="form-control" id="cpfMembro" name="cpf_membro"
                                        placeholder="000.000.000-00" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emailMembro">E-mail</label>
                                    <input type="email" class="form-control" id="emailMembro" name="email_membro"
                                        placeholder="Digite o e-mail do membro" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nivelAcessoMembro">Nível de Acesso</label>
                                    <select class="form-control" id="nivelAcessoMembro" name="nivel_acesso_membro"
                                        required>
                                        <option value="">Selecione um nível</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-primary" id="buttonSalvarMembroEscritorio">
                                    <i class="fas fa-save"></i> Cadastrar Membro do Escritório
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Lista de Membros do Escritório -->
                    <hr> <!-- Separador visual -->
                    <div class="mt-3">
                        <h3 class="card-title">Lista de Membros do Escritório</h3>
                    </div>
                    <div class="mt-3">
                        <table id="membrosEscritorioTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Nível de Acesso</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
@php
    $usuario = Auth::user();
    $escritorio = $usuario->escritorio
        ?? ($usuario->membros()->with('escritorio')->first()->escritorio ?? null);
    $escritorioId = $escritorio->id ?? '';
@endphp

<script>
    // "Template" com placeholder :id
    let escritorioUpdateTemplate = "{{ route('dados-escritorio.update', ':id') }}";
    let escritorioShowTemplate   = "{{ route('dados-escritorio.show', ':id') }}";

    // Variáveis globais iniciais
    let escritorioId = "{{ $escritorioId }}";
    let escritorioUpdateUrl = escritorioId
        ? escritorioUpdateTemplate.replace(':id', escritorioId)
        : null;
    let escritorioShowUrl = escritorioId
        ? escritorioShowTemplate.replace(':id', escritorioId)
        : null;

    let escritorioStoreUrl = "{{ route('dados-escritorio.store') }}";
    let csrfToken = "{{ csrf_token() }}";

        /* URL index de listar niveis de acesso */
        const nivelAcessoIndexUrl = "{{ route('nivel-acesso.index') }}";

        /* URL de store para membros do escritorio */
        const membroStoreUrl = "{{ route('membro-escritorio.store') }}";

        let membroEscritorioShowUrl = escritorioId ?
            `{{ route('membro-escritorio.show', '__ID__') }}`.replace('__ID__', escritorioId) : '';
    </script>


    {{-- Dados Escritorio Script --}}
    <script src="{{ asset('js/escritorio/dados-escritorio/escritorio-form-show.js') }}"></script>
    <script src="{{ asset('js/escritorio/dados-escritorio/escritorio-form-store.js') }}"></script>
    <script src="{{ asset('js/escritorio/dados-escritorio/escritorio-form-update.js') }}"></script>

    {{-- Membros Escritorio Script --}}
    <script src="{{ asset('js/escritorio/membro-escritorio/membro-escritorio-form-show.js') }}"></script>
    <script src="{{ asset('js/escritorio/membro-escritorio/membro-escritorio-form-store.js') }}"></script>
    <script src="{{ asset('js/escritorio/membro-escritorio/membro-escritorio-form-update.js') }}"></script>
    <script src="{{ asset('js/escritorio/membro-escritorio/membro-escritorio-form-suspend.js') }}"></script>
    <script src="{{ asset('js/escritorio/membro-escritorio/membro-escritorio-form-reactivate.js') }}"></script>
    <script src="{{ asset('js/escritorio/membro-escritorio/membro-escritorio-form-delete.js') }}"></script>
@endpush
