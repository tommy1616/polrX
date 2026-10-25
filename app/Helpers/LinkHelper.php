<?php
namespace App\Helpers;
use App\Models\Link;
use App\Helpers\BaseHelper;

class LinkHelper {
    static public function validateSafeDestinationUrl($long_link) {
        if (! is_string($long_link) || preg_match('/[\r\n]|%0[da]/i', $long_link)) {
            throw new \Exception('Invalid destination URL.');
        }

        $parsed = @parse_url($long_link);
        if (! is_array($parsed) || empty($parsed['scheme']) || empty($parsed['host'])) {
            throw new \Exception('Invalid destination URL.');
        }

        if (! empty($parsed['user']) || ! empty($parsed['pass'])) {
            throw new \Exception('URLs containing embedded credentials (@) are not allowed.');
        }

        $host = strtolower(trim($parsed['host'], '[]'));
        if (
            $host === 'localhost'
            || str_ends_with($host, '.localhost')
            || str_ends_with($host, '.local')
            || str_ends_with($host, '.internal')
            || $host === 'metadata.google.internal'
            || $host === '169.254.169.254'
        ) {
            throw new \Exception('Private, loopback, or internal destination URLs are not allowed.');
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (! filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw new \Exception('Private, loopback, or reserved IP addresses are not allowed.');
            }
        }

        return true;
    }

    static public function checkIfAlreadyShortened($long_link) {
        /**
         * Provided a long link (string),
         * detect whether the link belongs to an URL shortener.
         * @return boolean
         */
        $shortener_domains = [
            'polr.me',
            'bit.ly',
            'is.gd',
            'tiny.cc',
            'adf.ly',
            'ur1.ca',
            'goo.gl',
            'ow.ly',
            'j.mp',
            't.co',
        ];

        $appAddress = strtolower(trim((string) parse_url('http://' . ltrim((string) env('APP_ADDRESS'), '/'), PHP_URL_HOST)));
        if ($appAddress !== '') {
            $shortener_domains[] = $appAddress;
        }

        $targetHost = strtolower((string) parse_url((string) $long_link, PHP_URL_HOST));
        if ($targetHost === '') {
            return false;
        }

        foreach ($shortener_domains as $shortener_domain) {
            $shortener_domain = strtolower(trim((string) $shortener_domain));
            if ($shortener_domain === '') {
                continue;
            }
            if ($targetHost === $shortener_domain || str_ends_with($targetHost, '.' . $shortener_domain)) {
                return true;
            }
        }
        return false;
    }

    static public function linkExists($link_ending) {
        /**
         * Provided a link ending (string),
         * return the link object, or false.
         * @return Link model instance
         */

        $link = Link::where('short_url', $link_ending)
            ->first();

        if ($link != null) {
            return $link;
        }
        else {
            return false;
        }
    }

    static public function longLinkExists($long_url, $username=false) {
        /**
         * Provided a long link (string),
         * check whether the link is in the DB.
         * If a username is provided, only search for links created by the
         * user.
         * @return boolean
         */
        $link_base = Link::longUrl($long_url)
            ->where('is_custom', 0)
            ->where('secret_key', '');

        if (is_null($username)) {
            // Search for links without a creator only
            $link = $link_base->where('creator', '')->first();
        }
        else if (($username !== false)) {
            // Search for links created by $username only
            $link = $link_base->where('creator', $username)->first();
        }
        else {
            // Search for links created by any user
            $link = $link_base->first();
        }

        if ($link == null) {
            return false;
        }
        else {
            return $link->short_url;
        }
    }

    static public function validateEnding($link_ending) {
        if (! is_string($link_ending)) {
            return 0;
        }
        $is_valid_ending = preg_match('/\A[a-zA-Z0-9_-]{1,64}\z/', $link_ending);
        return $is_valid_ending;
    }

    static public function findPseudoRandomEnding() {
        /**
         * Return an available pseudorandom string of length _PSEUDO_RANDOM_KEY_LENGTH,
         * as defined in .env
         * Edit _PSEUDO_RANDOM_KEY_LENGTH in .env if you wish to increase the length
         * of the pseudorandom string generated.
         * @return string
         */

        $pr_str = '';
        $in_use = true;
        $length = max(5, (int) env('_PSEUDO_RANDOM_KEY_LENGTH', 5));
        $base = (int) env('POLR_BASE', 32);
        $alphabet = ($base === 62)
            ? '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
            : '0123456789abcdefghjkmnpqrstuvwxyz';
        $maxIdx = strlen($alphabet) - 1;

        while ($in_use) {
            // Generate a new string until the ending is not in use
            $buf = '';
            for ($i = 0; $i < $length; $i++) {
                $buf .= $alphabet[random_int(0, $maxIdx)];
            }
            $pr_str = $buf;
            $in_use = LinkHelper::linkExists($pr_str);
        }

        return $pr_str;
    }

    static public function findSuitableEnding() {
        /**
         * Provided an in-use link ending (string),
         * find the next available base-32/62 ending.
         * @return string
         */
        $base = env('POLR_BASE');

        $link = Link::where('is_custom', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($link == null) {
            $base10_val = 0;
            $base_x_val = 0;
        }
        else {
            $latest_link_ending = $link->short_url;
            $base10_val = BaseHelper::toBase10($latest_link_ending, $base);
            $base10_val++;
        }


        $base_x_val = null;

        while (LinkHelper::linkExists($base_x_val) || $base_x_val == null) {
            $base_x_val = BaseHelper::toBase($base10_val, $base);
            $base10_val++;
        }

        return $base_x_val;
    }
}
