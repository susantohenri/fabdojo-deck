jQuery(() => {
    fabdojoDeckForm_initSelect2()
    jQuery('.add_card_info').css('cursor', 'pointer').click(fabdojoDeckForm_addCardInfo)
})

function fabdojoDeckForm_initSelect2() {
    jQuery('.fabdojo-deck-form select[data-source]').not('[data-select2-id]').each(function () {
        var source = jQuery(this).attr('data-source')
        jQuery(this).select2({ ajax: { url: source, dataType: 'json' } })
    })
}

function fabdojoDeckForm_addCardInfo() {
    jQuery.get(fabdojo_deck_form.card_info_url, HTML => {
        jQuery('table.fabdojo-card-list tbody').append(HTML)
        fabdojoDeckForm_initSelect2()
    })
}