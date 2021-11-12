// noinspection JSUnresolvedFunction

$(function () {
    'use strict'

    $(document).on('click', 'a.collapse-variant-form', function () {
        const $link = $(this);
        const collapsed = $link.hasClass('collapsed');

        const $formField = $link.parents('.registry-table-record__form-field');
        const $formNewVariant = $formField.find('.registry-table-record__form-variant');
        const $select = $formField.find('select.variants-selector');

        $formNewVariant
            .find(":input:not([type=hidden])")
            .val(null)
            .trigger('change');

        $formNewVariant
            .find(":input")
            .prop('disabled', collapsed);

        $select.prop('disabled', !collapsed);

        $link
            .parents('.registry-table-record__form')
            .find('form')
            .yiiActiveForm('validate', false);
    });

    $('.registry-table-record__form-variant.collapse:not(.show)')
        .find(":input")
        .prop('disabled', true);
});
