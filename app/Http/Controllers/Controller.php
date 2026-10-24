<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use App\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ValidatesRequests;

    protected static function resolveActiveSessionUser(): ?User
    {
        $username = session('username');
        if (! is_string($username) || $username === '') {
            return null;
        }

        if (! env('POLR_SETUP_RAN')) {
            return null;
        }

        try {
            $user = User::where('username', $username)->where('active', 1)->first();
        } catch (\Throwable) {
            return null;
        }

        if (! $user) {
            session()->forget(['username', 'role', 'auth_pw_hash']);

            return null;
        }

        $sessionPwHash = session('auth_pw_hash');
        $currentPwHash = hash('sha256', (string) $user->password);
        if (is_string($sessionPwHash) && $sessionPwHash !== '' && ! hash_equals($currentPwHash, $sessionPwHash)) {
            session()->forget(['username', 'role', 'auth_pw_hash']);

            return null;
        }

        if (session('role') !== $user->role) {
            session(['role' => $user->role]);
        }

        return $user;
    }

    protected static function currIsAdmin(): bool
    {
        $user = self::resolveActiveSessionUser();

        return $user !== null && $user->role === UserHelper::$USER_ROLES['admin'];
    }

    protected static function isLoggedIn(): bool
    {
        return self::resolveActiveSessionUser() !== null;
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
