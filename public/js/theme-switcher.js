(function () {
    'use strict';

    function getMetaContent(name) {
        var el = document.querySelector('meta[name="' + name + '"]');
        return el ? el.getAttribute('content') || '' : '';
    }

    function applyTheme(themeKey) {
        if (!themeKey) return;
        document.documentElement.setAttribute('data-theme', themeKey);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var csrfToken = getMetaContent('csrf-token');
        var i18n = window.POLR_I18N || {};
        var toastTitles = i18n.toast || { success: 'Success', error: 'Error' };
        var adminJs = i18n.admin_js || {};

        // 1. Theme Cards
        var themeCards = document.querySelectorAll('.polrx-theme-card[data-theme]');
        var themeBusy = false;

        function markActiveTheme(themeKey) {
            themeCards.forEach(function (card) {
                var match = card.getAttribute('data-theme') === themeKey;
                card.classList.toggle('is-active', match);
                card.setAttribute('aria-pressed', match ? 'true' : 'false');
            });
        }

        function selectTheme(card) {
            if (themeBusy) return;
            var nextTheme = card.getAttribute('data-theme');
            var prevTheme = document.documentElement.getAttribute('data-theme') || 'default';
            if (!nextTheme || nextTheme === prevTheme) return;

            themeBusy = true;
            applyTheme(nextTheme);
            markActiveTheme(nextTheme);

            var body = new URLSearchParams();
            body.set('theme', nextTheme);
            body.set('_token', csrfToken);

            fetch('/admin/action/change_theme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: body.toString()
            })
                .then(function (res) {
                    return res.json().then(function (payload) {
                        if (!res.ok || !payload || !payload.success) {
                            throw new Error((payload && payload.message) || adminJs.theme_save_failed || 'Error');
                        }
                        return payload;
                    });
                })
                .then(function (payload) {
                    if (window.toastr) {
                        toastr.success(payload.message, toastTitles.success);
                    }
                })
                .catch(function (err) {
                    applyTheme(prevTheme);
                    markActiveTheme(prevTheme);
                    if (window.toastr) {
                        toastr.error(err.message || adminJs.theme_save_failed || 'Error', toastTitles.error);
                    }
                })
                .finally(function () {
                    themeBusy = false;
                });
        }

        themeCards.forEach(function (card) {
            card.addEventListener('click', function () {
                selectTheme(card);
            });
            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    selectTheme(card);
                }
            });
        });

        // 2. Language Cards
        var localeCards = document.querySelectorAll('.polrx-locale-card[data-locale]');
        var localeBusy = false;

        function markActiveLocale(localeCode) {
            localeCards.forEach(function (card) {
                var match = card.getAttribute('data-locale') === localeCode;
                card.classList.toggle('is-active', match);
                card.setAttribute('aria-pressed', match ? 'true' : 'false');
            });
        }

        function selectLocale(card) {
            if (localeBusy) return;
            var nextLocale = card.getAttribute('data-locale');
            var prevLocale = getMetaContent('current-locale') || 'zh_TW';
            if (!nextLocale || nextLocale === prevLocale) return;

            localeBusy = true;
            markActiveLocale(nextLocale);

            var body = new URLSearchParams();
            body.set('locale', nextLocale);
            body.set('_token', csrfToken);

            fetch('/admin/action/change_locale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: body.toString()
            })
                .then(function (res) {
                    return res.json().then(function (payload) {
                        if (!res.ok || !payload || !payload.success) {
                            throw new Error((payload && payload.message) || adminJs.locale_save_failed || 'Error');
                        }
                        return payload;
                    });
                })
                .then(function (payload) {
                    if (window.toastr) {
                        toastr.success(payload.message, toastTitles.success);
                    }
                    setTimeout(function () {
                        window.location.hash = '#language';
                        window.location.reload();
                    }, 350);
                })
                .catch(function (err) {
                    markActiveLocale(prevLocale);
                    localeBusy = false;
                    if (window.toastr) {
                        toastr.error(err.message || adminJs.locale_save_failed || 'Error', toastTitles.error);
                    }
                });
        }

        localeCards.forEach(function (card) {
            card.addEventListener('click', function () {
                selectLocale(card);
            });
            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    selectLocale(card);
                }
            });
        });
    });
})();
