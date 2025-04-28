@extends('layouts.main')

@section('title', 'Andamento do Serviço')

@section('content')
    {{-- botão Voltar, Inserir Andamento, Honorários e Editar/Inserir Número do Processo --}}
    <div class="row mb-3 align-items-start">
        {{-- Botão Voltar --}}
        <div class="col-md-3">
            <a href="{{ url()->previous() }}" onclick="event.preventDefault(); history.back();" class="btn btn-secondary w-100">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        {{-- Botões de ação: Arquivos, Novo Andamento, Honorários, Nº Processo --}}
        <div class="col-md-9 d-flex flex-column flex-md-row justify-content-md-end align-items-stretch gap-2 mt-3 mt-md-0">
            <button class="btn btn-dark" onclick="abrirModalDocumentosServico({{ $servico->id }})">
                <i class="fas fa-folder-open"></i> Todos os Arquivos
            </button>

            <button class="btn btn-primary" onclick="abrirModalHonorarios({{ $servico->id }})">
                <i class="fas fa-dollar-sign"></i> Honorários
            </button>

            <button class="btn btn-primary" onclick="abrirFormularioNovoAndamento({{ $servico->id }})">
                <i class="fas fa-plus"></i> Inserir Andamento
            </button>

            @if ($servico->numero_processo_formatado)
                <button class="btn btn-success"
                    onclick="abrirModalNumeroProcesso({{ $servico->id }}, '{{ $servico->numero_processo_formatado }}')">
                    <i class="fas fa-edit"></i> Editar número do processo
                </button>
            @else
                <button class="btn btn-primary"
                    onclick="abrirModalNumeroProcesso({{ $servico->id }}, null)">
                    <i class="fas fa-plus"></i> Inserir número do processo
                </button>
            @endif
        </div>
    </div>

    {{-- Informações do Serviço --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header"><strong>Informações do Serviço</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <strong>Tipo de Serviço:</strong><br>
                            {{ $servico->tipoServico->nome_servico ?? '—' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Tipo de Cliente:</strong><br>
                            {{ $servico->tipo_cliente === 'pessoa_fisica' ? 'Pessoa Física' : 'Pessoa Jurídica' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Data de Início:</strong><br>
                            {{ optional($servico->data_inicio)->format('d/m/Y') ?? '—' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Cliente:</strong><br>
                            {{ $clienteNome ?? '—' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>{{ $tipoDocumento ?? 'Documento' }}:</strong><br>
                            @if ($cpfCnpj && $tipoDocumento === 'cpf')
                                {{ preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $cpfCnpj) }}
                            @elseif ($cpfCnpj && $tipoDocumento === 'cnpj')
                                {{ preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $cpfCnpj) }}
                            @else
                                —
                            @endif
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Observações:</strong><br>
                            {{ $servico->observacoes ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dados do Processo --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header"><strong>Dados do Processo</strong></div>
                <div class="card-body">
                    <strong>Número do Processo:</strong><br>
                    @if ($servico->numero_processo_formatado)
                        <div class="d-flex align-items-center mt-1">
                            <span id="numProcessoCopy">{{ $servico->numero_processo_formatado }}</span>
                            <button class="btn btn-xs btn-link p-0 ml-1 text-primary" title="Copiar"
                                onclick="navigator.clipboard.writeText('{{ $servico->numero_processo_formatado }}')
                                         .then(() => toastr.success('Número copiado!'));">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        @if ($servico->consulta_processo_tribunal_url)
                            <a href="{{ $servico->consulta_processo_tribunal_url }}" target="_blank" rel="noopener"
                                class="small d-block mt-1">
                                <i class="fas fa-university"></i> Abrir site
                                {{ $servico->detalhes_processo['tribunal_sigla'] }}
                            </a>
                        @endif
                        <ul class="list-unstyled small mb-0 mt-1">
                            <li>Órgão:&nbsp;{{ $servico->detalhes_processo['orgao_nome'] ?? '—' }}</li>
                            <li>Tribunal:&nbsp;{{ $servico->detalhes_processo['tribunal_nome'] ?? '—' }}</li>
                            <li>Vara:&nbsp;{{ $servico->detalhes_processo['vara_codigo'] ?? '—' }}</li>
                        </ul>
                    @else
                        <span class="mt-1">—</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Timeline com scroll infinito --}}
    <div class="row">
        <div class="col-md-12">
            <div class="timeline-container border rounded shadow-sm p-3 mb-4"
                style="max-height: calc(100vh - 400px); overflow-y: auto;" data-servico="{{ $servico->id }}"
                data-cliente="{{ $servico->clienteFormatado->id }}" data-tipocliente="{{ $servico->tipo_cliente }}">
                <div class="timeline" id="timeline-conteudo">
                    {{-- Conteúdo será preenchido via JS --}}
                </div>
                <div id="carregando-indicador" class="text-center text-muted mt-3">
                    <i class="fas fa-spinner fa-spin"></i> Carregando mais andamentos...
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/moment-js/js/moment-with-locales.js') }}"></script>
    <script>
        moment.locale('pt-br');
    </script>
    <link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>

    <script>
        const servicoId = {{ $servico->id }};
        const clienteId = {{ $servico->clienteFormatado->id }};
        const tipoCliente = '{{ $servico->tipo_cliente }}';
    </script>

    <script src="{{ asset('js/servico/andamento_servico/observacoes-servico-store.js') }}"></script>
    <script src="{{ asset('js/servico/andamento_servico/arquivos-servico-andamento.js') }}"></script>
    <script src="{{ asset('js/servico/andamento_servico/arquivos-servico-andamento-store.js') }}"></script>
    <script src="{{ asset('js/servico/andamento_servico/arquivos-servico-andamento-listener.js') }}"></script>
    <script src="{{ asset('js/servico/andamento_servico/processo-servico-andamento-store.js') }}"></script>
    <script src="{{ asset('js/servico/andamento_servico/todos-arquivos-servicos-andamento.js') }}"></script>
@endpush
