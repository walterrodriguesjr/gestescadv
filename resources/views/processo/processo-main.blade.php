@extends('layouts.app')

@section('content')
<div class="row justify-content-center align-items-center mb-3">
    <div class="col-md-6">
        <div class="input-group">
            <input type="text" class="form-control" id="pesquisarProcesso" placeholder="Pesquise por número do processo, área, cliente ou CPF">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
        </div>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary" id="limparProcesso">
            <i class="fas fa-eraser"></i> Limpar Pesquisa
        </button>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary" id="abrirModalCadastrarProcesso">
            <i class="fas fa-floppy-disk"></i> Cadastrar Novo Processo
        </button>
    </div>
</div>

<div class="table-responsive">
    <table id="tabelaClientes" class="table table-hover table-striped table-bordered">
        <thead class="table-white">
            <tr>
                <th>Número do Processo</th>
                <th>Área</th>
                <th>Nome do Cliente</th>
                <th>CPF</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dados serão preenchidos dinamicamente -->
        </tbody>
    </table>
</div>

@endsection