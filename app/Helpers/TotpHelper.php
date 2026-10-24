<?php
namespace App\Helpers;

class TotpHelper {
    protected static $base32Alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public static function generateSecret($length = 32) {
        $alphabet = self::$base32Alphabet;
        $secret = '';
        $max = strlen($alphabet) - 1;

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, $max)];
        }

        return $secret;
    }

    public static function verifyCode($secret, $code, $window = 1) {
        $code = self::normalizeCode($code);
        if (!$secret || strlen($code) !== 6) {
            return false;
        }

        $timeSlice = (int) floor(time() / 30);
        for ($offset = -$window; $offset <= $window; $offset++) {
            $candidateSlice = $timeSlice + $offset;
            if (hash_equals(self::generateCode($secret, $candidateSlice), $code)) {
                $replayKey = 'totp_used:' . hash('sha256', (string) $secret) . ':' . $candidateSlice;
                try {
                    if (!\Illuminate\Support\Facades\Cache::add($replayKey, 1, 120)) {
                        return false;
                    }
                } catch (\Throwable) {
                    // Fallback if cache store is unavailable
                }
                return true;
            }
        }

        return false;
    }

    public static function getOtpAuthUrl($issuer, $label, $secret) {
        $issuer = trim((string) $issuer);
        $label = trim((string) $label);

        return 'otpauth://totp/' . rawurlencode($label)
            . '?secret=' . rawurlencode($secret)
            . '&issuer=' . rawurlencode($issuer)
            . '&algorithm=SHA1&digits=6&period=30';
    }

    protected static function generateCode($secret, $timeSlice) {
        $secretKey = self::base32Decode($secret);
        if ($secretKey === false) {
            return '';
        }

        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncatedHash = substr($hash, $offset, 4);
        $value = unpack('N', $truncatedHash)[1] & 0x7FFFFFFF;

        return str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);
    }

    protected static function normalizeCode($code) {
        return preg_replace('/\D+/', '', (string) $code);
    }

    protected static function base32Decode($secret) {
        $secret = strtoupper(preg_replace('/[^A-Z2-7]/', '', (string) $secret));
        if ($secret === '') {
            return false;
        }

        $alphabet = array_flip(str_split(self::$base32Alphabet));
        $buffer = 0;
        $bitsLeft = 0;
        $result = '';

        foreach (str_split($secret) as $char) {
            if (!isset($alphabet[$char])) {
                return false;
            }

            $buffer = ($buffer << 5) | $alphabet[$char];
            $bitsLeft += 5;

            while ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $result .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $result;
    }
}
