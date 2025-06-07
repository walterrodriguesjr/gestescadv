function recarregarTiposDespesaChoices(novoId = null) {
    const escritorioId = $('input[name="escritorio_id"]').val();
    $.get('/tipo-despesas/listar/' + escritorioId, function (tipos) {
        // Limpa o select e destrói o Choices antigo
        if (choicesTipoDespesa) {
            choicesTipoDespesa.destroy();
        }
        let $select = $('#tipo_despesa_id');
        $select.empty().append('<option value="">Selecione o tipo de despesa</option>');
        tipos.forEach(function (tipo) {
            $select.append($('<option>', {
                value: tipo.id,
                text: tipo.titulo,
                selected: (novoId && tipo.id == novoId)
            }));
        });
        // Reinstancia o Choices
        choicesTipoDespesa = new Choices('#tipo_despesa_id', {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            placeholderValue: 'Selecione o tipo de despesa',
            allowHTML: false
        });
        $('#tipo_despesa_id').data('choices-initialized', true);

        // Seleciona o novoId se existir
        if (novoId) {
            choicesTipoDespesa.setChoiceByValue(novoId.toString());
        }
    });
}
