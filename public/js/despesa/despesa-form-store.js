// public/js/despesa/despesa-form-store.js

$(function () {
    // Ao clicar em salvar despesa
    $(document).on('click', '#btnSalvarDespesa', function (e) {
        e.preventDefault();

        // Array com os campos obrigatórios: [selector, texto para exibir no swal]
        const camposObrigatorios = [
            { campo: '#tipo_despesa_id', nome: 'Tipo de despesa' },
            { campo: '#valor', nome: 'Valor' },
            { campo: '#data_vencimento', nome: 'Data de vencimento' }
        ];

        let camposNaoPreenchidos = camposObrigatorios
            .filter(item => !$(item.campo).val())
            .map(item => `<li><i class="fas fa-exclamation-circle text-danger mr-1"></i>${item.nome}</li>`)
            .join('');

        // Validação especial para valor (opcional: regex valor brasileiro)
        let valorValido = true;
        let valor = $('#valor').val();
        if (valor) {
            valorValido = /^\d{1,3}(\.\d{3})*,\d{2}$|^\d+,\d{2}$/.test(valor);
            if (!valorValido) {
                camposNaoPreenchidos += `<li><i class="fas fa-exclamation-circle text-danger mr-1"></i>Valor em formato inválido!</li>`;
            }
        }

        if (camposNaoPreenchidos) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                html: `Preencha os seguintes campos obrigatórios antes de salvar:
                       <ul class="text-left mt-2">${camposNaoPreenchidos}</ul>`,
                confirmButtonColor: '#6c63ff'
            });
            return;
        }

        // Normaliza o valor para float (ex: 1.234,56 => 1234.56)
        if (valor) valor = valor.replace(/\./g, '').replace(',', '.');

        // Prepara os dados para enviar
        let dataToSend = {
            escritorio_id: escritorioId,
            tipo_despesa_id: $('#tipo_despesa_id').val(),
            valor: valor,
            data_vencimento: $('#data_vencimento').val()
        };

        // Loader padrão SweetAlert2
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

        // AJAX para salvar
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
                    if (window.tabelaDespesas) window.tabelaDespesas.ajax.reload();
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
