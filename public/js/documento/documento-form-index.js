$(function () {
    let choicesTipoDocumento = null;
    let tipoSelecionado = '';

    function initChoices() {
    if (choicesTipoDocumento) choicesTipoDocumento.destroy();
    choicesTipoDocumento = new Choices('#filtroTipoDocumento', {
        searchEnabled: true,
        itemSelectText: '',
        shouldSort: false,
        placeholderValue: 'Selecione o tipo de documento',
    });
    window.choicesTipoDocumento = choicesTipoDocumento; // <-- AQUI
}


    function carregarTiposDocumento() {
        let urlListar = urlTipoDocumentoListar.replace(':id', escritorioId);
        $.get(urlListar, function (tipos) {
            let options = '';
            tipos.forEach(function (tipo) {
                options += `<option value="${tipo.id}" data-titulo="${tipo.titulo}">${tipo.titulo}</option>`;
            });
            $('#filtroTipoDocumento').html(options);
            initChoices();
        });
    }

    carregarTiposDocumento();

    $(document).on('change', '#filtroTipoDocumento', function () {
        let tipoId = $(this).val();
        tipoSelecionado = $(this).find('option:selected').data('titulo') || '';
        $('#btnRedigirDocumento').prop('disabled', !tipoId);
        $('#btnAssistenteIa').prop('disabled', !tipoId);
        const editor = tinymce.get('editorDocumento');
        if (editor) {
            editor.setContent('');
            tinymce.remove(editor);
        }
        $('#areaEditorDocumento').slideUp();
    });

    $(document).on('click', '#btnRedigirDocumento', function () {
        $('#tituloDocumento').val('');
        tinymce.remove('#editorDocumento');
        setTimeout(() => {
            tinymce.init({
                selector: '#editorDocumento',
                height: 350,
                menubar: true,
                plugins: 'lists link image table code autoresize',
                toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link image | alignleft aligncenter alignright | table | code',
                language: 'pt_BR',
                language_url: '/vendor/tinymce/langs/pt_BR.js',
                branding: false,
            });
        }, 100);
        $('#areaEditorDocumento').slideDown();
    });

    $(document).on('click', '#btnAssistenteIa', function () {
        if (!tipoSelecionado || tipoSelecionado === 'Selecione o tipo de documento') {
            Swal.fire({
                icon: 'warning',
                title: 'Tipo de Documento Obrigatório',
                text: 'Por favor, selecione um tipo de documento antes de usar a Assistente de IA.',
                confirmButtonColor: '#6c63ff'
            });
            return;
        }

        const editor = tinymce.get('editorDocumento');
        const conteudoAtual = editor?.getContent({ format: 'text' })?.trim();

        const gerarIA = () => {
            let promptGerado = `Você é um redator jurídico de um escritório de advocacia brasileiro. Gere o conteúdo completo de um documento do tipo "${tipoSelecionado}", em português formal e profissional.

Use os seguintes dados do advogado já inseridos no corpo do texto (não como variáveis ou código):

- Nome: ${userName}
- CPF: ${userCpf}
- OAB: ${userOab}
- Cidade: ${userCidade}
- Estado: ${userEstado}

IMPORTANTE: Não escreva códigos, não use aspas, não utilize variáveis como {{ Auth::user() }}, {{ $variavel }}, ou trechos em Blade, PHP ou qualquer linguagem. Não gere blocos de código (como \`\`\`php).

Gere apenas o texto corrido como seria escrito em um Word ou Google Docs, pronto para ser lido pelo cliente. Comece com o conteúdo direto.`;

            Swal.fire({
                title: 'Consultando IA...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: urlAssistenteIa,
                method: 'POST',
                data: { prompt: promptGerado },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (resp) {
                    Swal.close();

                    let textoBruto = resp.resposta || '';
                    let textoLimpo = textoBruto
                        .replace(/```[\s\S]*?```/g, '')
                        .replace(/{{.*?}}/g, '')
                        .replace(/Auth::user\(\)->.*?(\n|$)/g, '')
                        .replace(/\[\[.*?\]\]/g, '')
                        .replace(/\\n/g, '\n')
                        .replace(/\\+"/g, '"')
                        .replace(/\\'/g, "'")
                        .replace(/\\\\/g, '')
                        .trim();

                    let paragrafoFormatado = textoLimpo.split(/\n\s*\n/)
                        .map(p => `<p>${p.replace(/\n/g, '<br>')}</p>`)
                        .join('\n');

                    tinymce.remove('#editorDocumento');

                    setTimeout(() => {
                        tinymce.init({
                            selector: '#editorDocumento',
                            height: 350,
                            menubar: true,
                            plugins: 'lists link image table code autoresize',
                            toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link image | alignleft aligncenter alignright | table | code',
                            language: 'pt_BR',
                            language_url: '/vendor/tinymce/langs/pt_BR.js',
                            branding: false,
                            setup: function (editor) {
                                editor.on('init', function () {
                                    editor.setContent(paragrafoFormatado);
                                    $('#areaEditorDocumento').slideDown();
                                });
                            }
                        });
                    }, 100);
                },
                error: function (xhr, status, error) {
                    Swal.close();

                    let mensagem = 'Erro ao consultar a IA.';
                    if (xhr.status === 0) {
                        mensagem = 'Servidor da IA não está respondendo. Verifique se o LM Studio está ativo.';
                    } else if (xhr.status === 500) {
                        mensagem = 'Erro interno ao processar a solicitação.';
                    } else if (xhr.responseJSON?.message) {
                        mensagem = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Falha na comunicação',
                        html: mensagem,
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        };

        gerarIA();
    });


    $(document).on('click', '#btnCancelarEditor', function () {
        const editor = tinymce.get('editorDocumento');
        if (editor) {
            editor.setContent('');
            tinymce.remove(editor);
        }
        $('#areaEditorDocumento').slideUp();
    });


    $(document).on('click', '#btnLimparEditor', function () {
        $('#tituloDocumento').val('');
        if (tinymce.get('editorDocumento')) {
            tinymce.get('editorDocumento').setContent('');
        }
    });


    $(document).on('submit', '#formNovoDocumento', function (e) {
        e.preventDefault();
        let conteudo = tinymce.get('editorDocumento')?.getContent() ?? '';
        let titulo = $('#tituloDocumento').val();
        let tipo_id = $('#filtroTipoDocumento').val();

        if (!titulo || !conteudo || !tipo_id) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                html: 'Preencha todos os campos antes de salvar.',
                confirmButtonColor: '#6c63ff'
            });
            return;
        }

        Swal.fire({
            title: 'Salvando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: urlDocumentoStore,
            method: 'POST',
            data: {
                escritorio_id: escritorioId,
                tipo_documento_id: tipo_id,
                titulo: titulo,
                conteudo: conteudo
            },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function () {
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: 'Documento salvo com sucesso.',
                    confirmButtonColor: '#28a745'
                });
                $('#areaEditorDocumento').slideUp();
                tinymce.remove('#editorDocumento');
            },
            error: function (xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: xhr.responseJSON?.message || 'Erro ao salvar documento.',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });

});
