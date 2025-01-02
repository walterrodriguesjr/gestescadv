<div class="modal fade" id="clienteModalEditar" tabindex="-1" aria-labelledby="clienteModalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header btn-success">
                <h1 class="modal-title fs-5" id="clienteModalEditarLabel"><i class="fas fa-user-edit"></i> Editar Dados do Cliente</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarCliente">
                    <input type="hidden" id="clienteIdEditar" name="cliente_id_editar">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="clienteNomeCompletoEditar" class="col-form-label fw-normal">Nome Completo</label>
                            <input type="text" class="form-control" id="clienteNomeCompletoEditar" name="cliente_nome_completo_editar">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCpfEditar" class="col-form-label fw-normal">CPF</label>
                            <input type="text" class="form-control" id="clienteCpfEditar" name="cliente_cpf_editar" disabled>
                        </div>
                        <div class="col-md-6 col-lg-6 mb-3">
                            <label for="clienteEmailEditar" class="col-form-label fw-normal">E-mail</label>
                            <input type="email" class="form-control" id="clienteEmailEditar" name="cliente_email_editar">
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCelularEditar" class="col-form-label fw-normal">Celular</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="clienteCelularEditar" name="cliente_celular_editar">
                                <a id="whatsappLinkEditar" href="#" target="_blank" class="input-group-text text-success d-none">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteTelefoneEditar" class="col-form-label fw-normal">Telefone</label>
                            <input type="text" class="form-control" id="clienteTelefoneEditar" name="cliente_telefone_editar">
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCepEditar" class="col-form-label fw-normal">CEP</label>
                            <input type="text" class="form-control" id="clienteCepEditar" name="cliente_cep_editar">
                        </div>
                        <div class="col-md-6 col-lg-6 mb-3">
                            <label for="clienteRuaEditar" class="col-form-label fw-normal">Rua</label>
                            <input type="text" class="form-control" id="clienteRuaEditar" name="cliente_rua_editar">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteNumeroEditar" class="col-form-label fw-normal">Número</label>
                            <input type="text" class="form-control" id="clienteNumeroEditar" name="cliente_numero_editar">
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteBairroEditar" class="col-form-label fw-normal">Bairro</label>
                            <input type="text" class="form-control" id="clienteBairroEditar" name="cliente_bairro_editar">
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteEstadoEditar" class="col-form-label fw-normal">Estado</label>
                            <select class="form-select" id="clienteEstadoEditar" name="cliente_estado_editar">
                                <option value="" disabled selected>Selecione</option>
                                <!-- Opções de estados serão preenchidas dinamicamente -->
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCidadeEditar" class="col-form-label fw-normal">Cidade</label>
                            <select class="form-select" id="clienteCidadeEditar" name="cliente_cidade_editar">
                                <option value="" disabled selected>Selecione</option>
                                <!-- Opções de cidades serão preenchidas dinamicamente -->
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                <button type="button" class="btn btn-success" id="salvarClienteEditar"><i class="fas fa-sync"></i> Atualizar</button>
            </div>
        </div>
    </div>
</div>
