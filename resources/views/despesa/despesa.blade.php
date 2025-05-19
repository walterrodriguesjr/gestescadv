<!-- resources/views/despesa/despesa.blade.php -->
@extends('layouts.app')

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
            <form id="formCadastrarDespesa" enctype="multipart/form-data" class="mb-4">
                @csrf
                <input type="hidden" name="escritorio_id" value="{{ auth()->user()->escritorio_id }}">

                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label for="tipo_despesa_id" class="form-label">Tipo de Despesa</label>
                        <select name="tipo_despesa_id" id="tipo_despesa_id" class="form-select" required>
                            <option value="">Selecione</option>
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
                        <label for="data" class="form-label">Data</label>
                        <input type="text" name="data" id="data" class="form-control" placeholder="dd/mm/aaaa" maxlength="10" required>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table id="tabelaDespesas" class="table table-bordered table-striped w-100 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Data</th>
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
