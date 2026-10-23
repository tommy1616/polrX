<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\DataTablesHelper as DataTables;

use App\Models\Link;
use App\Models\User;
use App\Helpers\UserHelper;

class AdminPaginationController extends Controller {
    private function escapeJsArg($value): string {
        return e(addcslashes((string) $value, "\\'\n\r"));
    }

    public function renderLongUrlCell($link) {
        return '<a target="_blank" rel="noopener noreferrer" title="' . e($link->long_url) . '" href="'. e($link->long_url) .'">' . e(Str::limit($link->long_url, 50)) . '</a>
            <a class="btn btn-primary btn-xs edit-long-link-btn" ng-click="editLongLink(\'' . $this->escapeJsArg($link->short_url) . '\', \'' . $this->escapeJsArg($link->long_url) . '\')"><i class="fa fa-edit edit-link-icon"></i></a>';
    }

    public function renderClicksCell($link) {
        if (env('SETTING_ADV_ANALYTICS')) {
            return $link->clicks . ' <a target="_blank" class="stats-icon" href="/admin/stats/' . e($link->short_url) . '">
                <i class="fa fa-area-chart" aria-hidden="true"></i>
            </a>';
        }
        else {
            return $link->clicks;
        }
    }

    public function renderDeleteUserCell($user) {
        $btn_class = '';
        if (session('username') === $user->username) {
            $btn_class = 'disabled';
        }
        return '<a ng-click="deleteUser($event, \''. $user->id .'\')" class="btn btn-sm btn-danger ' . $btn_class . '">
            ' . e(__('polr.admin.users.delete')) . '
        </a>';
    }

    public function renderDeleteLinkCell($link) {
        return '<a ng-click="deleteLink($event, \'' . e($link->short_url)  . '\')"
            class="btn btn-sm btn-warning delete-link">
            ' . e(__('polr.admin.links.delete')) . '
        </a>';
    }

    public function renderAdminApiActionCell($user) {
        return '<a class="activate-api-modal btn btn-sm btn-info"
            ng-click="openAPIModal($event, \'' . e($user->username) . '\', \'' . $user->api_key . '\', \'' . $user->api_active . '\', \'' . e($user->api_quota) . '\', \'' . $user->id . '\')">
            ' . e(__('polr.admin.users.api_info')) . '
        </a>';
    }

    public function renderToggleUserActiveCell($user) {
        $btn_class = '';
        if (session('username') === $user->username) {
            $btn_class = ' disabled';
        }

        if ($user->active) {
            $active_text = e(__('polr.admin.users.active'));
            $btn_color_class = ' btn-success';
        }
        else {
            $active_text = e(__('polr.admin.users.inactive'));
            $btn_color_class = ' btn-danger';
        }

        return '<a class="btn btn-sm status-display' . $btn_color_class . $btn_class . '" ng-click="toggleUserActiveStatus($event, ' . $user->id . ')">' . $active_text . '</a>';
    }

    public function renderChangeUserRoleCell($user) {
        $select_role = '<select ng-init="changeUserRole.u' . $user->id . ' = \'' . e($user->role) . '\'"
            ng-model="changeUserRole.u' . $user->id . '" ng-change="changeUserRole(changeUserRole.u' . $user->id . ', '.$user->id.')"
            class="form-control"';

        if (session('username') === $user->username) {
            $select_role .= ' disabled';
        }
        $select_role .= '>';

        foreach (UserHelper::$USER_ROLES as $role_text => $role_val) {
            $select_role .= '<option value="' . e($role_val) . '"';

            if ($user->role === $role_val) {
                $select_role .= ' selected';
            }

            $select_role .= '>' . e($role_text) . '</option>';
        }

        $select_role .= '</select>';
        return $select_role;
    }

    public function renderToggleLinkActiveCell($link) {
        $btn_class = 'btn-danger';
        $btn_text = e(__('polr.admin.links.disable'));

        if ($link->is_disabled) {
            $btn_class = 'btn-success';
            $btn_text = e(__('polr.admin.links.enable'));
        }

        return '<a ng-click="toggleLink($event, \'' . e($link->short_url) . '\')" class="btn btn-sm ' . $btn_class . '">
            ' . $btn_text . '
        </a>';
    }

    public function paginateAdminUsers(Request $request) {
        self::ensureAdmin();

        $admin_users = User::select(['username', 'email', 'created_at', 'active', 'api_key', 'api_active', 'api_quota', 'role', 'id']);
        return DataTables::of($admin_users)
            ->addColumn('api_action', fn($user) => $this->renderAdminApiActionCell($user))
            ->addColumn('toggle_active', fn($user) => $this->renderToggleUserActiveCell($user))
            ->addColumn('change_role', fn($user) => $this->renderChangeUserRoleCell($user))
            ->addColumn('delete', fn($user) => $this->renderDeleteUserCell($user))
            ->rawColumns(['api_action', 'toggle_active', 'change_role', 'delete'])
            ->make(true);
    }

    public function paginateAdminLinks(Request $request) {
        self::ensureAdmin();

        $admin_links = Link::select(['short_url', 'long_url', 'clicks', 'created_at', 'creator', 'is_disabled']);
        return DataTables::of($admin_links)
            ->addColumn('disable', fn($link) => $this->renderToggleLinkActiveCell($link))
            ->addColumn('delete', fn($link) => $this->renderDeleteLinkCell($link))
            ->editColumn('clicks', fn($link) => $this->renderClicksCell($link))
            ->editColumn('long_url', fn($link) => $this->renderLongUrlCell($link))
            ->rawColumns(['disable', 'delete', 'clicks', 'long_url'])
            ->make(true);
    }

    public function paginateUserLinks(Request $request) {
        self::ensureLoggedIn();

        $username = session('username');
        $user_links = Link::where('creator', $username)
            ->select(['id', 'short_url', 'long_url', 'clicks', 'created_at']);

        return DataTables::of($user_links)
            ->editColumn('clicks', fn($link) => $this->renderClicksCell($link))
            ->editColumn('long_url', fn($link) => $this->renderLongUrlCell($link))
            ->rawColumns(['clicks', 'long_url'])
            ->make(true);
    }
}
