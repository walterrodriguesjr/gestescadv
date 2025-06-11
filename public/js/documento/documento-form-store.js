$(function () {
    $(document).on('click', '#btnSalvarDocumento', function () {
        const titulo = $('#tituloDocumento').val();
        const tipo_id = $('#filtroTipoDocumento').val();
        const conteudo = tinymce.get('editorDocumento')?.getContent() ?? '';

        const camposObrigatorios = [];
        if (!titulo) camposObrigatorios.push('Título do Documento');
        if (!conteudo) camposObrigatorios.push('Conteúdo do Documento');

        if (camposObrigatorios.length > 0) {
            const lista = camposObrigatorios.map(campo => `<li>${campo}</li>`).join('');
            Swal.fire({
                icon: 'warning',
                title: 'Campos obrigatórios!',
                html: `Preencha os seguintes campos antes de salvar:<ul class="text-left mt-2">${lista}</ul>`,
                confirmButtonColor: '#6c63ff'
            });
            return;
        }

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
                setTimeout(() => { podeFechar = true }, tempoMinimo);
                setTimeout(() => { if (Swal.isVisible()) Swal.close(); }, tempoMaximo);
            }
        });

        const request = $.ajax({
            url: urlDocumentoStore,
            method: 'POST',
            data: {
                escritorio_id: escritorioId,
                tipo_documento_id: tipo_id,
                titulo: titulo,
                conteudo: conteudo
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        request.done(() => {
            const tempoDecorrido = Date.now() - inicio;
            const atraso = tempoMinimo - tempoDecorrido;

            const finalizar = () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: 'Documento salvo com sucesso.',
                    confirmButtonColor: '#6c63ff'
                });

                $('#tituloDocumento').val('');
                $('#filtroTipoDocumento').val('').trigger('change');
                if (window.choicesTipoDocumento) {
                    window.choicesTipoDocumento.destroy();
                    window.choicesTipoDocumento = new Choices('#filtroTipoDocumento', {
                        searchEnabled: true,
                        itemSelectText: '',
                        shouldSort: false,
                        placeholderValue: 'Selecione o tipo de documento',
                    });
                }

                $('#areaEditorDocumento').slideUp();
                tinymce.remove('#editorDocumento');
            };

            if (podeFechar) {
                Swal.close();
                finalizar();
            } else {
                setTimeout(() => {
                    Swal.close();
                    finalizar();
                }, atraso > 0 ? atraso : 0);
            }
        });

        request.fail((xhr) => {
            const mostrarErro = () => {
                const msg = xhr.responseJSON?.message || 'Erro ao salvar documento.';
                Swal.fire('Erro', msg, 'error');
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
        });
    });
});
