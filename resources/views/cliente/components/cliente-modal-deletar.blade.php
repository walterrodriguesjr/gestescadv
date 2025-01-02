<div class="modal fade" id="clienteModalDeletar" tabindex="-1" aria-labelledby="clienteModalDeletarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-danger text-white">
                <h1 class="modal-title fs-5" id="clienteModalDeletarLabel">
                    <i class="fas fa-exclamation-triangle"></i> Atenção!
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                <h4 class="mt-3 text-danger fw-bold">Tem certeza que deseja deletar este cliente?</h4>
                <p class="text-muted">Esta ação não pode ser desfeita e todos os dados associados a este cliente serão removidos permanentemente.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="salvarClienteDeletar">
                    <i class="fas fa-trash"></i> Confirmar Exclusão
                </button>
            </div>
        </div>
    </div>
</div>
