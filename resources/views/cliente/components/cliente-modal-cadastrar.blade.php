
<div class="modal fade" id="clienteModalCadastrar" tabindex="-1" aria-labelledby="clienteModalCadastrarLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <h1 class="modal-title fs-5" id="clienteModalCadastrarLabel"><i class="fas fa-user"></i> Cadastrar Cliente
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="clienteNomeCompleto" class="col-form-label fw-normal">Nome Completo</label>
                            <input type="text" class="form-control" id="clienteNomeCompleto"
                                name="cliente_nome_completo" placeholder="Digite">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCpf" class="col-form-label fw-normal">CPF</label>
                            <input type="text" class="form-control" id="clienteCpf" name="cliente_cpf" placeholder="xxx.xxx.xxx-xx">
                        </div>
                        <div class="col-md-6 col-lg-6 mb-3">
                            <label for="clienteEmail" class="col-form-label fw-normal">E-mail</label>
                            <input type="email" class="form-control" id="clienteEmail" name="cliente_email" placeholder="emailDoCliente@gmail.com">
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCelular" class="col-form-label fw-normal">Celular</label>
                            <input type="text" class="form-control" id="clienteCelular" name="cliente_celular" placeholder="(xx)xxxxx-xxxx">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteTelefone" class="col-form-label fw-normal">Telefone</label>
                            <input type="text" class="form-control" id="clienteTelefone" name="cliente_telefone" placeholder="(xx)xxxx-xxxx">
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCep" class="col-form-label fw-normal">CEP</label>
                            <input type="text" class="form-control" id="clienteCep" name="cliente_cep" placeholder="xxxxx-xxx">
                        </div>
                        <div class="col-md-6 col-lg-6 mb-3">
                            <label for="clienteRua" class="col-form-label fw-normal">Rua</label>
                            <input type="text" class="form-control" id="clienteRua" name="cliente_rua" placeholder="Digite">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteNumero" class="col-form-label fw-normal">Número</label>
                            <input type="text" class="form-control" id="clienteNumero" name="cliente_numero" placeholder="Digite">
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteBairro" class="col-form-label fw-normal">Bairro</label>
                            <input type="text" class="form-control" id="clienteBairro" name="cliente_bairro" placeholder="Digite">
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteEstado" class="col-form-label fw-normal">Estado</label>
                            <select class="form-select" id="clienteEstado" name="cliente_estado">
                                <option value="" disabled selected>Selecione</option>
                                <!-- Opções de estados serão adicionadas dinamicamente -->
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCidade" class="col-form-label fw-normal">Cidade</label>
                            <select class="form-select" id="clienteCidade" name="cliente_cidade">
                                <option value="" disabled selected>Selecione</option>
                                <!-- Opções de cidades serão adicionadas dinamicamente -->
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i>
                    Fechar</button>
                <button type="button" class="btn btn-primary" id="buttonSalvarDadosNovoCliente"><i
                        class="fas fa-check"></i> Salvar</button>
            </div>
        </div>
    </div>
</div>
