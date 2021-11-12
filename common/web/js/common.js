(function($) {
    $('[data-toggle="tooltip"]').tooltip();

    $.fn.btnState = function (state) {
        return this.each(function () {
            var $this = $(this);
            $this.attr('aria-disabled', state ? 'false' : 'true');
            $this.prop('disabled', !state);
            $this[state ? 'removeClass' : 'addClass']('disabled');
        });
    };

    $.fn.loading = function (state, modifier = null) {
        return this.each(function () {
            $(this)[state ? 'addClass' : 'removeClass'](modifier
                ? 'loading loading_' + modifier
                : 'loading');
        });
    };

    $(document).on('click', '.form-list__item-remove-item-btn', function () {
        $(this)
            .parents('.form-list__item')
            .fadeOut(300, function() { $(this).remove(); });

        return true;
    });
}(jQuery));
