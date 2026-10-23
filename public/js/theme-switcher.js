(function() {
    document.addEventListener('DOMContentLoaded', function() {
        var themeOptions = document.querySelectorAll('.theme-option');
        if (!themeOptions.length) {
            return;
        }

        var usernameMeta = document.querySelector('meta[name="current-username"]');
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var username = usernameMeta ? usernameMeta.getAttribute('content') : '';
        var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        function setActiveTheme(selectedTheme) {
            themeOptions.forEach(function(opt) {
                opt.classList.toggle('active', opt.getAttribute('data-theme') === selectedTheme);
            });
        }

        themeOptions.forEach(function(option) {
            option.addEventListener('click', function() {
                var selectedTheme = this.getAttribute('data-theme');
                var previousTheme = document.documentElement.getAttribute('data-theme') || 'default';

                setActiveTheme(selectedTheme);
                document.documentElement.setAttribute('data-theme', selectedTheme);

                var payload = new URLSearchParams();
                payload.append('theme', selectedTheme);
                payload.append('_token', csrfToken);

                fetch('/admin/action/change_theme', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: payload.toString()
                })
                .then(function(response) {
                    return response.json().then(function(data) {
                        if (!response.ok || !data.success) {
                            throw new Error((data && data.message) ? data.message : '主題儲存失敗');
                        }
                        return data;
                    });
                })
                .then(function(data) {
                    if (username) {
                        localStorage.setItem('user_theme_' + username, data.theme);
                    }
                    toastr.success('主題已更新。', '成功');
                })
                .catch(function(error) {
                    document.documentElement.setAttribute('data-theme', previousTheme);
                    setActiveTheme(previousTheme);
                    toastr.error(error.message || '主題更新失敗。', '錯誤');
                    console.error('主題更新失敗：', error);
                });
            });
        });
    });
})();
