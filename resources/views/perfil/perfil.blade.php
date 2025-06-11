@extends('layouts.main')

@section('title', 'Perfil')

@section('content')


    {{-- Dados Pessoais --}}
<div class="col-md-12">
    <div class="card card-outline card-primary collapsed-card">
        <div class="card-header d-flex align-items-center" data-card-widget="collapse">
            <h3 class="card-title m-0">Dados Pessoais</h3>
            <div class="card-tools ml-auto">
                <button type="button" class="btn btn-tool">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">

            <!-- Aviso de Segurança e LGPD -->
            <div id="mensagemSeguranca" class="alert alert-info">
                <i class="fas fa-shield-alt"></i> Seus dados pessoais são armazenados de forma segura e criptografada,
                conforme a <strong>Lei Geral de Proteção de Dados (LGPD - Lei nº 13.709/2018)</strong>.
            </div>

            <form id="meus-dados-form" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Foto do Usuário -->
                <div class="row mb-3">
                    <div class="col-md-4 text-center">
                        <label>Foto de Perfil</label>
                        <div class="d-flex flex-column align-items-center">
                            <img id="fotoPreview" 
                                src="{{ asset(Auth::user()->foto ? 'storage/foto-perfil/' . Auth::user()->foto : 'storage/foto-perfil/sem-foto.jpg') }}" 
                                class="rounded-circle border shadow-sm" 
                                style="width: 120px; height: 120px; object-fit: cover;">
                            
                            <input type="file" class="form-control mt-2" id="fotoUsuario" name="foto_usuario" accept="image/*">
                            <small class="text-muted">Apenas imagens (JPG, PNG, GIF). Tamanho máx: 5MB.</small>
                        </div>
                    </div>
                </div>

                <!-- Nome -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="nomeUsuario">Nome</label>
                        <input type="text" class="form-control" id="nomeUsuario" name="nome_usuario"
                            value="{{ Auth::user()->name }}" required>
                    </div>
                </div>

                <!-- Email -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="emailUsuario">Email</label>
                        <input type="email" class="form-control" id="emailUsuario" name="email_usuario"
                            value="{{ Auth::user()->email }}" required>
                    </div>
                </div>

                <div class="row">
                    <!-- CPF -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cpfUsuario">CPF</label>
                            <input type="text" class="form-control" id="cpfUsuario" name="cpf_usuario"
                                placeholder="Digite seu CPF">
                        </div>
                    </div>

                    <!-- Celular -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="celularUsuario">Celular</label>
                            <input type="text" class="form-control" id="celularUsuario" name="celular_usuario"
                                placeholder="(00) 00000-0000">
                        </div>
                    </div>

                    <!-- Data de Nascimento -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dataNascimentoUsuario">Data de Nascimento</label>
                            <input type="date" class="form-control" id="dataNascimentoUsuario"
                                name="data_nascimento_usuario">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Estado -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estadoUsuario">Estado</label>
                            <select class="form-control" id="estadoUsuario" name="estado_usuario" style="width: 100%;">
                                <option value="">Selecione um estado</option>
                            </select>
                        </div>
                    </div>
                    <!-- Cidade -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cidadeUsuario">Cidade</label>
                            <select class="form-control" id="cidadeUsuario" name="cidade_usuario" style="width: 100%;">
                                <option value="">Selecione uma cidade</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- OAB -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="oabUsuario">OAB</label>
                            <input type="text" class="form-control" id="oabUsuario" name="oab_usuario"
                                placeholder="Número da OAB">
                        </div>
                    </div>

                    <!-- Estado OAB -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estadoOabUsuario">Estado OAB</label>
                            <select class="form-control" id="estadoOabUsuario" name="estado_oab_usuario"
                                style="width: 100%;">
                                <option value="">Selecione um estado da OAB</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-success" id="buttonSalvarDadosUsuarios">
                            <i class="fas fa-edit"></i> Atualizar Dados
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Alterar Senha -->
    <div class="col-md-12">
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header d-flex align-items-center" data-card-widget="collapse">
                <h3 class="card-title m-0">Alterar Senha</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Aviso sobre os requisitos da senha -->
                <div id="mensagemSenha" class="alert alert-warning">
                    <i class="fas fa-exclamation-circle"></i> Sua nova senha deve conter:
                    <ul class="mb-0">
                        <li id="requisito-comprimento"><i class="fas fa-times-circle text-danger"></i> Pelo menos
                            <strong>8 caracteres</strong>
                        </li>
                        <li id="requisito-maiuscula"><i class="fas fa-times-circle text-danger"></i> Uma <strong>letra
                                maiúscula</strong></li>
                        <li id="requisito-minuscula"><i class="fas fa-times-circle text-danger"></i> Uma <strong>letra
                                minúscula</strong></li>
                        <li id="requisito-numero"><i class="fas fa-times-circle text-danger"></i> Um
                            <strong>número</strong>
                        </li>
                        <li id="requisito-especial"><i class="fas fa-times-circle text-danger"></i> Um <strong>caractere
                                especial</strong> (@, $, !, %, *, ?, &...)</li>
                    </ul>
                </div>


                <form id="alterar-senha-form" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="senhaAtual">Senha Atual</label>
                        <input type="password" class="form-control" id="senhaAtual" name="senha_atual" required
                            placeholder="Digite sua senha atual">
                    </div>
                    <div class="form-group">
                        <label for="novaSenha">Nova Senha</label>
                        <input type="password" class="form-control" id="novaSenha" name="nova_senha" required
                            placeholder="Digite sua nova senha">
                    </div>
                    <div class="form-group">
                        <label for="confirmarSenha">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="confirmarSenha" name="nova_senha_confirmation"
                            required placeholder="Confirme sua nova senha">
                    </div>
                    <button type="button" class="btn btn-success float-right" id="buttonAlterarSenha">
                        <i class="fas fa-save"></i> Alterar Senha
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Autenticação de Dois Fatores -->
    <div class="col-md-12">
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header d-flex align-items-center" data-card-widget="collapse">
                <h3 class="card-title m-0">Autenticação de Dois Fatores</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="formAutenticacaoDoisFatores">
                    @csrf

                    <!-- Mensagem condicional sobre status da 2FA -->
                    <div id="mensagem2FA"
                        class="alert {{ Auth::user()->two_factor_enabled ? 'alert-info' : 'alert-danger' }}">
                        {{ Auth::user()->two_factor_enabled
                            ? 'Sua autenticação de dois fatores já está ativa. Caso queira desabilitar, clique no botão abaixo.'
                            : 'Clique para habilitar sua autenticação de dois fatores.' }}

                        <!-- Texto explicativo adicionado abaixo da mensagem condicional -->
                        @if (!Auth::user()->two_factor_enabled)
                            <br><br>
                            <strong>Proteja sua conta!</strong> A <strong>Autenticação de Dois Fatores (2FA)</strong>
                            adiciona
                            uma camada extra de segurança à sua conta, dificultando acessos não autorizados, mesmo que sua
                            senha seja comprometida.
                            <br><br>
                            Ao ativar a 2FA, será necessário confirmar sua identidade através de um segundo fator (e-mail)
                            sempre que fizer login, tornando sua conta muito mais segura contra invasões e tentativas de
                            fraude.
                            <br><br>
                            Recomendamos fortemente que você ative esse recurso para garantir a <strong>máxima
                                segurança</strong> dos seus dados e informações pessoais.
                        @endif
                    </div>

                    <!-- Opção para ativar/desativar (Switch estilo Apple) -->
                    <div class="form-group">
                        <label for="switch2FA">Ativar Autenticação de Dois Fatores?</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="switch2FA" name="dois_fatores"
                                {{ Auth::user()->two_factor_enabled ? 'checked' : '' }}>
                            <label class="custom-control-label" for="switch2FA"></label>
                        </div>
                    </div>

                    <!-- Seleção do método (somente aparece se 2FA estiver ativado) -->
                    <div class="form-group" id="metodoAutenticacao"
                        style="display: {{ Auth::user()->two_factor_enabled ? 'block' : 'none' }};">
                        <label>Escolha o método de autenticação</label>
                        <select class="form-control select2" id="tipoAutenticacao" name="tipo_autenticacao"
                            style="width: 100%;">
                            <option value="email" {{ Auth::user()->two_factor_type == 'email' ? 'selected' : '' }}>E-mail
                            </option>
                            {{-- <option value="sms" {{ Auth::user()->two_factor_type == 'sms' ? 'selected' : '' }}>SMS</option> --}}
                            {{-- <option value="app" {{ Auth::user()->two_factor_type == 'app' ? 'selected' : '' }}>Google Authenticator</option> --}}
                        </select>
                    </div>

                    <button type="button" class="btn btn-success float-right"
                        id="buttonAlterarLaterarAutenticaoDoisFatores">
                        <i class="fas fa-lock"></i> Atualizar Segurança
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Gerenciamento de Sessões Ativas -->
    <div class="col-md-12">
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header d-flex align-items-center" data-card-widget="collapse">
                <h3 class="card-title m-0">Gerenciamento de Sessões Ativas</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Aviso informativo -->
                <div id="mensagemSessoes" class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aqui você pode visualizar e encerrar acessos ativos à sua conta em
                    outros dispositivos.
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>Dispositivo</th>
                            <th>Última Atividade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="listaSessoes">
                        <tr>
                            <td colspan="4" class="text-center">Carregando sessões ativas...</td>
                        </tr>
                    </tbody>
                </table>
                <button id="encerrarTodasSessoes" class="btn btn-danger float-right mt-2">
                    <i class="fas fa-sign-out-alt"></i> Encerrar todas as sessões
                </button>
            </div>
        </div>
    </div>

    <!-- Exportação de Dados -->
    <div class="col-md-12">
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header d-flex align-items-center" data-card-widget="collapse">
                <h3 class="card-title m-0">Exportação de Dados</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Alerta informativo sobre a LGPD e GDPR -->
                <div id="mensagemLGPD" class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong> Atenção:</strong> Esta funcionalidade está em conformidade
                    com a
                    <strong>Lei Geral de Proteção de Dados (LGPD, Art. 18, Incisos II e V)</strong> e o
                    <strong>Regulamento Geral de Proteção de Dados (GDPR, Art. 15 e 20)</strong>.
                    Como titular dos dados, você tem o direito de acessar e exportar suas informações pessoais armazenadas
                    no sistema.
                    <br><br>
                    Para garantir a transparência e a segurança dos seus dados, todas as informações criptografadas
                    serão devidamente <strong>descriptografadas</strong> antes da exportação, conforme previsto pela
                    legislação, permitindo que você visualize suas informações de forma legível e acessível.
                </div>

                <p>Você pode baixar uma cópia dos seus dados armazenados no sistema nos formatos disponíveis.</p>
                <button class="btn btn-primary" id="baixarDadosJSON">
                    <i class="fas fa-download"></i> Baixar JSON
                </button>
                <button class="btn btn-success" id="baixarDadosCSV">
                    <i class="fas fa-file-csv"></i> Baixar CSV
                </button>
            </div>
        </div>
    </div>

    {{-- Histórico de Alterações --}}
    <div class="col-md-12">
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header d-flex align-items-center" data-card-widget="collapse">
                <h3 class="card-title m-0">Histórico de Alterações</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">

                <!-- 🛡️ Mensagem de Informação sobre LGPD -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Aviso:</strong> O sistema armazena seu histórico de alterações de dados pessoais
                    conforme recomendação da <strong>Lei Geral de Proteção de Dados (LGPD - Lei nº 13.709/2018)</strong>.
                    Essa medida visa garantir transparência e segurança sobre suas informações.
                </div>

                <table class="table table-bordered" id="tabelaHistorico">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Valor Anterior</th>
                            <th>Valor Novo</th>
                            <th>Data da Alteração</th>
                        </tr>
                    </thead>
                    <tbody id="historicoAlteracoes">
                        <tr>
                            <td colspan="4" class="text-center">Carregando...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Exclusão de Conta -->
    <div class="col-md-12">
        <div class="card card-outline card-danger collapsed-card">
            <div class="card-header d-flex align-items-center" data-card-widget="collapse">
                <h3 class="card-title m-0">Excluir Conta</h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Alerta de Atenção -->
                <div id="mensagemExclusao" class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Atenção!</strong>
                    <br>
                    Ao excluir sua conta, **todos os seus dados serão permanentemente apagados** e não poderão ser
                    recuperados.
                    <br><br>
                    <strong>O que será excluído?</strong>
                    <ul class="mb-0">
                        <li>Seu perfil e credenciais de acesso.</li>
                        <li>Todos os seus dados armazenados no sistema.</li>
                        <li>Histórico de atividades e sessões ativas.</li>
                    </ul>
                    <br>
                    <strong>Importante:</strong> Após confirmar a exclusão, seus dados serão apagados definitivamente
                    conforme a Lei Geral de Proteção de Dados (LGPD)</strong>.
                </div>

                <!-- Formulário para confirmação da exclusão -->
                <form id="formExcluirConta" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="senhaConfirmacao">Digite sua senha para confirmar:</label>
                        <input type="password" class="form-control" id="senhaConfirmacao" name="senha_confirmacao"
                            required placeholder="Digite sua senha">
                    </div>

                    <!-- Checkbox de confirmação -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="confirmarExclusao">
                            <label class="custom-control-label" for="confirmarExclusao">
                                Eu entendo que esta ação é <strong>irreversível</strong> e desejo excluir minha conta.
                            </label>
                        </div>
                    </div>

                    <!-- Botão de Exclusão -->
                    <button type="button" class="btn btn-danger float-right" id="buttonExcluirConta" disabled>
                        <i class="fas fa-trash"></i> Excluir Minha Conta
                    </button>
                </form>
            </div>
        </div>
    </div>



@endsection

@push('scripts')
    {{-- Scripts Meus Dados --}}
    <script src="{{ asset('js/perfil/meus-dados/perfil-form-update.js') }}"></script>

    {{-- Scripts Alterar Senha --}}
    <script src="{{ asset('js/perfil/alterar-senha/alterar-senha-form-update.js') }}"></script>

    {{-- Scripts Alterar Autenticacao de dois fatores --}}
    <script
        src="{{ asset('js/perfil/alterar-autenticacao-dois-fatores/alterar-autenticacao-dois-fatores-form-update.js') }}">
    </script>

    {{-- Scripts Gerenciar Sessões Ativas --}}
    <script src="{{ asset('js/perfil/gerenciar-sessoes-ativas/gerenciar-sessoes-ativas-form.js') }}"></script>

    {{-- Scripts Exportar Dados --}}
    <script src="{{ asset('js/perfil/exportar-dados/exportar-dados-form.js') }}"></script>

    {{-- Scripts Histótico de Alterações --}}
    <script src="{{ asset('js/perfil/historico-alteracoes/historico-alteracoes-form.js') }}"></script>

    {{-- Scripts Exluir Conta --}}
    <script src="{{ asset('js/perfil/excluir-conta/excluir-conta-form.js') }}"></script>

    <script>
        const userId = "{{ Auth::id() }}"; // Armazena o ID do usuário logado
        const perfilUpdateUrl = "{{ route('perfil.update', ['perfil' => Auth::id()]) }}";
        const perfilShowUrl = "{{ route('perfil.show', ['perfil' => ':id']) }}".replace(':id', userId); // URL dinâmica
        const csrfToken = "{{ csrf_token() }}";
    </script>
@endpush
