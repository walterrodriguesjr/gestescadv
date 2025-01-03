<style>
    /* Overlay para desabilitar o restante da interface */
.overlay-spinner {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050; /* Certifique-se de que esteja acima de tudo */
}

/* Container do spinner centralizado */
.spinner-container {
    text-align: center;
}

</style>


<div id="deletarSpinner" class="overlay-spinner d-none overlaySpinner">
    <div class="spinner-container">
        <button class="btn btn-danger" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Deletando...
        </button>
    </div>
</div>
