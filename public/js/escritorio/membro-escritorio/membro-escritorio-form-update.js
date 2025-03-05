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
                <input type="text" id="nome" class="swal2-input" value="${nome}" required>
                
                <label for="email">E-mail:</label>
                <input type="email" id="email" class="swal2-input" value="${email}" required>
                
                <label for="nivelAcesso">Nível de Acesso:</label>
                <select id="nivelAcesso" class="swal2-select">
                    <option value="Funcionário" ${nivelAcesso === 'Funcionário' ? 'selected' : ''}>Funcionário</option>
                    <option value="Estagiário" ${nivelAcesso === 'Estagiário' ? 'selected' : ''}>Estagiário</option>
                </select>

                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" class="swal2-input" value="${cpf}" required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: "Salvar",
        cancelButtonText: "Cancelar",
        didOpen: () => {
            // Aplica máscara ao CPF quando o modal é aberto
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
            // Se o usuário clicou em "Salvar" e passou na validação, faz update AJAX
            atualizarMembro(membroId, result.value);
        }
    });
}

/**
 * Faz a requisição AJAX para atualizar o membro no servidor
 */
function atualizarMembro(membroId, dados) {
    Swal.fire({
        title: "Atualizando...",
        text: "Aguarde enquanto os dados são atualizados.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: `/membros/${membroId}/update`, // Ajuste a rota conforme necessário
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
            Swal.fire({
                icon: "success",
                title: "Sucesso!",
                text: response.message || "Membro atualizado com sucesso."
            });

            // Recarrega a tabela DataTable
            $('#membrosEscritorioTable').DataTable().ajax.reload();
        },
        error: function (xhr) {
            Swal.fire({
                icon: "error",
                title: "Erro!",
                text: "Não foi possível atualizar o membro. Tente novamente."
            });
        }
    });
}
