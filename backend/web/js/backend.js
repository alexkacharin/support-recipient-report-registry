$(function () {
    'use strict'

    const $header = $('#header');

    function updateHeaderHeight() {
        $header.css({ minHeight: $('#navbar-top').outerHeight() });
    }

    $(window).resize(function() {
        updateHeaderHeight();
    });

    updateHeaderHeight();
});
