<div class="modal fade" id="clienteModalVisualizar" tabindex="-1" aria-labelledby="clienteModalVisualizarLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header btn-info">
                <h1 class="modal-title fs-5" id="clienteModalVisualizarLabel"><i class="fas fa-user"></i> Visualizar Dados do Cliente
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNovoCliente">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="clienteNomeCompletoVisualizar" class="col-form-label fw-normal">Nome Completo</label>
                            <input type="text" class="form-control" id="clienteNomeCompletoVisualizar" name="cliente_nome_completo_visualizar" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCpfVisualizar" class="col-form-label fw-normal">CPF</label>
                            <input type="text" class="form-control" id="clienteCpfVisualizar" name="cliente_cpf_visualizar" disabled>
                        </div>
                        <div class="col-md-6 col-lg-6 mb-3">
                            <label for="clienteEmailVisualizar" class="col-form-label fw-normal">E-mail</label>
                            <input type="email" class="form-control" id="clienteEmailVisualizar" name="cliente_email_visualizar" disabled>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCelularVisualizar" class="col-form-label fw-normal">Celular</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="clienteCelularVisualizar" name="cliente_celular_visualizar" disabled>
                                <a id="whatsappLink" href="#" target="_blank" class="input-group-text text-success d-none">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteTelefoneVisualizar" class="col-form-label fw-normal">Telefone</label>
                            <input type="text" class="form-control" id="clienteTelefoneVisualizar" name="cliente_telefone_visualizar" disabled>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCepVisualizar" class="col-form-label fw-normal">CEP</label>
                            <input type="text" class="form-control" id="clienteCepVisualizar" name="cliente_cep_visualizar" disabled>
                        </div>
                        <div class="col-md-6 col-lg-6 mb-3">
                            <label for="clienteRuaVisualizar" class="col-form-label fw-normal">Rua</label>
                            <input type="text" class="form-control" id="clienteRuaVisualizar" name="cliente_rua_visualizar" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteNumeroVisualizar" class="col-form-label fw-normal">Número</label>
                            <input type="text" class="form-control" id="clienteNumeroVisualizar" name="cliente_numero_visualizar" disabled>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteBairroVisualizar" class="col-form-label fw-normal">Bairro</label>
                            <input type="text" class="form-control" id="clienteBairroVisualizar" name="cliente_bairro_visualizar" disabled>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteEstadoVisualizar" class="col-form-label fw-normal">Estado</label>
                            <input type="text" class="form-control" id="clienteEstadoVisualizar" name="cliente_estado_visualizar" placeholder="Digite o estado" disabled>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="clienteCidadeVisualizar" class="col-form-label fw-normal">Cidade</label>
                            <input type="text" class="form-control" id="clienteCidadeVisualizar" name="cliente_cidade_visualizar" placeholder="Digite a cidade" disabled>
                        </div>
                        
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a id="googleMapsLink" href="#" target="_blank" class="text-primary me-auto d-none">
                    <i class="fas fa-map-marker-alt"></i> Clique para ver este endereço no mapa
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
            </div>
            
        </div>
    </div>
</div>
