// public/js/despesa/despesa-form-store.js

$(function () {
    // Adiciona método "pattern" caso não exista (compatibilidade)
    if (!$.validator.methods.pattern) {
        $.validator.addMethod("pattern", function (value, element, param) {
            if (this.optional(element)) return true;
            if (typeof param === "string") param = new RegExp(param);
            return param.test(value);
        }, "Formato inválido.");
    }

    // Inicializa a validação do formulário usando jQuery Validate
    $('#formCadastrarDespesa').validate({
        rules: {
            tipo_despesa_id: { required: true },
            valor: {
                required: true,
                pattern: /^\d{1,3}(\.\d{3})*,\d{2}$|^\d+,\d{2}$/
            },
            data_vencimento: { required: true }
        },
        messages: {
            tipo_despesa_id: {
                required: "<strong class='text-danger'><i class='fas fa-exclamation-circle'></i> Escolha um tipo de despesa!</strong>"
            },
            valor: {
                required: "<strong class='text-danger'><i class='fas fa-exclamation-circle'></i> Informe o valor!</strong>",
                pattern: "<strong class='text-danger'><i class='fas fa-exclamation-circle'></i> Informe um valor válido!</strong>"
            },
            data_vencimento: {
                required: "<strong class='text-danger'><i class='fas fa-exclamation-circle'></i> Informe a data de vencimento!</strong>"
            }
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        },
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        }
    });

    // Evento click no botão de salvar
    $(document).on('click', '#btnSalvarDespesa', function (e) {
        e.preventDefault();

        if (!$('#formCadastrarDespesa').valid()) {
            Swal.fire('Erro', 'Preencha todos os campos obrigatórios corretamente.', 'error');
            return;
        }

        // Pega os dados do form
        let tipoDespesaId = $('#tipo_despesa_id').val();
        let valor = $('#valor').val();
        let dataVencimento = $('#data_vencimento').val();

        // Normaliza valor para float (ex: 1.234,56 -> 1234.56)
        if (valor) valor = valor.replace(/\./g, '').replace(',', '.');

        let dataToSend = {
            escritorio_id: escritorioId,
            tipo_despesa_id: tipoDespesaId,
            valor: valor,
            data_vencimento: dataVencimento
        };

        // --- Loader padrão: mínimo 1.5s, máximo 10s ---
        let podeFechar = false;
        const tempoMinimo = 1500;
        const tempoMaximo = 10000;
        const inicio = Date.now();

        Swal.fire({
            title: 'Salvando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                setTimeout(() => { podeFechar = true; }, tempoMinimo);
                setTimeout(() => { if (Swal.isVisible()) Swal.close(); }, tempoMaximo);
            }
        });

        // Ajax para salvar
        $.ajax({
            url: urlDespesaStore,
            method: 'POST',
            data: dataToSend,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (resp) {
                const tempoDecorrido = Date.now() - inicio;
                const atraso = tempoMinimo - tempoDecorrido;

                const mostrarSwalSucesso = () => {
                    Swal.fire('Sucesso!', 'Despesa cadastrada com sucesso.', 'success');
                    $('#formCadastrarDespesa')[0].reset();
                    $('#tipo_despesa_id').val('').trigger('change');
                };

                if (podeFechar) {
                    Swal.close();
                    mostrarSwalSucesso();
                } else {
                    setTimeout(() => {
                        Swal.close();
                        mostrarSwalSucesso();
                    }, atraso > 0 ? atraso : 0);
                }
            },
            error: function (xhr) {
                const mostrarSwalErro = () => {
                    let msg = xhr.responseJSON?.message || 'Erro ao cadastrar despesa.';
                    Swal.fire('Erro', msg, 'error');
                };

                if (podeFechar) {
                    Swal.close();
                    mostrarSwalErro();
                } else {
                    setTimeout(() => {
                        Swal.close();
                        mostrarSwalErro();
                    }, tempoMinimo);
                }
            }
        });
    });
});
