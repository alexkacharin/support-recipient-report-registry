// noinspection ES6ConvertVarToLetConst

if (typeof tableConstructorSettings === "undefined" || !tableConstructorSettings) {
    var tableConstructorSettings = {
        getFieldUrl: '',
        getPermissionUrl: '',
    };
}

$(function () {
    'use strict'

    $(document).on('change input', '.registry-table__form-field input[data-field-input="name"]', function (e) {
        const $input = $(this);

        $input
            .parents('.registry-table__form-field')
            .find('.registry-table__form-field-name')
            .text($input.val());
    });

    $(document).on('change', '.registry-table__form-field-force-render', function (e) {
        const $fieldContainer = $(this).parents('.registry-table__form-field');
        const data = $fieldContainer.find(':input').serializeArray();
        const formData = new FormData();

        $.each(data, function (i, v) {
            formData.append(v.name, v.value);
        });

        $fieldContainer.loading(true);
        $.ajax({
            url: tableConstructorSettings.getFieldUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            complete: function() {
                $fieldContainer.loading(false);
            },
            success: function (html) {
                $fieldContainer.replaceWith(html);
            },
            error: function (error) {
                alert('Произошла ошибка, ' + JSON.stringify(error));
            },
        });
    });

    $(document).on('click', '.registry-table__form-add-field-btn', function (e) {
        const $btn = $(this);
        const $formContainer = $btn.parents('.registry-table__form');

        $formContainer.loading(true);
        $.ajax({
            url: tableConstructorSettings.getFieldUrl,
            type: 'POST',
            dataType: 'html',
            complete: function() {
                $formContainer.loading(false);
            },
            success: function (html) {
                $formContainer.find('.registry-table__form-fields').append(html);
            },
            error: function (error) {
                alert('Произошла ошибка, ' + JSON.stringify(error));
            },
        });
    });

    $(document).on('click', '.registry-table__form-add-permission-btn', function (e) {
        const $btn = $(this);
        const $formContainer = $btn.parents('.registry-table__form');

        $formContainer.loading(true);
        $.ajax({
            url: tableConstructorSettings.getPermissionUrl,
            type: 'POST',
            dataType: 'html',
            complete: function() {
                $formContainer.loading(false);
            },
            success: function (html) {
                $formContainer.find('.registry-table__form-permissions').append(html);
            },
            error: function (error) {
                alert('Произошла ошибка, ' + JSON.stringify(error));
            },
        });
    });

    $(document).on('click', '.registry-table__form-field-template-variant', function (e) {
        const nameTemplate = $(this).data('name-template');
        const $input = $($(this).data('target-input'));
        const template = $input.val().trim();

        if (!template) {
            $input.val(nameTemplate);
        } else {
            $input.val(template + ', ' + nameTemplate);
        }
    });

    $('.registry-table__form-fields').sortable({
        handle: '.form-list__item-move-item-btn',
        animation: 350,
    });
});
