@extends('layouts.main')

@section('title', 'Documentos')

@section('content')
<style>
    .choices__list--dropdown {
    z-index: 9999 !important;
}
</style>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Documentos</h4>
        </div>

        {{-- Linha de filtro e botão --}}
        <div class="row mb-3">
    <div class="col-md-12 d-flex align-items-end justify-content-between flex-wrap gap-2">
        <div class="flex-grow-1">
            <label for="filtroTipoDocumento" class="form-label">Tipo de Documento</label>
            <select id="filtroTipoDocumento" class="form-select w-100" required>

                {{-- options via ajax --}}
            </select>
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-end">
            <button class="btn btn-primary" id="btnRedigirDocumento" disabled>
                <i class="fas fa-file-alt"></i> Redigir Documento
            </button>
            <button class="btn btn-outline-primary" id="btnCriarTipoDocumento">
                <i class="fas fa-plus"></i> Novo Tipo de Documento
            </button>
            <button class="btn btn-primary d-flex align-items-center" id="btnAssistenteIa">
    <i class="bi bi-stars me-2"></i> Assistente de IA
</button>

        </div>
    </div>
</div>


        {{-- Editor só aparece quando clicar em "Redigir Documento" --}}
        <div class="row mb-3" id="areaEditorDocumento" style="display: none;">
            <div class="col-12">
                <form id="formNovoDocumento">
                    <div class="mb-2">
                        <input type="text" class="form-control" id="tituloDocumento" name="titulo"
                            placeholder="Título do Documento" required>
                    </div>
                    <textarea id="editorDocumento" name="conteudo" rows="15"></textarea>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-secondary" id="btnCancelarEditor">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-warning" id="btnLimparEditor">
                            <i class="fas fa-eraser"></i> Limpar
                        </button>
                        <button type="button" class="btn btn-primary" id="btnSalvarDocumento">
                            <i class="fas fa-save"></i> Salvar Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabela de Documentos --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped w-100 align-middle" id="tabelaDocumentos">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Linhas via DataTables --}}
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
    const escritorioId = '{{ $escritorioId }}';
    const userName = {!! json_encode($nome) !!};
    const userCpf = {!! json_encode($cpf) !!};
    const userOab = {!! json_encode($oab) !!};
    const userCidade = {!! json_encode($cidade) !!};
    const userEstado = {!! json_encode($estado) !!};

    const urlTipoDocumentoListar = "{{ route('tipos-documento.listar', [':id']) }}";
    const urlDocumentoStore = "{{ route('documentos.store') }}";
    const urlAssistenteIa = "{{ route('documentos.assistente-ia') }}";
</script>

    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('js/documento/documento-form-index.js') }}"></script>
    <script src="{{ asset('js/documento/documento-form-store.js') }}"></script>
@endpush
