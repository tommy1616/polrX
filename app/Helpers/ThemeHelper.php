<?php

namespace App\Helpers;

class ThemeHelper
{
    public const THEMES = [
        'default' => [
            'name' => '經典亮白',
            'description' => '簡潔俐落的預設淺色風格',
            'swatches' => ['#ffffff', '#f5f7fa', '#2c3e50', '#18bc9c'],
        ],
        'midnight' => [
            'name' => '極夜深邃',
            'description' => '低眩光深色模式與靛藍強調色',
            'swatches' => ['#0f172a', '#1e293b', '#e2e8f0', '#6366f1'],
        ],
        'ocean' => [
            'name' => '海洋微風',
            'description' => '清爽冰藍底色搭配青藍重點色',
            'swatches' => ['#f0f7ff', '#ffffff', '#0f2942', '#0284c7'],
        ],
        'warm-sand' => [
            'name' => '暖陽沙丘',
            'description' => '溫潤紙感米白與琥珀棕點綴',
            'swatches' => ['#faf6f0', '#ffffff', '#2d241e', '#c26d38'],
        ],
    ];

    public const LEGACY_MAP = [
        'dark-tech' => 'midnight',
        'blue-purple' => 'ocean',
        'soft-pastel' => 'warm-sand',
    ];

    public static function normalize(?string $theme): string
    {
        if (! $theme) {
            return 'default';
        }

        if (isset(self::LEGACY_MAP[$theme])) {
            return self::LEGACY_MAP[$theme];
        }

        return array_key_exists($theme, self::THEMES) ? $theme : 'default';
    }

    public static function isValid(?string $theme): bool
    {
        return $theme !== null && array_key_exists($theme, self::THEMES);
    }

    public static function all(): array
    {
        return self::THEMES;
    }
}
