<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Hash;

use App\Models\Link;
use App\Models\User;
use App\Helpers\UserHelper;
use App\Helpers\TotpHelper;

class AdminController extends Controller {
    public function displayAdminPage(Request $request) {
        if (!$this->isLoggedIn()) {
            return redirect(route('login'))->with('error', 'Please login to access your dashboard.');
        }

        $username = session('username');
        $role = session('role');

        $user = UserHelper::getUserByUsername($username);

        if (!$user) {
            return redirect(route('index'))->with('error', 'Invalid or disabled account.');
        }

        $pendingTotpSecret = $request->session()->get('pending_totp_secret');
        $totpLabel = $username . '@' . env('APP_ADDRESS');
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
            'user_theme' => $user->theme ?? 'default',
        ]);
    }

    public function changePassword(Request $request) {
        if (!$this->isLoggedIn()) {
            return abort(404);
        }

        $username = session('username');
        $old_password = $request->input('current_password');
        $new_password = $request->input('new_password');

        if (UserHelper::checkCredentials($username, $old_password) == false) {
            return redirect('admin')->with('error', 'Current password invalid. Try again.');
        }
        else {
            $user = UserHelper::getUserByUsername($username);
            $user->password = Hash::make($new_password);
            $user->save();

            $request->session()->flash('success', "Password changed successfully.");
            return redirect(route('admin'));
        }
    }

    public function startTotpSetup(Request $request) {
        self::ensureLoggedIn();
        $user = UserHelper::getUserByUsername(session('username'));

        if (!$user) {
            return redirect(route('admin'))->with('error', '使用者不存在。');
        }

        $request->session()->put('pending_totp_secret', TotpHelper::generateSecret());
        return redirect(route('admin') . '#settings')->with('success', '請使用驗證器掃描 QR code，並輸入 6 碼驗證碼完成綁定。');
    }

    public function confirmTotpSetup(Request $request) {
        self::ensureLoggedIn();
        $username = session('username');
        $user = UserHelper::getUserByUsername($username);
        $pendingSecret = $request->session()->get('pending_totp_secret');
        $currentPassword = $request->input('totp_current_password');
        $totpCode = $request->input('totp_code');

        if (!$user || !$pendingSecret) {
            return redirect(route('admin') . '#settings')->with('error', '找不到待綁定的 2FA，請重新開始。');
        }

        if (UserHelper::checkCredentials($username, $currentPassword) == false) {
            return redirect(route('admin') . '#settings')->with('error', '目前密碼錯誤，無法啟用 2FA。');
        }

        if (!TotpHelper::verifyCode($pendingSecret, $totpCode)) {
            return redirect(route('admin') . '#settings')->with('error', '驗證碼錯誤，請確認手機上的 6 碼代碼。');
        }

        $user->totp_secret = $pendingSecret;
        $user->totp_enabled = 1;
        $user->save();

        $request->session()->forget('pending_totp_secret');
        return redirect(route('admin') . '#settings')->with('success', '兩步驟驗證已啟用。');
    }

    public function cancelTotpSetup(Request $request) {
        self::ensureLoggedIn();
        $request->session()->forget('pending_totp_secret');
        return redirect(route('admin') . '#settings')->with('info', '已取消 2FA 綁定流程。');
    }

    public function disableTotp(Request $request) {
        self::ensureLoggedIn();
        $username = session('username');
        $currentPassword = $request->input('disable_totp_current_password');
        $user = UserHelper::getUserByUsername($username);

        if (!$user || !$user->totp_enabled) {
            return redirect(route('admin') . '#settings')->with('error', '目前帳號尚未啟用 2FA。');
        }

        if (UserHelper::checkCredentials($username, $currentPassword) == false) {
            return redirect(route('admin') . '#settings')->with('error', '目前密碼錯誤，無法停用 2FA。');
        }

        $user->totp_enabled = 0;
        $user->totp_secret = null;
        $user->save();
        $request->session()->forget('pending_totp_secret');

        return redirect(route('admin') . '#settings')->with('success', '兩步驟驗證已停用。');
    }

    public function changeTheme(Request $request) {
        if (!$this->isLoggedIn()) {
            return response()->json(['success' => false, 'message' => '未登入'], 401);
        }

        $username = session('username');
        $theme = $request->input('theme');

        // 驗證主題名稱
        $allowedThemes = ['default', 'dark-tech', 'blue-purple', 'soft-pastel'];
        if (!in_array($theme, $allowedThemes)) {
            return response()->json(['success' => false, 'message' => '無效的主題'], 400);
        }

        $user = UserHelper::getUserByUsername($username);
        if (!$user) {
            return response()->json(['success' => false, 'message' => '使用者不存在'], 404);
        }

        $user->theme = $theme;
        $user->save();

        return response()->json(['success' => true, 'message' => '主題已更新', 'theme' => $theme]);
    }
}
