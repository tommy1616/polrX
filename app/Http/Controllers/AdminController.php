<?php

namespace App\Http\Controllers;

use App\Helpers\LocaleHelper;
use App\Helpers\ThemeHelper;
use App\Helpers\TotpHelper;
use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function displayAdminPage(Request $request)
    {
        if (! $this->isLoggedIn()) {
            return redirect(route('login'))->with('error', __('polr.messages.login_required_admin'));
        }

        $username = session('username');
        $role = session('role');

        $user = UserHelper::getUserByUsername($username);

        if (! $user) {
            return redirect(route('index'))->with('error', __('polr.messages.account_invalid'));
        }

        $normalizedTheme = ThemeHelper::normalize($user->theme);
        $request->session()->put('theme', $normalizedTheme);

        $normalizedLocale = LocaleHelper::normalize($request->session()->get('locale') ?: ($user->locale ?? null));
        $request->session()->put('locale', $normalizedLocale);
        App::setLocale($normalizedLocale);

        $pendingTotpSecret = $request->session()->get('pending_totp_secret');
        $totpLabel = $username.'@'.env('APP_ADDRESS');
        $totpIssuer = env('APP_NAME');

        return view('admin', [
            'role' => $role,
            'admin_role' => UserHelper::$USER_ROLES['admin'],
            'user_roles' => UserHelper::$USER_ROLES,
            'api_key' => $user->api_key,
            'api_active' => $user->api_active,
            'api_quota' => $user->api_quota,
            'user_id' => $user->id,
            'totp_enabled' => (bool) $user->totp_enabled,
            'pending_totp_secret' => $pendingTotpSecret,
            'pending_totp_otpauth' => $pendingTotpSecret ? TotpHelper::getOtpAuthUrl($totpIssuer, $totpLabel, $pendingTotpSecret) : null,
            'totp_label' => $totpLabel,
            'user_theme' => $normalizedTheme,
            'available_themes' => ThemeHelper::all(),
            'user_locale' => $normalizedLocale,
            'available_locales' => LocaleHelper::all(),
        ]);
    }

    public function changePassword(Request $request)
    {
        if (! $this->isLoggedIn()) {
            return abort(404);
        }

        $old_password = $request->input('current_password', '');
        $new_password = $request->input('new_password', '');

        if (! is_string($old_password) || ! is_string($new_password) || strlen(trim($new_password)) < 8) {
            return redirect(route('admin').'#settings')->with('error', __('polr.messages.password_empty'));
        }

        $username = session('username');

        if (UserHelper::checkCredentials($username, $old_password) == false) {
            return redirect(route('admin').'#settings')->with('error', __('polr.messages.current_password_wrong'));
        }

        $user = UserHelper::getUserByUsername($username);
        $user->password = Hash::make($new_password);
        $user->save();

        $request->session()->regenerate();
        $request->session()->put('auth_pw_hash', hash('sha256', (string) $user->password));

        return redirect(route('admin').'#settings')->with('success', __('polr.messages.password_updated'));
    }

    public function startTotpSetup(Request $request)
    {
        self::ensureLoggedIn();
        $user = UserHelper::getUserByUsername(session('username'));

        if (! $user) {
            return redirect(route('admin').'#totp')->with('error', __('polr.messages.user_not_found'));
        }

        $request->session()->put('pending_totp_secret', TotpHelper::generateSecret());

        return redirect(route('admin').'#totp')->with('success', __('polr.messages.totp_scan_prompt'));
    }

    public function confirmTotpSetup(Request $request)
    {
        self::ensureLoggedIn();
        $username = session('username');
        $user = UserHelper::getUserByUsername($username);
        $pendingSecret = $request->session()->get('pending_totp_secret');
        $currentPassword = $request->input('totp_current_password');
        $totpCode = $request->input('totp_code');

        if (! $user || ! $pendingSecret) {
            return redirect(route('admin').'#totp')->with('error', __('polr.messages.totp_no_pending'));
        }

        if (UserHelper::checkCredentials($username, $currentPassword) == false) {
            return redirect(route('admin').'#totp')->with('error', __('polr.messages.totp_password_wrong'));
        }

        if (! TotpHelper::verifyCode($pendingSecret, $totpCode)) {
            return redirect(route('admin').'#totp')->with('error', __('polr.messages.totp_code_wrong'));
        }

        $user->totp_secret = $pendingSecret;
        $user->totp_enabled = 1;
        $user->save();

        $request->session()->forget('pending_totp_secret');

        return redirect(route('admin').'#totp')->with('success', __('polr.messages.totp_enabled'));
    }

    public function cancelTotpSetup(Request $request)
    {
        self::ensureLoggedIn();
        $request->session()->forget('pending_totp_secret');

        return redirect(route('admin').'#totp')->with('info', __('polr.messages.totp_cancelled'));
    }

    public function disableTotp(Request $request)
    {
        self::ensureLoggedIn();
        $username = session('username');
        $currentPassword = $request->input('disable_totp_current_password');
        $user = UserHelper::getUserByUsername($username);

        if (! $user || ! $user->totp_enabled) {
            return redirect(route('admin').'#totp')->with('error', __('polr.messages.totp_not_enabled'));
        }

        if (UserHelper::checkCredentials($username, $currentPassword) == false) {
            return redirect(route('admin').'#totp')->with('error', __('polr.messages.totp_disable_password_wrong'));
        }

        $user->totp_enabled = 0;
        $user->totp_secret = null;
        $user->save();
        $request->session()->forget('pending_totp_secret');

        return redirect(route('admin').'#totp')->with('success', __('polr.messages.totp_disabled'));
    }

    public function changeTheme(Request $request)
    {
        if (! $this->isLoggedIn()) {
            return response()->json(['success' => false, 'message' => __('polr.messages.unauthenticated')], 401);
        }

        $username = session('username');
        $theme = (string) $request->input('theme', '');

        if (! ThemeHelper::isValid($theme)) {
            return response()->json(['success' => false, 'message' => __('polr.messages.invalid_theme')], 400);
        }

        $user = UserHelper::getUserByUsername($username);
        if (! $user) {
            return response()->json(['success' => false, 'message' => __('polr.messages.user_not_found')], 404);
        }

        $user->theme = $theme;
        $user->save();
        $request->session()->put('theme', $theme);

        $themes = ThemeHelper::all();

        return response()->json([
            'success' => true,
            'message' => __('polr.messages.theme_updated', ['theme' => $themes[$theme]['name']]),
            'theme' => $theme,
        ]);
    }

    public function changeLocale(Request $request)
    {
        $locale = (string) $request->input('locale', '');

        if (! LocaleHelper::isValid($locale)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('polr.messages.invalid_locale')], 400);
            }

            return redirect()->back()->with('error', __('polr.messages.invalid_locale'));
        }

        $request->session()->put('locale', $locale);
        App::setLocale($locale);

        if (env('POLR_SETUP_RAN') && $this->isLoggedIn()) {
            $user = UserHelper::getUserByUsername(session('username'));
            if ($user && Schema::hasColumn('users', 'locale')) {
                $user->locale = $locale;
                $user->save();
            }
        }

        $localeName = LocaleHelper::LOCALES[$locale]['name'];
        $cookie = cookie('polrx_locale', $locale, 60 * 24 * 365);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('polr.messages.locale_updated', ['locale' => $localeName]),
                'locale' => $locale,
            ])->withCookie($cookie);
        }

        return redirect()->back()
            ->with('success', __('polr.messages.locale_updated', ['locale' => $localeName]))
            ->withCookie($cookie);
    }
}
