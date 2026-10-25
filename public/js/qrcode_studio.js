(function () {
    var currentType = 'url';
    var currentStyle = 'branded';

    function escapeWifiField(val) {
        return String(val || '').replace(/([\\;,:])/g, '\\$1');
    }

    function buildPayload() {
        if (currentType === 'url') {
            return ($('#qr-input-url').val() || '').trim();
        }
        if (currentType === 'text') {
            return ($('#qr-input-text').val() || '').trim();
        }
        if (currentType === 'wifi') {
            var ssid = ($('#qr-wifi-ssid').val() || '').trim();
            if (!ssid) return '';
            var enc = $('#qr-wifi-enc').val() || 'WPA';
            var pass = $('#qr-wifi-pass').val() || '';
            var hidden = $('#qr-wifi-hidden').is(':checked') ? 'true' : 'false';
            if (enc === 'nopass') {
                return 'WIFI:T:nopass;S:' + escapeWifiField(ssid) + ';H:' + hidden + ';;';
            }
            return 'WIFI:T:' + enc + ';S:' + escapeWifiField(ssid) + ';P:' + escapeWifiField(pass) + ';H:' + hidden + ';;';
        }
        if (currentType === 'email') {
            var to = ($('#qr-email-to').val() || '').trim();
            var subject = ($('#qr-email-subject').val() || '').trim();
            var body = ($('#qr-email-body').val() || '').trim();
            if (!to && !subject && !body) return '';
            var query = [];
            if (subject) query.push('subject=' + encodeURIComponent(subject));
            if (body) query.push('body=' + encodeURIComponent(body));
            return 'mailto:' + to + (query.length ? '?' + query.join('&') : '');
        }
        if (currentType === 'sms') {
            var mode = $('#qr-sms-mode').val() || 'tel';
            var phone = ($('#qr-sms-phone').val() || '').trim();
            var smsBody = ($('#qr-sms-body').val() || '').trim();
            if (!phone) return '';
            if (mode === 'sms') {
                return 'SMSTO:' + phone + ':' + smsBody;
            }
            return 'tel:' + phone;
        }
        return '';
    }

    function getThemeQrColors(style) {
        var rootStyle = window.getComputedStyle(document.documentElement);
        var primary = (rootStyle.getPropertyValue('--polrx-primary') || '#10b981').trim();
        var navBg = (rootStyle.getPropertyValue('--polrx-nav-bg') || '#0f172a').trim();

        if (style === 'classic') {
            return {
                moduleColor: '#0f172a',
                finderOuter: '#0f172a',
                finderInner: '#0f172a',
                accentColor: '#0f172a'
            };
        }

        var moduleTone = (navBg && navBg.charAt(0) === '#') ? navBg : '#0f172a';
        return {
            moduleColor: moduleTone,
            finderOuter: moduleTone,
            finderInner: primary || '#10b981',
            accentColor: primary || '#10b981'
        };
    }

    function isFinderZone(row, col, count) {
        if (row < 8 && col < 8) return true;
        if (row < 8 && col >= count - 8) return true;
        if (row >= count - 8 && col < 8) return true;
        return false;
    }

    function isCenterLogoZone(row, col, count, style) {
        if (style !== 'branded') return false;
        var center = Math.floor(count / 2);
        var half = 3;
        return (row >= center - half && row <= center + half && col >= center - half && col <= center + half);
    }

    function buildStyledQrSvg(text, style) {
        var scratch = document.createElement('div');
        var qr = new QRCode(scratch, {
            text: text,
            width: 256,
            height: 256,
            correctLevel: QRCode.CorrectLevel.H
        });
        var qrModel = qr._oQRCode;
        var count = qrModel.getModuleCount();
        var colors = getThemeQrColors(style);
        var pad = 2.5;
        var total = count + pad * 2;

        var parts = [];
        parts.push(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="' + (-pad) + ' ' + (-pad) + ' ' + total + ' ' + total + '" width="512" height="512" shape-rendering="geometricPrecision">'
        );

        parts.push(
            '<rect x="' + (-pad) + '" y="' + (-pad) + '" width="' + total + '" height="' + total + '" rx="2.2" fill="#ffffff"/>'
        );

        var finderOrigins = [
            [0, 0],
            [0, count - 7],
            [count - 7, 0]
        ];
        var outerRx = (style === 'classic') ? '0.45' : '1.65';
        var innerRx = (style === 'classic') ? '0.25' : '0.85';

        for (var f = 0; f < finderOrigins.length; f++) {
            var r0 = finderOrigins[f][0];
            var c0 = finderOrigins[f][1];
            parts.push(
                '<rect x="' + (c0 + 0.5) + '" y="' + (r0 + 0.5) + '" width="6" height="6" rx="' + outerRx + '" fill="none" stroke="' + colors.finderOuter + '" stroke-width="1"/>'
            );
            parts.push(
                '<rect x="' + (c0 + 2) + '" y="' + (r0 + 2) + '" width="3" height="3" rx="' + innerRx + '" fill="' + colors.finderInner + '"/>'
            );
        }

        var dotSize = (style === 'classic') ? 0.94 : 0.84;
        var dotOffset = (1 - dotSize) / 2;
        var dotRx = (style === 'classic') ? '0.16' : '0.40';

        for (var r = 0; r < count; r++) {
            for (var c = 0; c < count; c++) {
                if (isFinderZone(r, c, count) || isCenterLogoZone(r, c, count, style)) {
                    continue;
                }
                if (qrModel.isDark(r, c)) {
                    var x = (c + dotOffset).toFixed(3);
                    var y = (r + dotOffset).toFixed(3);
                    parts.push(
                        '<rect x="' + x + '" y="' + y + '" width="' + dotSize + '" height="' + dotSize + '" rx="' + dotRx + '" fill="' + colors.moduleColor + '"/>'
                    );
                }
            }
        }

        if (style === 'branded') {
            var badgeSize = 6.2;
            var cStart = (count - badgeSize) / 2;
            var iconScale = (badgeSize / 128).toFixed(5);

            parts.push(
                '<rect x="' + (cStart - 0.45).toFixed(2) + '" y="' + (cStart - 0.45).toFixed(2) + '" width="' + (badgeSize + 0.9).toFixed(2) + '" height="' + (badgeSize + 0.9).toFixed(2) + '" rx="1.75" fill="#ffffff"/>'
            );
            parts.push(
                '<defs>' +
                    '<linearGradient id="polrxQrStudioBg" x1="16" y1="12" x2="112" y2="116" gradientUnits="userSpaceOnUse">' +
                        '<stop offset="0%" stop-color="#111827"/>' +
                        '<stop offset="100%" stop-color="#090D16"/>' +
                    '</linearGradient>' +
                    '<linearGradient id="polrxQrStudioStar" x1="32" y1="32" x2="96" y2="96" gradientUnits="userSpaceOnUse">' +
                        '<stop offset="0%" stop-color="#34D399"/>' +
                        '<stop offset="100%" stop-color="#10B981"/>' +
                    '</linearGradient>' +
                '</defs>' +
                '<g transform="translate(' + cStart.toFixed(3) + ',' + cStart.toFixed(3) + ') scale(' + iconScale + ')">' +
                    '<rect width="128" height="128" rx="30" fill="url(#polrxQrStudioBg)"/>' +
                    '<path d="M46 30H38C29.163 30 22 37.163 22 46V82C22 90.837 29.163 98 38 98H46" fill="none" stroke="#475569" stroke-width="9" stroke-linecap="round"/>' +
                    '<path d="M82 30H90C98.837 30 106 37.163 106 46V82C106 90.837 98.837 98 90 98H82" fill="none" stroke="#475569" stroke-width="9" stroke-linecap="round"/>' +
                    '<path d="M64 26L72.5 55.5L102 64L72.5 72.5L64 102L55.5 72.5L26 64L55.5 55.5L64 26Z" fill="url(#polrxQrStudioStar)"/>' +
                '</g>'
            );
        }

        parts.push('</svg>');
        return parts.join('');
    }

    function renderStudioQr() {
        var stage = document.getElementById('polrx-qr-studio-stage');
        if (!stage) return;
        var payload = buildPayload() || window.location.origin;
        try {
            stage.innerHTML = buildStyledQrSvg(payload, currentStyle);
        } catch (e) {
            // Ignore transient encoding overflow
        }
    }

    $(document).on('click', '.qr-type-btn[data-qr-type]', function () {
        var type = $(this).attr('data-qr-type');
        if (!type) return;
        currentType = type;
        $('.qr-type-btn').removeClass('is-active');
        $(this).addClass('is-active');
        $('.qr-form-pane').removeClass('is-active');
        $('.qr-form-pane[data-pane="' + type + '"]').addClass('is-active');
        renderStudioQr();
    });

    $(document).on('click', '.qr-style-pill[data-qr-style]', function () {
        var nextStyle = $(this).attr('data-qr-style');
        if (!nextStyle) return;
        currentStyle = nextStyle;
        $('.qr-style-pill').removeClass('is-active');
        $(this).addClass('is-active');
        renderStudioQr();
    });

    $('#qr-wifi-enc').on('change', function () {
        if ($(this).val() === 'nopass') {
            $('#qr-wifi-pass-group').slideUp(120);
        } else {
            $('#qr-wifi-pass-group').slideDown(120);
        }
        renderStudioQr();
    });

    $('#qr-sms-mode').on('change', function () {
        if ($(this).val() === 'sms') {
            $('#qr-sms-body-group').slideDown(120);
        } else {
            $('#qr-sms-body-group').slideUp(120);
        }
        renderStudioQr();
    });

    $('.qr-form-pane input, .qr-form-pane textarea, .qr-form-pane select').on('input change', function () {
        renderStudioQr();
    });

    $('#qr-studio-generate').on('click', function () {
        renderStudioQr();
    });

    $('#qr-studio-download-png').on('click', function () {
        var svgEl = document.querySelector('#polrx-qr-studio-stage svg');
        if (!svgEl) return;

        var serializer = new XMLSerializer();
        var svgStr = serializer.serializeToString(svgEl);
        var svgBlob = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
        var url = URL.createObjectURL(svgBlob);

        var img = new Image();
        img.onload = function () {
            var canvas = document.createElement('canvas');
            canvas.width = 1024;
            canvas.height = 1024;
            var ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, 1024, 1024);
            ctx.drawImage(img, 0, 0, 1024, 1024);
            URL.revokeObjectURL(url);

            var a = document.createElement('a');
            a.download = 'polrX-qr-' + currentType + '.png';
            a.href = canvas.toDataURL('image/png');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        };
        img.src = url;
    });

    $('#qr-studio-download-svg').on('click', function () {
        var svgEl = document.querySelector('#polrx-qr-studio-stage svg');
        if (!svgEl) return;

        var serializer = new XMLSerializer();
        var svgStr = serializer.serializeToString(svgEl);
        var svgBlob = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
        var url = URL.createObjectURL(svgBlob);

        var a = document.createElement('a');
        a.download = 'polrX-qr-' + currentType + '.svg';
        a.href = url;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(function () {
            URL.revokeObjectURL(url);
        }, 500);
    });

    $(function () {
        renderStudioQr();
    });
})();
