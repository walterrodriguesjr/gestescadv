@extends('layouts.app')
{{-- Inclui o modal de cadastrar cliente--}}
@include('cliente.components.cliente-modal-cadastrar')

{{-- Carrega o script de cadastrar-cliente --}}
@vite(['resources/js/cliente/cadastrar-cliente.js'])

@section('content')
<div>
    <button class="btn btn-primary" id="abrirModalCadastrarCliente">Cadastrar Cliente</button>
</div>  
@endsection

