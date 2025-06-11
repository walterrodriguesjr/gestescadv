$(document).ready(function () {
    async function baixarArquivo(formato, botao) {
        // Desativa o botão para evitar múltiplos cliques
        $(botao).prop("disabled", true);

        let loadingSwal = Swal.fire({
            title: `Gerando seu arquivo ${formato.toUpperCase()}...`,
            text: "Aguarde...",
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        let requestStartTime = new Date().getTime();

        try {
            const response = await fetch(`/perfil/exportar-dados?formato=${formato}`, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            let requestEndTime = new Date().getTime();
            let elapsedTime = requestEndTime - requestStartTime;
            let minWaitTime = 1500;

            if (!response.ok) {
                throw new Error(`Erro ao gerar ${formato.toUpperCase()}: ${response.statusText}`);
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = `meus-dados.${formato}`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    icon: "success",
                    title: "Download concluído!",
                    text: `Seu arquivo ${formato.toUpperCase()} foi gerado com sucesso.`,
                    confirmButtonText: "<i class='fas fa-check'></i> OK"
                });
                $(botao).prop("disabled", false); // Reativa o botão após a operação
            }, Math.max(minWaitTime - elapsedTime, 0));

        } catch (error) {
            console.error(`Erro ao baixar ${formato.toUpperCase()}:`, error);

            let requestEndTime = new Date().getTime();
            let elapsedTime = requestEndTime - requestStartTime;
            let minWaitTime = 1500;

            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Erro",
                    text: `Erro ao baixar ${formato.toUpperCase()}. Tente novamente.`,
                    confirmButtonText: "<i class='fas fa-times'></i> OK"
                });
                $(botao).prop("disabled", false); // Reativa o botão após erro
            }, Math.max(minWaitTime - elapsedTime, 0));
        }
    }

    // Evento para baixar JSON
    $("#baixarDadosJSON").click(function () {
        baixarArquivo("json", this);
    });

    // Evento para baixar CSV
    $("#baixarDadosCSV").click(function () {
        baixarArquivo("csv", this);
    });
});
