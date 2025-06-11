<div class="modal fade" id="modalHonorarios" tabindex="-1" aria-labelledby="modalHonorariosLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded shadow border-0">
      <div class="modal-header border-0 px-0">
        <h5 class="modal-title swal2-title text-center w-100 m-0" id="modalHonorariosLabel">
          Honorários
        </h5>
        <button type="button" class="btn btn-light btn-sm border position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Fechar">
          <i class="fas fa-times text-muted"></i>
        </button>
      </div>

      <div class="modal-body">
        <!-- Formulário -->
        <form id="formCadastrarHonorario" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="valorHonorario" class="form-label fw-semibold">Valor do Honorário (R$)</label>
            <input type="text" class="form-control" id="valorHonorario" placeholder="Ex: 1.500,00">
          </div>

          <div class="mb-3">
            <label for="dataRecebimentoHonorario" class="form-label fw-semibold">Data de Recebimento</label>
            <input type="date" class="form-control" id="dataRecebimentoHonorario">
          </div>

          <div class="mb-3">
            <label for="arquivoComprovante" class="form-label fw-semibold">Comprovante (opcional)</label>
            <input type="file" class="form-control" id="arquivoComprovante" accept=".pdf,.jpg,.jpeg,.png">
            <small class="text-muted">Formatos aceitos: PDF, JPG, PNG</small>
          </div>

          <div class="mb-3">
            <label for="observacoesHonorario" class="form-label fw-semibold">Observações (opcional)</label>
            <textarea class="form-control" id="observacoesHonorario" rows="3"></textarea>
          </div>

          <div class="text-end">
            <button type="button" class="btn btn-secondary border me-2" data-bs-dismiss="modal">
              <i class="fas fa-times"></i> Cancelar
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-check"></i> Salvar
            </button>
          </div>
        </form>

        <!-- Divider -->
        <hr class="my-4">

        <!-- DataTable de honorários -->
        <div class="table-responsive">
          <table id="tabelaHonorarios" class="table table-bordered table-striped w-100 align-middle">
            <thead class="table-light">
              <tr>
                <th>Valor</th>
                <th>Comprovante</th>
                <th>Observações</th>
                <th>Data de Recebimento</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <!-- Preenchido via JS -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
