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
    var half = 3; // 7x7 module zone in center for crisp polrX icon
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

    // 3. Center Branded Badge (exact polrX favicon.svg Option C icon!) for 'branded' style
    if (style === 'branded') {
        var badgeSize = 6.2;
        var cStart = (count - badgeSize) / 2;
        var iconScale = (badgeSize / 128).toFixed(5);

        // White quiet cushion around center icon
        parts.push(
            '<rect x="' + (cStart - 0.45).toFixed(2) + '" y="' + (cStart - 0.45).toFixed(2) + '" width="' + (badgeSize + 0.9).toFixed(2) + '" height="' + (badgeSize + 0.9).toFixed(2) + '" rx="1.75" fill="#ffffff"/>'
        );

        // Exact polrX Option C Icon (128x128 coordinate space scaled to badgeSize)
        parts.push(
            '<defs>' +
                '<linearGradient id="polrxQrBg" x1="16" y1="12" x2="112" y2="116" gradientUnits="userSpaceOnUse">' +
                    '<stop offset="0%" stop-color="#111827"/>' +
                    '<stop offset="100%" stop-color="#090D16"/>' +
                '</linearGradient>' +
                '<linearGradient id="polrxQrStar" x1="32" y1="32" x2="96" y2="96" gradientUnits="userSpaceOnUse">' +
                    '<stop offset="0%" stop-color="#34D399"/>' +
                    '<stop offset="100%" stop-color="#10B981"/>' +
                '</linearGradient>' +
            '</defs>' +
            '<g transform="translate(' + cStart.toFixed(3) + ',' + cStart.toFixed(3) + ') scale(' + iconScale + ')">' +
                '<rect width="128" height="128" rx="30" fill="url(#polrxQrBg)"/>' +
                '<path d="M46 30H38C29.163 30 22 37.163 22 46V82C22 90.837 29.163 98 38 98H46" fill="none" stroke="#475569" stroke-width="9" stroke-linecap="round"/>' +
                '<path d="M82 30H90C98.837 30 106 37.163 106 46V82C106 90.837 98.837 98 90 98H82" fill="none" stroke="#475569" stroke-width="9" stroke-linecap="round"/>' +
                '<path d="M64 26L72.5 55.5L102 64L72.5 72.5L64 102L55.5 72.5L26 64L55.5 55.5L64 26Z" fill="url(#polrxQrStar)"/>' +
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
