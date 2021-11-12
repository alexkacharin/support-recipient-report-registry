$(function () {
    'use strict'

    $(document).on('submit', '.records-search__form', function() {
        $(this)
            .find(":input")
            .filter(function() { return isEmpty(this.value); })
            .attr("disabled", "disabled");

        return true;
    });
});
