// AJAX settings
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Escape jQuery selectors
function esc_selector(selector) {
    return selector.replace( /(:|\.|\[|\]|,)/g, "\\$1" );
}

jQuery.fn.clearForm = function() {
    $(this).find('input').not(':button, :submit, :reset, :hidden')
        .val('')
        .removeAttr('checked')
        .removeAttr('selected');

    return this;
};

// Global navbar locale switcher
$(function () {
    $(document).on('click', '.polrx-nav-locale-item[data-locale]', function (e) {
        e.preventDefault();
        var nextLocale = $(this).attr('data-locale');
        var currentLocale = $('meta[name="current-locale"]').attr('content') || 'zh_TW';
        if (!nextLocale || nextLocale === currentLocale) return;

        var csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
        $.ajax({
            url: '/locale',
            method: 'POST',
            dataType: 'json',
            data: {
                locale: nextLocale,
                _token: csrfToken
            }
        }).done(function () {
            window.location.reload();
        }).fail(function () {
            var i18n = window.POLR_I18N || {};
            if (window.toastr) {
                toastr.error((i18n.admin_js && i18n.admin_js.locale_save_failed) || 'Error', (i18n.toast && i18n.toast.error) || 'Error');
            }
        });
    });
});

// Output helpful console message
console.log('%cpolrX', 'font-size:5em;color:green');
console.log('%cGitHub: https://github.com/tommy1616/polrX', 'color:blue');

// Set up Angular module
var polr = angular.module('polr',[]);
