<?php

namespace App\Providers;

use App\Helpers\LocaleHelper;
use App\Helpers\ThemeHelper;
use App\Helpers\UserHelper;
use Illuminate\Support\Facades\App;
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
        $this->app->useLangPath(base_path('lang'));

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
        if (str_starts_with((string) env('APP_PROTOCOL', ''), 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            config([
                'session.secure' => true,
                'session.http_only' => true,
                'session.same_site' => 'lax',
            ]);
        }

        View::composer('*', function ($view) {
            if (! array_key_exists('errors', $view->getData())) {
                $view->with('errors', session('errors') ?: new \Illuminate\Support\ViewErrorBag());
            }

            $theme = session('theme');
            $req = app()->bound('request') ? request() : null;
            $queryLocale = $req ? $req->query('lang') : null;
            $locale = ($queryLocale && LocaleHelper::isValid($queryLocale))
                ? $queryLocale
                : (session('locale') ?: ($req ? $req->cookie('polrx_locale') : App::getLocale()));

            $setupRan = (bool) env('POLR_SETUP_RAN', false);

            if ($setupRan && (! $theme || ! $locale) && session('username')) {
                try {
                    $user = UserHelper::getUserByUsername(session('username'));
                    if ($user) {
                        if (! $theme) {
                            $theme = $user->theme ?: 'default';
                            session(['theme' => ThemeHelper::normalize($theme)]);
                        }
                        if (! $locale && ! empty($user->locale)) {
                            $locale = $user->locale;
                            session(['locale' => LocaleHelper::normalize($locale)]);
                        }
                    }
                } catch (\Throwable) {
                    $theme = $theme ?: 'default';
                    $locale = $locale ?: LocaleHelper::DEFAULT_LOCALE;
                }
            }

            $normalizedLocale = LocaleHelper::normalize($locale);
            App::setLocale($normalizedLocale);

            $view->with('user_theme', ThemeHelper::normalize($theme));
            $view->with('available_themes', ThemeHelper::all());
            $view->with('user_locale', $normalizedLocale);
            $view->with('current_locale_meta', LocaleHelper::currentMeta($normalizedLocale));
            $view->with('available_locales', LocaleHelper::all());
        });
    }
}
