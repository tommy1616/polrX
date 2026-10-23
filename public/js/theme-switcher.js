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
        var cards = document.querySelectorAll('.polrx-theme-card[data-theme]');
        if (!cards.length) return;

        var csrfToken = getMetaContent('csrf-token');
        var busy = false;

        function markActive(themeKey) {
            cards.forEach(function (card) {
                var match = card.getAttribute('data-theme') === themeKey;
                card.classList.toggle('is-active', match);
                card.setAttribute('aria-pressed', match ? 'true' : 'false');
            });
        }

        function selectTheme(card) {
            if (busy) return;
            var nextTheme = card.getAttribute('data-theme');
            var prevTheme = document.documentElement.getAttribute('data-theme') || 'default';
            if (!nextTheme || nextTheme === prevTheme) return;

            busy = true;
            applyTheme(nextTheme);
            markActive(nextTheme);

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
                            throw new Error((payload && payload.message) || '主題儲存失敗');
                        }
                        return payload;
                    });
                })
                .then(function (payload) {
                    if (window.toastr) {
                        toastr.success(payload.message || '主題已更新', '成功');
                    }
                })
                .catch(function (err) {
                    applyTheme(prevTheme);
                    markActive(prevTheme);
                    if (window.toastr) {
                        toastr.error(err.message || '主題更新失敗', '錯誤');
                    }
                })
                .finally(function () {
                    busy = false;
                });
        }

        cards.forEach(function (card) {
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
    });
})();
