@extends('layouts.main')

@section('title', 'Despesas')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Despesas</h4>
            <button id="btnNovaDespesa" class="btn btn-primary">
                <i class="fas fa-plus"></i> Criar novo tipo de despesa
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form id="formCadastrarDespesa" class="mb-4">
                    @csrf
                    <input type="hidden" name="escritorio_id" value="{{ auth()->user()->escritorio_id }}">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label for="tipo_despesa_id" class="form-label">Tipo de Despesa</label>
                            <select name="tipo_despesa_id" id="tipo_despesa_id" class="form-select w-100" required>
                                <option value="">Selecione o tipo de despesa</option>
                                @foreach ($tiposDespesa as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->titulo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="valor" class="form-label">Valor (R$)</label>
                            <input type="text" name="valor" id="valor" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="data_vencimento" class="form-label">Data de Vencimento</label>
                            <input type="date" name="data_vencimento" id="data_vencimento" class="form-control" required>

                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="btnSalvarDespesa">
                                <i class="fas fa-save"></i> Salvar
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mb-3 d-flex align-items-center gap-2" id="filtroMesesDespesa">
                    <button type="button" class="btn btn-outline-secondary btn-mes-despesa" data-mes="0">Todos</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="1">Jan</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="2">Fev</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="3">Mar</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="4">Abr</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="5">Mai</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="6">Jun</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="7">Jul</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="8">Ago</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="9">Set</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="10">Out</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="11">Nov</button>
                    <button type="button" class="btn btn-outline-primary btn-mes-despesa" data-mes="12">Dez</button>
                    <select id="selectAnoDespesa" class="form-select w-auto ms-2"></select>
                </div>

                <div class="table-responsive">
                    <table id="tabelaDespesas" class="table table-bordered table-striped w-100 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Data de Vencimento</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados via Ajax -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const escritorioId = '{{ Auth::user()->escritorio->id }}';
    </script>


    <!-- Scripts de Despesas -->
    <script src="{{ asset('js/despesa/tipo-despesa-form-create.js') }}"></script>
    <script src="{{ asset('js/despesa/despesa-form-index.js') }}"></script>
    <script src="{{ asset('js/despesa/despesa-form-show.js') }}"></script>
    <script src="{{ asset('js/despesa/despesa-form-store.js') }}"></script>
    <script src="{{ asset('js/despesa/despesa-form-update.js') }}"></script>
    <script src="{{ asset('js/despesa/despesa-form-delete.js') }}"></script>

    <script>
        // Define a URL global usando Blade
        const urlTipoDespesaStore = "{{ route('tipo-despesas.store') }}";
        const urlTipoDespesaShow = "{{ route('tipo-despesas.show', [':id']) }}";
        const urlDespesaStore = "{{ route('despesas.store') }}";

        // Instanciar Choices de forma global para acessar em outros scripts
        let choicesTipoDespesa = null;
        $(function() {
            if (!$('#tipo_despesa_id').data('choices-initialized')) {
                choicesTipoDespesa = new Choices('#tipo_despesa_id', {
                    searchEnabled: true,
                    itemSelectText: '',
                    shouldSort: false,
                    placeholderValue: 'Selecione o tipo de despesa',
                    allowHTML: false
                });
                $('#tipo_despesa_id').data('choices-initialized', true);
            } else {
                choicesTipoDespesa = $('#tipo_despesa_id')[0].choices;
            }
            $('#valor').mask('#.##0,00', {
                reverse: true
            });
        });
    </script>
@endpush
