/**
 * Essa função é chamada ao clicar no botão "Editar" definido no script de SHOW.
 * Ela abre um Swal com os campos Nome, CPF, Email, e Nível de Acesso.
 */
function abrirEdicaoMembro(membroId, nome, email, nivelAcesso, cpf) {
    Swal.fire({
        title: "Editar Membro",
        html: `
            <form id="formEditarMembro">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" class="swal2-input" value="${nome}" placeholder="Digite o nome completo" required>

                <label for="email">E-mail:</label>
                <input type="email" id="email" class="swal2-input" value="${email}" placeholder="Digite o e-mail" required>

                <label for="nivelAcesso">Nível de Acesso:</label>
                <select id="nivelAcesso" class="swal2-select">
                    <option value="Funcionário" ${nivelAcesso === 'Funcionário' ? 'selected' : ''}>Funcionário</option>
                    <option value="Estagiário" ${nivelAcesso === 'Estagiário' ? 'selected' : ''}>Estagiário</option>
                </select>

                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" class="swal2-input" value="${cpf}" placeholder="Digite o CPF" required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: "<i class='fas fa-check'></i> Atualizar",
        cancelButtonText: "<i class='fas fa-times'></i> Cancelar",
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-success ms-2",
            cancelButton: "btn btn-secondary me-2"
        },
        reverseButtons: true,
        didOpen: () => {
            $("#cpf").mask("000.000.000-00");
        },
        preConfirm: () => {
            const nomeEditado = $("#nome").val().trim();
            const emailEditado = $("#email").val().trim();
            const nivelAcessoEditado = $("#nivelAcesso").val();
            const cpfEditado = $("#cpf").val().trim();

            if (!nomeEditado || !emailEditado || !nivelAcessoEditado || !cpfEditado) {
                Swal.showValidationMessage("Todos os campos são obrigatórios.");
                return false;
            }

            return {
                nome: nomeEditado,
                email: emailEditado,
                nivelAcesso: nivelAcessoEditado,
                cpf: cpfEditado
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            atualizarMembro(membroId, result.value);
        }
    });
}


/**
 * Faz a requisição AJAX para atualizar o membro no servidor
 */
function atualizarMembro(membroId, dados) {
    let podeFechar = false;
    const tempoMinimo = 1500; // 1.5 segundos
    const tempoMaximo = 10000; // 10 segundos

    Swal.fire({
        title: "Atualizando...",
        text: "Aguarde enquanto os dados são atualizados.",
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
            setTimeout(() => { podeFechar = true; }, tempoMinimo);
            setTimeout(() => {
                if (Swal.isVisible()) Swal.close();
            }, tempoMaximo);
        }
    });

    $.ajax({
        url: `/membros/${membroId}/update`,
        type: "POST",
        headers: { "X-CSRF-TOKEN": csrfToken },
        data: {
            _method: "PUT",
            nome: dados.nome,
            email: dados.email,
            nivelAcesso: dados.nivelAcesso,
            cpf: dados.cpf
        },
        success: function (response) {
            const mostrarSwal = () => {
                Swal.fire({
                    icon: "success",
                    title: "Sucesso!",
                    text: response.message || "Membro atualizado com sucesso."
                });
                $('#membrosEscritorioTable').DataTable().ajax.reload();
            };

            if (podeFechar) {
                Swal.close();
                mostrarSwal();
            } else {
                setTimeout(() => {
                    Swal.close();
                    mostrarSwal();
                }, tempoMinimo);
            }
        },
        error: function () {
            const mostrarErro = () => {
                Swal.fire({
                    icon: "error",
                    title: "Erro!",
                    text: "Não foi possível atualizar o membro. Tente novamente."
                });
            };

            if (podeFechar) {
                Swal.close();
                mostrarErro();
            } else {
                setTimeout(() => {
                    Swal.close();
                    mostrarErro();
                }, tempoMinimo);
            }
        }
    });
}

