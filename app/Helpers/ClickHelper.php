<?php

namespace App\Helpers;

use App\Models\Click;
use App\Models\Link;
use GeoIp2\Database\Reader;
use Illuminate\Http\Request;

class ClickHelper
{
    private static function getCountry(?string $ip): ?string
    {
        if (! $ip) {
            return null;
        }

        if (function_exists('geoip')) {
            try {
                return geoip()->getLocation($ip)->iso_code;
            } catch (\Throwable) {
                return null;
            }
        }

        $candidates = [
            storage_path('app/geoip.mmdb'),
            storage_path('geoip/GeoLite2-Country.mmdb'),
            storage_path('geoip/GeoLite2-City.mmdb'),
            '/usr/share/GeoIP/GeoLite2-Country.mmdb',
            '/usr/share/GeoIP/GeoLite2-City.mmdb',
        ];

        foreach ($candidates as $dbPath) {
            if (is_readable($dbPath)) {
                try {
                    $reader = new Reader($dbPath);

                    return $reader->country($ip)->country->isoCode;
                } catch (\Throwable) {
                    return null;
                }
            }
        }

        return null;
    }

    private static function getHost(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return parse_url($url, PHP_URL_HOST) ?: null;
    }

    public static function recordClick(Link $link, Request $request): bool
    {
        try {
            $ip = $request->ip();
            $referer = $request->server('HTTP_REFERER');
            $refererHost = self::getHost(is_string($referer) ? $referer : null);
            $userAgent = $request->server('HTTP_USER_AGENT');

            $click = new Click;
            $click->link_id = $link->id;
            $click->ip = is_string($ip) ? substr($ip, 0, 64) : '';
            $click->country = self::getCountry(is_string($ip) ? $ip : null);
            $click->referer = is_string($referer) ? mb_substr($referer, 0, 250) : null;
            $click->referer_host = is_string($refererHost) ? mb_substr($refererHost, 0, 250) : null;
            $click->user_agent = is_string($userAgent) ? mb_substr($userAgent, 0, 250) : null;
            $click->save();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
