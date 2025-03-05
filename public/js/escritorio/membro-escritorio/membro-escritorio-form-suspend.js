function suspenderMembro(membroId) {
    Swal.fire({
        title: "Tem certeza?",
        text: "O membro será suspenso e não poderá mais acessar o escritório!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, suspender",
        cancelButtonText: "Cancelar",
        reverseButtons: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d"
    }).then((result) => {
        if (result.isConfirmed) {
            let requestStartTime = new Date().getTime();
            let minWaitTime = 1500; // Tempo mínimo do spinner
            let maxWaitTime = 10000; // Tempo máximo antes de erro
            let timeoutReached = false;

            let timeout = setTimeout(() => {
                timeoutReached = true;
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Erro",
                    text: "A requisição demorou muito para responder. Tente novamente.",
                });
            }, maxWaitTime);

            Swal.fire({
                title: "Suspendendo...",
                text: "Aguarde enquanto o membro está sendo suspenso.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `/membro-escritorio/${membroId}/suspender`,
                type: "POST",
                headers: { "X-CSRF-TOKEN": csrfToken },
                success: function(response) {
                    clearTimeout(timeout); // Cancela o timeout se a resposta chegou
                    if (timeoutReached) return; // Se já chegou no timeout, não faz nada

                    let requestEndTime = new Date().getTime();
                    let elapsedTime = requestEndTime - requestStartTime;

                    setTimeout(() => {
                        Swal.close();
                        Swal.fire({
                            icon: "success",
                            title: "Sucesso!",
                            text: response.message,
                        });

                        // ✅ Atualiza a tabela automaticamente
                        $('#membrosEscritorioTable').DataTable().ajax.reload(null, false);
                    }, Math.max(minWaitTime - elapsedTime, 0)); // Garante tempo mínimo do spinner
                },
                error: function(xhr) {
                    clearTimeout(timeout);
                    if (timeoutReached) return;

                    Swal.close();
                    Swal.fire({
                        icon: "error",
                        title: "Erro!",
                        text: xhr.responseJSON?.message || "Não foi possível suspender o membro.",
                    });
                }
            });
        }
    });
}
