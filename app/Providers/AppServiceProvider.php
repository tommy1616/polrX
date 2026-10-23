<?php

namespace App\Providers;

use App\Helpers\ThemeHelper;
use App\Helpers\UserHelper;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $aliases = [
            'Artisan' => Artisan::class,
            'Cache' => Cache::class,
            'DB' => DB::class,
            'Hash' => Hash::class,
            'Mail' => Mail::class,
            'Schema' => Schema::class,
            'Session' => Session::class,
            'Validator' => Validator::class,
        ];

        foreach ($aliases as $alias => $facade) {
            if (! class_exists($alias)) {
                class_alias($facade, $alias);
            }
        }
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $theme = session('theme');

            if (! $theme && session('username')) {
                try {
                    $user = UserHelper::getUserByUsername(session('username'));
                    $theme = $user ? $user->theme : 'default';
                    session(['theme' => ThemeHelper::normalize($theme)]);
                } catch (\Throwable) {
                    $theme = 'default';
                }
            }

            $view->with('user_theme', ThemeHelper::normalize($theme));
            $view->with('available_themes', ThemeHelper::all());
        });
    }
}
