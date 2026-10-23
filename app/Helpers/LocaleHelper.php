<?php

namespace App\Helpers;

class LocaleHelper
{
    public const DEFAULT_LOCALE = 'zh_TW';

    public const LOCALES = [
        'zh_TW' => [
            'code' => 'zh_TW',
            'html_lang' => 'zh-TW',
            'name' => '繁體中文（台灣）',
            'subtitle' => 'Traditional Chinese (Taiwan)',
            'badge' => 'TW',
        ],
        'en' => [
            'code' => 'en',
            'html_lang' => 'en',
            'name' => 'English',
            'subtitle' => 'English (International)',
            'badge' => 'EN',
        ],
        'ja' => [
            'code' => 'ja',
            'html_lang' => 'ja',
            'name' => '日本語',
            'subtitle' => 'Japanese',
            'badge' => 'JP',
        ],
        'ko' => [
            'code' => 'ko',
            'html_lang' => 'ko',
            'name' => '한국어',
            'subtitle' => 'Korean',
            'badge' => 'KR',
        ],
    ];

    public static function normalize(?string $locale): string
    {
        if (! $locale) {
            return self::DEFAULT_LOCALE;
        }

        $trimmed = trim($locale);
        if (in_array($trimmed, ['zh-TW', 'zh_Hant', 'zh-Hant-TW'], true)) {
            return 'zh_TW';
        }

        return array_key_exists($trimmed, self::LOCALES) ? $trimmed : self::DEFAULT_LOCALE;
    }

    public static function isValid(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, self::LOCALES);
    }

    public static function all(): array
    {
        return self::LOCALES;
    }

    public static function currentMeta(?string $locale = null): array
    {
        $norm = self::normalize($locale ?? app()->getLocale());

        return self::LOCALES[$norm];
    }
}
