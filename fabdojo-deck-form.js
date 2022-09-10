jQuery(() => {
    fabdojoDeckForm_initSelect2()
    fabdojoDeckForm_updateCardCount()
    jQuery('.fabdojo-deck-form .add_card_info').click(fabdojoDeckForm_addCardInfo)
    jQuery('.fabdojo-deck-form button.delete').click(fabdojoDeckForm_delete)
    jQuery('.fabdojo-deck-form button.save_add').click(fabdojoDeckForm_saveAdd)
    jQuery('.fabdojo-deck-form button.save_edit').click(fabdojoDeckForm_saveEdit)
    jQuery('.fabdojo-deck-form button.save').click(fabdojoDeckForm_save)
})

function fabdojoDeckForm_initSelect2() {
    jQuery('.fabdojo-deck-form select[data-source]').not('[data-select2-id]').each(function () {
        var source = jQuery(this).attr('data-source')
        jQuery(this).select2({width: '100%', ajax: { url: source, dataType: 'json' } })
    })
}

function fabdojoDeckForm_updateCardCount() {
    jQuery('.fabdojo-deck-form [id="get-card-count"]').html(jQuery('table.fabdojo-card-list tbody tr').length)
}

function fabdojoDeckForm_addCardInfo() {
    jQuery.get(fabdojo_deck_form.card_info_url, HTML => {
        jQuery('table.fabdojo-card-list tbody').append(HTML)
        fabdojoDeckForm_initSelect2()
        fabdojoDeckForm_updateCardCount()
    })
}

function fabdojoDeckForm_delete() {
    alert('delete')
}

function fabdojoDeckForm_saveAdd() {
    alert('saveAdd')
}

function fabdojoDeckForm_saveEdit() {
    alert('saveEdit')
}

function fabdojoDeckForm_save() {
    var post = {}
    jQuery('.fabdojo-deck-form select, .fabdojo-deck-form input')
    .not('[name$="[]"]')
    .each(function () {
        var input = jQuery(this)
        post[input.attr('name')] = input.is(':checkbox') ? input.is(':checked') : input.val()
    })

    arrayInputNames = jQuery('.fabdojo-deck-form [name$="[]"]')
        .map(function () { return this.name })
        .get()
        .filter((v, i, a) => a.indexOf(v) === i)

    for (var name of arrayInputNames) {
        post[name] = []
        jQuery(`.fabdojo-deck-form [name="${name}"]`).each(function () {
            var input = jQuery(this)
            post[name].push(input.is(':checkbox') ? input.is(':checked') : input.val())
        })
    }

    jQuery.post(fabdojo_deck_form.create_deck_url, post, response => {
        console.log(response)
    })
}