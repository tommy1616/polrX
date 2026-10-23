<?php
namespace App\Http\Controllers;
use Mail;
use Hash;
use App\Models\User;
use Illuminate\Http\Request;

use App\Helpers\CryptoHelper;
use App\Helpers\ThemeHelper;
use App\Helpers\UserHelper;
use App\Helpers\TotpHelper;

use App\Factories\UserFactory;

class UserController extends Controller {
    public function displayLoginPage(Request $request) {
        return view('login');
    }

    public function displayTotpPage(Request $request) {
        if (!$request->session()->get('pending_2fa_user_id')) {
            return redirect(route('login'))->with('error', '請先登入帳號密碼。');
        }

        return view('login_totp', [
            'pending_username' => $request->session()->get('pending_2fa_username')
        ]);
    }

    public function displaySignupPage(Request $request) {
        return view('signup');
    }

    public function displayLostPasswordPage(Request $request) {
        return view('lost_password');
    }

    public function performLogoutUser(Request $request) {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('index');
    }

    public function performLogin(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');

        $credentials_valid = UserHelper::checkCredentials($username, $password);

        if ($credentials_valid != false) {
            $role = $credentials_valid['role'];
            $user = UserHelper::getUserByUsername($username, true);

            if ($user && $user->totp_enabled && $user->totp_secret) {
                $request->session()->regenerate();
                $request->session()->put('pending_2fa_user_id', $user->id);
                $request->session()->put('pending_2fa_username', $username);
                $request->session()->put('pending_2fa_role', $role);
                $request->session()->forget('username');
                $request->session()->forget('role');

                return redirect()->route('login_totp');
            }

            $request->session()->regenerate();
            $request->session()->put('username', $username);
            $request->session()->put('role', $role);
            $request->session()->put('theme', ThemeHelper::normalize($user ? $user->theme : 'default'));

            return redirect()->route('index');
        }
        else {
            return redirect('login')->with('error', '帳號或密碼錯誤，或帳號尚未啟用。');
        }
    }

    public function performTotpLogin(Request $request) {
        $pendingUserId = $request->session()->get('pending_2fa_user_id');
        if (!$pendingUserId) {
            return redirect(route('login'))->with('error', '登入驗證已失效，請重新登入。');
        }

        $user = UserHelper::getUserById($pendingUserId, true);
        if (!$user || !$user->totp_enabled || !$user->totp_secret || !$user->active) {
            $request->session()->forget('pending_2fa_user_id');
            $request->session()->forget('pending_2fa_username');
            $request->session()->forget('pending_2fa_role');
            return redirect(route('login'))->with('error', '2FA 狀態異常，請重新登入。');
        }

        $code = $request->input('totp_code');
        if (!TotpHelper::verifyCode($user->totp_secret, $code)) {
            return redirect()->route('login_totp')->with('error', '驗證碼錯誤，請再試一次。');
        }

        $verifiedUser = $request->session()->get('pending_2fa_username');
        $verifiedRole = $request->session()->get('pending_2fa_role');
        $request->session()->regenerate();
        $request->session()->put('username', $verifiedUser);
        $request->session()->put('role', $verifiedRole);
        $request->session()->put('theme', ThemeHelper::normalize($user->theme));
        $request->session()->forget('pending_2fa_user_id');
        $request->session()->forget('pending_2fa_username');
        $request->session()->forget('pending_2fa_role');

        return redirect()->route('index');
    }

    public function performSignup(Request $request) {
        if (env('POLR_ALLOW_ACCT_CREATION') == false) {
            return redirect(route('index'))->with('error', 'Sorry, but registration is disabled.');
        }

        if (env('POLR_ACCT_CREATION_RECAPTCHA')) {
            $gRecaptchaResponse = $request->input('g-recaptcha-response');

            $recaptcha = new \ReCaptcha\ReCaptcha(env('POLR_RECAPTCHA_SECRET_KEY'));
            $recaptcha_resp = $recaptcha->verify($gRecaptchaResponse, $request->ip());

            if (!$recaptcha_resp->isSuccess()) {
                return redirect(route('signup'))->with('error', 'You must complete the reCAPTCHA to register.');
            }
        }

        $this->validate($request, [
            'username' => 'required|alpha_dash',
            'password' => 'required',
            'email' => 'required|email'
        ]);

        $username = $request->input('username');
        $password = $request->input('password');
        $email = $request->input('email');

        if (env('SETTING_RESTRICT_EMAIL_DOMAIN')) {
            $email_domain = explode('@', $email)[1];
            $permitted_email_domains = explode(',', env('SETTING_ALLOWED_EMAIL_DOMAINS'));

            if (!in_array($email_domain, $permitted_email_domains)) {
                return redirect(route('signup'))->with('error', 'Sorry, your email\'s domain is not permitted to create new accounts.');
            }
        }

        $ip = $request->ip();

        $user_exists = UserHelper::userExists($username);
        $email_exists = UserHelper::emailExists($email);

        if ($user_exists || $email_exists) {
            return redirect(route('signup'))->with('error', 'Sorry, your email or username already exists. Try again.');
        }

        $acct_activation_needed = env('POLR_ACCT_ACTIVATION');

        if ($acct_activation_needed == false) {
            $active = 1;
            $response = redirect(route('login'))->with('success', 'Thanks for signing up! You may now log in.');
        }
        else {
            $response = redirect(route('login'))->with('success', 'Thanks for signing up! Please confirm your email to continue.');
            $active = 0;
        }

        $api_active = false;
        $api_key = null;

        if (env('SETTING_AUTO_API')) {
            $api_active = 1;
            $api_key = CryptoHelper::generateRandomHex(env('_API_KEY_LENGTH'));
        }

        $user = UserFactory::createUser($username, $email, $password, $active, $ip, $api_key, $api_active);

        if ($acct_activation_needed) {
            Mail::send('emails.activation', [
                'username' => $username, 'recovery_key' => $user->recovery_key, 'ip' => $ip
            ], function ($m) use ($user) {
                $m->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $m->to($user->email, $user->username)->subject(env('APP_NAME') . ' account activation');
            });
        }

        return $response;
    }

    public function performSendPasswordResetCode(Request $request) {
        if (!env('SETTING_PASSWORD_RECOV')) {
            return redirect(route('index'))->with('error', 'Password recovery is disabled.');
        }

        $email = $request->input('email');
        $ip = $request->ip();
        $user = UserHelper::getUserByEmail($email);

        if (!$user) {
            return redirect(route('lost_password'))->with('error', 'Email is not associated with a user.');
        }

        $recovery_key = UserHelper::resetRecoveryKey($user->username);

        Mail::send('emails.lost_password', [
            'username' => $user->username, 'recovery_key' => $recovery_key, 'ip' => $ip
        ], function ($m) use ($user) {
            $m->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $m->to($user->email, $user->username)->subject(env('APP_NAME') . ' Password Reset');
        });

        return redirect(route('index'))->with('success', 'Password reset email sent. Check your inbox for details.');
    }

    public function performActivation(Request $request, $username, $recovery_key) {
        $user = UserHelper::getUserByUsername($username, true);

        if (UserHelper::userResetKeyCorrect($username, $recovery_key, true)) {
            $user->active = 1;
            $user->save();

            UserHelper::resetRecoveryKey($username);
            return redirect(route('login'))->with('success', 'Account activated. You may now login.');
        }
        else {
            return redirect(route('index'))->with('error', 'Username or activation key incorrect.');
        }
    }

    public function performPasswordReset(Request $request, $username, $recovery_key) {
        $new_password = $request->input('new_password');
        $user = UserHelper::getUserByUsername($username);

        if (UserHelper::userResetKeyCorrect($username, $recovery_key)) {
            if (!$new_password) {
                return view('reset_password');
            }

            $user->password = Hash::make($new_password);
            $user->save();

            UserHelper::resetRecoveryKey($username);
            return redirect(route('login'))->with('success', 'Password reset. You may now login.');
        }
        else {
            return redirect(route('index'))->with('error', 'Username or reset key incorrect.');
        }
    }
}
