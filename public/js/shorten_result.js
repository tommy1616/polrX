var original_link;
var currentQrStyle = 'branded';
var cachedQrMatrix = null;

function select_text() {
    $('.result-box').focus().select();
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

    // Use a dark readable tone for modules and theme primary for finder core & badge
    var moduleTone = (navBg && navBg.charAt(0) === '#') ? navBg : '#0f172a';
    return {
        moduleColor: moduleTone,
        finderOuter: moduleTone,
        finderInner: primary || '#10b981',
        accentColor: primary || '#10b981'
    };
}

function buildQrMatrix(text) {
    var scratch = document.createElement('div');
    var qr = new QRCode(scratch, {
        text: text,
        width: 256,
        height: 256,
        correctLevel: QRCode.CorrectLevel.H
    });
    return qr._oQRCode;
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
    var half = 2; // 5x5 module zone in center
    return (row >= center - half && row <= center + half && col >= center - half && col <= center + half);
}

function buildStyledQrSvg(text, style) {
    if (!cachedQrMatrix) {
        cachedQrMatrix = buildQrMatrix(text);
    }
    var qrModel = cachedQrMatrix;
    var count = qrModel.getModuleCount();
    var colors = getThemeQrColors(style);
    var pad = 2.5;
    var total = count + pad * 2;

    var parts = [];
    parts.push(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="' + (-pad) + ' ' + (-pad) + ' ' + total + ' ' + total + '" width="512" height="512" shape-rendering="geometricPrecision">'
    );

    // Crisp white background with rounded corners
    parts.push(
        '<rect x="' + (-pad) + '" y="' + (-pad) + '" width="' + total + '" height="' + total + '" rx="2.2" fill="#ffffff"/>'
    );

    // 1. Render 3 Finder Patterns (7x7 at top-left, top-right, bottom-left)
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
        // Outer 7x7 ring (stroke=1 centered at +0.5..+6.5 covers [0..7])
        parts.push(
            '<rect x="' + (c0 + 0.5) + '" y="' + (r0 + 0.5) + '" width="6" height="6" rx="' + outerRx + '" fill="none" stroke="' + colors.finderOuter + '" stroke-width="1"/>'
        );
        // Inner 3x3 core ([2..5])
        parts.push(
            '<rect x="' + (c0 + 2) + '" y="' + (r0 + 2) + '" width="3" height="3" rx="' + innerRx + '" fill="' + colors.finderInner + '"/>'
        );
    }

    // 2. Render Data Modules
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

    // 3. Center Branded Badge (5x5 module area) for 'branded' style
    if (style === 'branded') {
        var cStart = Math.floor(count / 2) - 2.35;
        var badgeSize = 4.7;
        var cx = count / 2;
        var cy = count / 2;

        // White cushion + colored squircle badge
        parts.push(
            '<rect x="' + (cStart - 0.35).toFixed(2) + '" y="' + (cStart - 0.35).toFixed(2) + '" width="' + (badgeSize + 0.7).toFixed(2) + '" height="' + (badgeSize + 0.7).toFixed(2) + '" rx="1.45" fill="#ffffff"/>'
        );
        parts.push(
            '<rect x="' + cStart.toFixed(2) + '" y="' + cStart.toFixed(2) + '" width="' + badgeSize.toFixed(2) + '" height="' + badgeSize.toFixed(2) + '" rx="1.2" fill="' + colors.accentColor + '"/>'
        );

        // Minimalist chain-link icon inside center badge (scaled from 24x24 viewBox to ~2.9 units)
        var iconScale = (2.8 / 24).toFixed(4);
        var iconTx = (cx - 1.4).toFixed(3);
        var iconTy = (cy - 1.4).toFixed(3);
        parts.push(
            '<g transform="translate(' + iconTx + ',' + iconTy + ') scale(' + iconScale + ')" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">' +
                '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>' +
                '<path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>' +
            '</g>'
        );
    }

    parts.push('</svg>');
    return parts.join('');
}

function renderCustomQr() {
    var stage = document.getElementById('polrx-qr-stage');
    if (!stage || !original_link) return;
    stage.innerHTML = buildStyledQrSvg(original_link, currentQrStyle);
}

$('.result-box').click(select_text);
$('.result-box').change(function () {
    $(this).val(original_link);
});

$('#generate-qr-code').click(function () {
    var panel = $('#qr-code-panel');
    renderCustomQr();
    panel.slideDown(180);
});

$(document).on('click', '.qr-style-pill[data-qr-style]', function () {
    var nextStyle = $(this).attr('data-qr-style');
    if (!nextStyle) return;
    currentQrStyle = nextStyle;
    $('.qr-style-pill').removeClass('is-active');
    $(this).addClass('is-active');
    renderCustomQr();
});

$('#download-qr-code').click(function () {
    var svgEl = document.querySelector('#polrx-qr-stage svg');
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

        var slug = (original_link || 'link').split('/').pop() || 'link';
        var a = document.createElement('a');
        a.download = 'polrX-qr-' + slug + '.png';
        a.href = canvas.toDataURL('image/png');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    };
    img.src = url;
});

var clipboard = new Clipboard('#clipboard-copy');
clipboard.on('success', function(e) {
    e.clearSelection();
    $('#clipboard-copy').tooltip('show');
});

$('#clipboard-copy').on('blur',function () {
    $(this).tooltip('destroy');
}).on('mouseleave',function () {
    $(this).tooltip('destroy');
});

$(function () {
    original_link = $('.result-box').val();
    select_text();
});
