<?php

namespace App\Http\Controllers;

use App\Factories\UserFactory;
use App\Helpers\CryptoHelper;
use App\Helpers\LinkHelper;
use App\Helpers\UserHelper;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function checkLinkAvailability(Request $request)
    {
        $link_ending = $request->input('link_ending');
        $ending_conforms = LinkHelper::validateEnding($link_ending);

        if (! $ending_conforms) {
            return 'invalid';
        } elseif (LinkHelper::linkExists($link_ending)) {
            return 'unavailable';
        } else {
            return 'available';
        }
    }

    public function toggleAPIActive(Request $request)
    {
        self::ensureAdmin();

        $user_id = $request->input('user_id');
        $user = UserHelper::getUserById($user_id, true);

        if (! $user) {
            abort(404, 'User not found.');
        }

        $user->api_active = ($user->api_active == 1) ? 0 : 1;
        $user->save();

        return $user->api_active;
    }

    public function generateNewAPIKey(Request $request)
    {
        self::ensureLoggedIn();

        $user_id = $request->input('user_id');
        $user = UserHelper::getUserById($user_id);

        $username_user_requesting = session('username');
        $user_requesting = UserHelper::getUserByUsername($username_user_requesting);

        if (! $user || ! $user_requesting) {
            abort(404, 'User not found.');
        }

        if ($user->id !== $user_requesting->id) {
            self::ensureAdmin();
        } else {
            if (! $user->api_active) {
                self::ensureAdmin();
            }
        }

        $new_api_key = CryptoHelper::generateRandomHex((int) env('_API_KEY_LENGTH', 15));
        $user->api_key = $new_api_key;
        $user->save();

        return $user->api_key;
    }

    public function editAPIQuota(Request $request)
    {
        self::ensureAdmin();

        $user_id = $request->input('user_id');
        $new_quota = (int) $request->input('new_quota');
        $user = UserHelper::getUserById($user_id, true);

        if (! $user) {
            abort(404, 'User not found.');
        }

        $user->api_quota = $new_quota;
        $user->save();

        return 'OK';
    }

    public function toggleUserActive(Request $request)
    {
        self::ensureAdmin();

        $user_id = $request->input('user_id');
        $user = UserHelper::getUserById($user_id, true);

        if (! $user) {
            abort(404, 'User not found.');
        }

        if (session('username') === $user->username) {
            abort(400, 'Cannot disable your own account.');
        }

        $user->active = ($user->active == 1) ? 0 : 1;
        $user->save();

        return $user->active;
    }

    public function changeUserRole(Request $request)
    {
        self::ensureAdmin();

        $user_id = $request->input('user_id');
        $role = (string) $request->input('role', '');
        $user = UserHelper::getUserById($user_id, true);

        if (! $user) {
            abort(404, 'User not found.');
        }

        if (session('username') === $user->username) {
            abort(400, 'Cannot change your own role.');
        }

        if (! in_array($role, array_values(UserHelper::$USER_ROLES), true)) {
            abort(400, 'Invalid role.');
        }

        $user->role = $role;
        $user->save();

        return 'OK';
    }

    public function addNewUser(Request $request)
    {
        self::ensureAdmin();

        $username = trim((string) $request->input('username', ''));
        $user_password = (string) $request->input('user_password', '');
        $user_email = trim((string) $request->input('user_email', ''));
        $user_role = (string) $request->input('user_role', '');

        $request->merge([
            'username' => $username,
            'user_email' => $user_email,
        ]);

        $this->validate($request, [
            'username' => 'required|string|alpha_dash|max:64|unique:users,username',
            'user_password' => 'required|string|min:1',
            'user_email' => 'required|string|email|unique:users,email',
        ]);

        $ip = $request->ip();

        if (! in_array($user_role, array_values(UserHelper::$USER_ROLES), true)) {
            $user_role = UserHelper::$USER_ROLES['default'];
        }

        $api_active = env('SETTING_AUTO_API') ? 1 : 0;
        $api_key = $api_active ? CryptoHelper::generateRandomHex((int) env('_API_KEY_LENGTH', 15)) : false;

        UserFactory::createUser($username, $user_email, $user_password, 1, $ip, $api_key, $api_active, $user_role);

        return 'OK';
    }

    public function deleteUser(Request $request)
    {
        self::ensureAdmin();

        $user_id = $request->input('user_id');
        $user = UserHelper::getUserById($user_id, true);

        if (! $user) {
            abort(404, 'User not found.');
        }

        if (session('username') === $user->username) {
            abort(400, 'Cannot delete your own account.');
        }

        $user->delete();

        return 'OK';
    }

    public function deleteLink(Request $request)
    {
        self::ensureAdmin();

        $link_ending = $request->input('link_ending');
        $link = LinkHelper::linkExists($link_ending);

        if (! $link) {
            abort(404, 'Link not found.');
        }

        $link->delete();

        return 'OK';
    }

    public function toggleLink(Request $request)
    {
        self::ensureAdmin();

        $link_ending = $request->input('link_ending');
        $link = LinkHelper::linkExists($link_ending);

        if (! $link) {
            abort(404, 'Link not found.');
        }

        $new_status = ($link->is_disabled == 1) ? 0 : 1;
        $link->is_disabled = $new_status;
        $link->save();

        return $new_status ? 'Enable' : 'Disable';
    }

    public function editLinkLongUrl(Request $request)
    {
        self::ensureLoggedIn();

        $link_ending = $request->input('link_ending');
        $link = LinkHelper::linkExists($link_ending);
        $new_long_url = $request->input('new_long_url');

        $this->validate($request, [
            'new_long_url' => ['required', 'string', 'url', 'regex:/\Ahttps?:\/\//i'],
        ]);

        try {
            LinkHelper::validateSafeDestinationUrl($new_long_url);
        } catch (\Exception $e) {
            abort(400, $e->getMessage());
        }

        if (! $link) {
            abort(404, 'Link not found.');
        }

        if ($link->creator === '' || $link->creator !== session('username')) {
            self::ensureAdmin();
        }

        $link->long_url = $new_long_url;
        $link->save();

        return 'OK';
    }
}
