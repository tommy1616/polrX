<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ValidatesRequests;

    protected static function currIsAdmin(): bool
    {
        return session('role') == 'admin';
    }

    protected static function isLoggedIn(): bool
    {
        $username = session('username');

        return isset($username);
    }

    protected static function checkRequiredArgs(array $required_args = []): bool
    {
        foreach ($required_args as $arg) {
            if ($arg === null) {
                return false;
            }
        }

        return true;
    }

    protected static function ensureAdmin(): bool
    {
        if (! self::currIsAdmin()) {
            abort(401, 'User not admin.');
        }

        return true;
    }

    protected static function ensureLoggedIn(): bool
    {
        if (! self::isLoggedIn()) {
            abort(401, 'User must be authenticated.');
        }

        return true;
    }
}
