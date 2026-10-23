<?php

namespace App\Http\Middleware;

use App\Helpers\LocaleHelper;
use App\Helpers\UserHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $queryLocale = $request->query('lang');
        if ($queryLocale && LocaleHelper::isValid($queryLocale)) {
            $locale = $queryLocale;
        } else {
            $locale = $request->session()->get('locale') ?: $request->cookie('polrx_locale');
        }

        $setupRan = (bool) env('POLR_SETUP_RAN', false);

        if ($setupRan && ! $locale && $request->session()->get('username')) {
            try {
                $user = UserHelper::getUserByUsername($request->session()->get('username'));
                if ($user && ! empty($user->locale)) {
                    $locale = $user->locale;
                }
            } catch (\Throwable) {
                $locale = null;
            }
        }

        $normalized = LocaleHelper::normalize($locale);
        $request->session()->put('locale', $normalized);
        App::setLocale($normalized);

        if (! $setupRan && ! $request->is('setup', 'setup/*', 'locale', 'up')) {
            return redirect('/setup');
        }

        return $next($request);
    }
}
