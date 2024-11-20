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
                    <div class="mb-3">
                        <div class="col-12">
                            <label for="recipient-name" class="col-form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="clienteNomeCompleto"
                                name="cliente_nome_completo">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 mb-3">
                            <label for="message-text" class="col-form-label">CPF</label>
                            <input type="text" class="form-control" id="clienteCpf" name="cliente_cpf">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="message-text" class="col-form-label">E-mail</label>
                            <input type="email" class="form-control" id="clienteEmail" name="cliente_email">
                        </div>
                        <div class="col-2 mb-3">
                            <label for="message-text" class="col-form-label">Celular</label>
                            <input type="text" class="form-control" id="clienteCelular" name="cliente_celular">
                        </div>
                        <div class="col-2 mb-3">
                            <label for="message-text" class="col-form-label">Telefone</label>
                            <input type="text" class="form-control" id="clienteTelefone" name="cliente_telefone">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 mb-3">
                            <label for="message-text" class="col-form-label">CEP</label>
                            <input type="text" class="form-control" id="clienteCep" name="cliente_cep">
                        </div>
                        <div class="col-8 mb-3">
                            <label for="message-text" class="col-form-label">Rua</label>
                            <input type="text" class="form-control" id="clienteRua" name="cliente_rua">
                        </div>
                        <div class="col-2 mb-3">
                            <label for="message-text" class="col-form-label">Número</label>
                            <input type="text" class="form-control" id="clienteNumero" name="cliente_numero">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label for="message-text" class="col-form-label">Bairro</label>
                            <input type="text" class="form-control" id="clienteBairro" name="cliente_Bairro">
                        </div>
                        <div class="col-4 mb-3">
                            <label for="clienteEstado" class="col-form-label">Estado</label>
                            <select class="form-select" id="clienteEstado" name="cliente_estado">
                                <option selected>Selecione</option>
                                <!-- Opções de estados -->
                            </select>
                        </div>
                        <div class="col-4 mb-3">
                            <label for="clienteCidade" class="col-form-label">Cidade</label>
                            <select class="form-select" id="clienteCidade" name="cliente_cidade">
                                <option selected>Selecione</option>
                                <!-- Opções de cidades -->
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i>
                    Fechar</button>
                <button type="button" class="btn btn-primary"><i class="fas fa-check"></i> Salvar</button>
            </div>
        </div>
    </div>
</div>
