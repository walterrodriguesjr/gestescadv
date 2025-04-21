@extends('layouts.main')

@section('title', 'Andamento do Serviço')

@section('content')
    {{-- botão Voltar --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    {{-- ──────────────── Card – Informações do Serviço ──────────────── --}}
    <div class="row mb-4">
        {{-- Informações do Serviço --}}
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Informações do Serviço</strong>
                </div>

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
                <div class="card-header">
                    <strong>Dados do Processo</strong>
                </div>

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

    {{-- ─────────────────────────── Timeline ─────────────────────────── --}}
    <div class="row">
        <div class="col-md-12">
            <div class="timeline">
                @foreach ($andamentos as $andamento)
                    <div>
                        <i class="fas fa-circle {{ $andamento->icone_cor }}"></i>

                        <div class="timeline-item">
                            <span class="time">
                                <i class="far fa-clock"></i>
                                {{ \Carbon\Carbon::parse($andamento->data_hora)->format('d/m/Y H:i') }}
                            </span>

                            <h3 class="timeline-header d-flex justify-content-between align-items-center">
                                <strong>{{ $andamento->etapa }}</strong>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-secondary"
                                        onclick="abrirArquivosSwal(
                                                    {{ $servico->id }},
                                                    {{ $servico->clienteFormatado->id }},
                                                    '{{ $servico->tipo_cliente }}',
                                                    '{{ $andamento->id }}')">
                                        <i class="fas fa-folder-open"></i> Arquivos
                                    </button>

                                    <button class="btn btn-sm btn-primary"
                                        onclick="abrirObservacaoSwal(
                                                    {{ $servico->id }},
                                                    '{{ addslashes($andamento->etapa) }}',
                                                    '{{ $andamento->data_hora }}')">
                                        <i class="fas fa-comment-dots"></i> Observações
                                    </button>
                                </div>
                            </h3>

                            <div class="timeline-body">
                                {!! nl2br(e($andamento->descricao)) ?: 'Sem descrição informada.' !!}

                                @if ($andamento->tipo === 'agenda')
                                    <div class="mt-2 text-muted small">
                                        Início:&nbsp;{{ \Carbon\Carbon::parse($andamento->data_hora)->format('d/m/Y H:i') }}<br>
                                        Término:&nbsp;{{ \Carbon\Carbon::parse($andamento->data_hora_fim)->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- fim da timeline --}}
                <div><i class="fas fa-clock bg-gray"></i></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Moment.js --}}
    <script src="{{ asset('vendor/moment-js/js/moment-with-locales.js') }}"></script>
    <script>
        moment.locale('pt-br');
    </script>

    {{-- Toastr (feedback “copiado”) --}}
    <link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>

    {{-- Observações e Arquivos --}}
    <script src="{{ asset('js/servico/andamento_servico/observacoes-servico-store.js') }}"></script>
    <script src="{{ asset('js/servico/andamento_servico/arquivos-servico-andamento-store.js') }}"></script>
@endpush
