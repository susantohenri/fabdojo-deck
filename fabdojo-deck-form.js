jQuery(() => {
    fabdojoDeckForm_initSelect2()
    fabdojoDeckForm_updateCardCount()
    jQuery('.fabdojo-deck-form .add_card_info')
    jQuery(`
        .fabdojo-deck-form .add_card_info,
        #fabdojo-deck-admin-form .add_card_info
    `).click(() => {
        fabdojoDeckForm_addCardInfo({})
    })
    jQuery('.fabdojo-deck-form button.delete').click(fabdojoDeckForm_delete)
    jQuery('.fabdojo-deck-form button.save_add').click(fabdojoDeckForm_saveAdd)
    jQuery('.fabdojo-deck-form button.save_edit').click(fabdojoDeckForm_saveEdit)
    jQuery('.fabdojo-deck-form button.save').click(fabdojoDeckForm_save)

    if (fabdojo_deck_form.admin_post_id) fabdojoDeckForm_adminRetrieve(fabdojo_deck_form.admin_post_id)

})

function fabdojoDeckForm_initSelect2() {
    jQuery(`
        .fabdojo-deck-form select[data-source],
        #fabdojo-deck-admin-form select[data-source]
    `).not('[data-select2-id]').each(function () {
        var source = jQuery(this).attr('data-source')
        jQuery(this).select2({ width: '100%', ajax: { url: source, dataType: 'json' } })
    })
}

function fabdojoDeckForm_updateCardCount() {
    jQuery('.fabdojo-deck-form [id="get-card-count"]').html(jQuery('table.fabdojo-card-list tbody tr').length)
}

function fabdojoDeckForm_addCardInfo(cardData) {
    cardData = !jQuery.isEmptyObject(cardData) ? cardData : {
        rowId: '',
        id: '',
        text: '',
        qty: 0
    }
    var option = '' === cardData.id ? '' : `<option value="${cardData.id}">${cardData.text}</option>`
    var cardRow = `
        <tr>
            <td width='50%'>
                <select name='card-name[${cardData.rowId}]' data-source='${fabdojo_deck_form.card_dropdown_source}'>${option}</select>
            </td>
            <td>
                <input type='text' name='card-qty[${cardData.rowId}]' value="${cardData.qty}">
            </td>
            <td>
                <input type='checkbox' name='card-delete[${cardData.rowId}]'>
            </td>
        </tr>
    `
    jQuery('table.fabdojo-card-list tbody').append(cardRow)
    fabdojoDeckForm_initSelect2()
    fabdojoDeckForm_updateCardCount()
}

function fabdojoDeckForm_delete() {
    if (confirm("Are you sure ?") == true) {
        jQuery.post(fabdojo_deck_form.delete_deck_url, {
            id: jQuery(`.fabdojo-deck-form [name='post-id']`).val()
        }, response => {
            window.location = fabdojo_deck_form.redirect_after_delete_url
        })
    }
}

function fabdojoDeckForm_saveAdd() {
    var post = fabdojoDeckForm_collectFormData()
    jQuery.post(fabdojo_deck_form.save_deck_url, post, fabdojoDeckForm_resetFormData)
}

function fabdojoDeckForm_saveEdit() {
    var post = fabdojoDeckForm_collectFormData()
    jQuery.post(fabdojo_deck_form.save_deck_url, post, post_id => {
        jQuery.get(fabdojo_deck_form.retrieve_deck_url, { post_id }, deck => {
            fabdojoDeckForm_resetFormData()
            fabdojoDeckForm_setupFormData(deck)
        })
    })
}

function fabdojoDeckForm_save() {
    var post = fabdojoDeckForm_collectFormData()
    jQuery.post(fabdojo_deck_form.save_deck_url, post, response => {
        window.location = fabdojo_deck_form.redirect_after_save_url
    })
}

function fabdojoDeckForm_collectFormData() {
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
    return post
}

function fabdojoDeckForm_resetFormData() {
    jQuery('.fabdojo-deck-form select, .fabdojo-deck-form input').val('').trigger('change')
    jQuery('table.fabdojo-card-list tbody').html('')
}

function fabdojoDeckForm_setupFormData(deck) {
    jQuery('.fabdojo-deck-form [name="post-id"]').val(deck.post_id)
    if (deck.player_id) jQuery('.fabdojo-deck-form [name="player-id"]').html(`<option value="${deck.player_id}">${deck.player_name}</option>`)
    if (deck.event_id) jQuery('.fabdojo-deck-form [name="event-id"]').html(`<option value="${deck.event_id}">${deck.event_name}</option>`)
    if (deck.hero_id) jQuery('.fabdojo-deck-form [name="hero-id"]').html(`<option value="${deck.hero_id}">${deck.hero_name}</option>`)
    jQuery('.fabdojo-deck-form [name="position"]').val(deck.position)
    for (var card of deck.cards) fabdojoDeckForm_addCardInfo(card)
}

function fabdojoDeckForm_adminRetrieve(post_id) {
    jQuery.get(fabdojo_deck_form.retrieve_deck_url, { post_id }, deck => {
        fabdojoDeckForm_resetFormData()
        fabdojoDeckForm_setupFormData(deck)
    })
}