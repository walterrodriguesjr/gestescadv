@extends('layouts.app')

{{-- Inclui o modal de cadastrar cliente--}}
@include('cliente.components.cliente-modal-cadastrar')

{{-- Inclui o modal de cadastrar cliente--}}
@include('cliente.components.cliente-modal-visualizar')

{{-- Inclui o modal de editar cliente--}}
@include('cliente.components.cliente-modal-editar')

{{-- Carrega o script de cadastrar-cliente --}}
@vite(['resources/js/cliente/cadastrar-cliente.js'])

{{-- Carrega o script de cadastrar-cliente --}}
@vite(['resources/js/cliente/editar-cliente.js'])

{{-- Carrega o script de listar-cliente --}}
@vite(['resources/js/cliente/listar-cliente.js'])

{{-- Carrega o script de limpar-cliente --}}
@vite(['resources/js/cliente/limpar-cliente.js'])

{{-- Carrega o script de pesquisar-cliente --}}
@vite(['resources/js/cliente/pesquisar-cliente.js'])

{{-- Carrega o script de visualizar-cliente --}}
@vite(['resources/js/cliente/visualizar-cliente.js'])

@section('content')
<div class="row justify-content-center align-items-center mb-3">
    <div class="col-md-6">
        <div class="input-group">
            <input type="text" class="form-control" id="pesquisarCliente" placeholder="Pesquise por nome ou cpf">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
        </div>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary" id="limparCliente">
            <i class="fas fa-eraser"></i> Limpar Pesquisa
        </button>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary" id="abrirModalCadastrarCliente">
            <i class="fas fa-floppy-disk"></i> Cadastrar Novo Cliente
        </button>
    </div>
</div>

<div class="table-responsive">
    <table id="tabelaClientes" class="table table-hover table-striped table-bordered">
        <thead class="table-white">
            <tr>
                <th>Nome Completo</th>
                <th>CPF</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dados serão preenchidos dinamicamente -->
        </tbody>
    </table>
</div>

@endsection

