$(document).ready(function () {

    // Converte primeira letra para maiúscula e o resto para minúscula
    $("#nomeServico").on("input", function () {
        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();
    });

    // Adiciona método 'pattern' se ainda não existir
    if (!$.validator.methods.pattern) {
        $.validator.addMethod("pattern", function (value, element, pattern) {
            if (typeof pattern === "string") {
                pattern = new RegExp(pattern);
            }
            return this.optional(element) || pattern.test(value);
        }, "Formato inválido.");
    }

    // Validação
    $("#formNovoServico").validate({
        rules: {
            nome_servico: {
                required: true,
                minlength: 3,
                pattern: /^[A-Za-zÀ-ú0-9\s]+$/
            }
        },
        messages: {
            nome_servico: {
                required: "O nome do serviço é obrigatório.",
                minlength: "Mínimo de 3 caracteres.",
                pattern: "Use apenas letras, números e espaços."
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger small',
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        }
    });

    $("#salvarTipoServico").on("click", function (e) {
        e.preventDefault();

        if (!$("#formNovoServico").valid()) return;

        const nomeServico = $("#nomeServico").val();

        Swal.fire({
            title: "Salvando...",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: `/tipo_servicos/${escritorioId}`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { nome_servico: nomeServico },
            success: function (resp) {
                Swal.fire("Sucesso!", resp.message, "success");
                $("#formNovoServico")[0].reset();
                $('#tabelaTipoServicos').DataTable().ajax.reload();
            },
            error: function (xhr) {
                let msg = "Erro ao cadastrar.";
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).join("<br>");
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire("Erro!", msg, "error");
            }
        });
    });
});
