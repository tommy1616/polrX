<?php

namespace App\Helpers;

class ThemeHelper
{
    public const THEMES = [
        'default' => [
            'name' => '經典亮白',
            'description' => '簡潔俐落的預設淺色風格',
            'swatches' => ['#f1f5f9', '#ffffff', '#0f172a', '#10b981'],
        ],
        'midnight' => [
            'name' => '極夜深邃',
            'description' => '低眩光深色模式與靛藍強調色',
            'swatches' => ['#0b1120', '#151f32', '#e2e8f0', '#6366f1'],
        ],
        'ocean' => [
            'name' => '海洋微風',
            'description' => '清爽冰藍底色搭配青藍重點色',
            'swatches' => ['#eef6ff', '#ffffff', '#082f49', '#0284c7'],
        ],
        'warm-sand' => [
            'name' => '暖陽沙丘',
            'description' => '溫潤紙感米白與琥珀棕點綴',
            'swatches' => ['#f6f1ea', '#fffdfa', '#291f19', '#c26d38'],
        ],
        'forest' => [
            'name' => '森系翠綠',
            'description' => '自然松針薄荷底色與翡翠綠重點色',
            'swatches' => ['#eef6f1', '#ffffff', '#0b291e', '#059669'],
        ],
        'rose' => [
            'name' => '櫻花粉霧',
            'description' => '溫柔櫻花粉霧底色與玫瑰莓果重點色',
            'swatches' => ['#fdf2f5', '#ffffff', '#2d121c', '#e11d48'],
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
        $items = [];
        foreach (self::THEMES as $key => $meta) {
            $nameKey = "polr.themes.{$key}.name";
            $descKey = "polr.themes.{$key}.desc";
            $translatedName = __($nameKey);
            $translatedDesc = __($descKey);

            $items[$key] = [
                'name' => ($translatedName !== $nameKey) ? $translatedName : $meta['name'],
                'description' => ($translatedDesc !== $descKey) ? $translatedDesc : $meta['description'],
                'swatches' => $meta['swatches'],
            ];
        }

        return $items;
    }
}
