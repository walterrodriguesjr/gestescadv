$(document).ready(function () {
    function carregarSessoesAtivas() {
        $.ajax({
            url: "/sessoes-ativas",
            type: "GET",
            success: function (sessoes) {
                const $tbody = $("#listaSessoes");
                $tbody.empty();

                if (sessoes.length === 0) {
                    $tbody.append('<tr><td colspan="4" class="text-center">Nenhuma sessão ativa encontrada.</td></tr>');
                    return;
                }

                sessoes.forEach((sessao) => {
                    const linha = `
                        <tr>
                            <td>${sessao.ip_address}</td>
                            <td>${sessao.user_agent}</td>
                            <td>${sessao.ultima_atividade}</td>
                            <td>
                                <button class="btn btn-danger btn-sm encerrarSessao" data-id="${sessao.id}">
                                    <i class="fas fa-times"></i> Encerrar
                                </button>
                            </td>
                        </tr>
                    `;
                    $tbody.append(linha);
                });
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Erro",
                    text: "Erro ao carregar sessões ativas.",
                    confirmButtonText: "<i class='fas fa-check'></i> OK"
                });
            },
        });
    }

    // Encerrar sessão específica com confirmação
    $(document).on("click", ".encerrarSessao", function () {
        const sessaoId = $(this).data("id");

        Swal.fire({
            title: "Tem certeza?",
            text: "Essa ação encerrará a sessão selecionada!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "<i class='fas fa-check'></i> Sim",
            cancelButtonText: "<i class='fas fa-times'></i> Cancelar",
            reverseButtons: true,
            customClass: {
                actions: 'd-flex justify-content-center gap-3 mt-3', // centraliza e espaça os botões
                confirmButton: 'btn btn-primary px-4',
                cancelButton: 'btn btn-secondary px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Encerrando sessão...",
                    text: "Aguarde...",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const requestStartTime = Date.now();

                $.ajax({
                    url: `/sessoes-ativas/logout/${sessaoId}`,
                    type: "POST",
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    success: function (response) {
                        const elapsedTime = Date.now() - requestStartTime;
                        const minWaitTime = 1000;

                        setTimeout(() => {
                            Swal.close();

                            Swal.fire({
                                icon: "success",
                                title: "Sessão encerrada!",
                                text: response.message,
                                confirmButtonText: "<i class='fas fa-check'></i> OK",
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                },
                                buttonsStyling: false
                            });

                            if (response.logout) {
                                setTimeout(() => {
                                    window.location.href = "/logout";
                                }, 2000);
                            } else {
                                carregarSessoesAtivas();
                            }
                        }, Math.max(minWaitTime - elapsedTime, 0));
                    },
                    error: function () {
                        const elapsedTime = Date.now() - requestStartTime;

                        setTimeout(() => {
                            Swal.close();

                            Swal.fire({
                                icon: "error",
                                title: "Erro",
                                text: "Erro ao encerrar sessão.",
                                confirmButtonText: "<i class='fas fa-check'></i> OK",
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                },
                                buttonsStyling: false
                            });
                        }, Math.max(1000 - elapsedTime, 0));
                    }
                });
            }
        });
    });


    // Encerrar todas as sessões com confirmação
    $("#encerrarTodasSessoes").click(function () {
        Swal.fire({
            title: "Tem certeza?",
            text: "Todas as sessões ativas serão encerradas!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "<i class='fas fa-check'></i> Sim",
            cancelButtonText: "<i class='fas fa-times'></i> Cancelar",
            reverseButtons: true,
            customClass: {
                actions: 'd-flex justify-content-center gap-3 mt-3',
                confirmButton: 'btn btn-primary px-4',
                cancelButton: 'btn btn-secondary px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                let loadingSwal = Swal.fire({
                    title: "Encerrando todas as sessões...",
                    text: "Aguarde...",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                let requestStartTime = new Date().getTime();

                $.ajax({
                    url: "/sessoes-ativas/logout-all",
                    type: "POST",
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    success: function (response) {
                        let requestEndTime = new Date().getTime();
                        let elapsedTime = requestEndTime - requestStartTime;
                        let minWaitTime = 1000;

                        setTimeout(() => {
                            Swal.close();

                            Swal.fire({
                                icon: "success",
                                title: "Sessões encerradas!",
                                text: response.message,
                                confirmButtonText: "<i class='fas fa-check'></i> OK"
                            });

                            if (response.logout) {
                                setTimeout(() => {
                                    window.location.href = "/login";
                                }, 2000);
                            } else {
                                carregarSessoesAtivas();
                            }
                        }, Math.max(minWaitTime - elapsedTime, 0));
                    },
                    error: function () {
                        let requestEndTime = new Date().getTime();
                        let elapsedTime = requestEndTime - requestStartTime;

                        setTimeout(() => {
                            Swal.close();

                            Swal.fire({
                                icon: "error",
                                title: "Erro",
                                text: "Erro ao encerrar todas as sessões.",
                                confirmButtonText: "<i class='fas fa-check'></i> OK"
                            });
                        }, Math.max(minWaitTime - elapsedTime, 0));
                    }
                });
            }
        });
    });

    // Carregar sessões ativas ao iniciar a página
    carregarSessoesAtivas();
});
