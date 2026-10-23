<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPaginationController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Api\ApiAnalyticsController;
use App\Http\Controllers\Api\ApiLinkController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

if (env('POLR_ALLOW_ACCT_CREATION')) {
    Route::get('/signup', [UserController::class, 'displaySignupPage'])->name('signup');
    Route::post('/signup', [UserController::class, 'performSignup'])->middleware('throttle:5,1')->name('psignup');
}

Route::get('/', [IndexController::class, 'showIndexPage'])->name('index');
Route::get('/logout', [UserController::class, 'performLogoutUser'])->name('logout');
Route::get('/login', [UserController::class, 'displayLoginPage'])->name('login');
Route::post('/login', [UserController::class, 'performLogin'])->middleware('throttle:10,1')->name('plogin');
Route::get('/login/totp', [UserController::class, 'displayTotpPage'])->name('login_totp');
Route::post('/login/totp', [UserController::class, 'performTotpLogin'])->middleware('throttle:6,1')->name('plogin_totp');
Route::get('/about-polr', [StaticPageController::class, 'displayAbout'])->name('about');

Route::get('/lost_password', [UserController::class, 'displayLostPasswordPage'])->name('lost_password');
Route::get('/activate/{username}/{recovery_key}', [UserController::class, 'performActivation'])->name('activate');
Route::get('/reset_password/{username}/{recovery_key}', [UserController::class, 'performPasswordReset'])->name('reset_password');

Route::get('/admin', [AdminController::class, 'displayAdminPage'])->name('admin');
Route::post('/admin/action/change_password', [AdminController::class, 'changePassword'])->name('change_password');
Route::post('/admin/action/totp/start', [AdminController::class, 'startTotpSetup'])->name('totp_start');
Route::post('/admin/action/totp/confirm', [AdminController::class, 'confirmTotpSetup'])->name('totp_confirm');
Route::post('/admin/action/totp/cancel', [AdminController::class, 'cancelTotpSetup'])->name('totp_cancel');
Route::post('/admin/action/totp/disable', [AdminController::class, 'disableTotp'])->name('totp_disable');
Route::post('/admin/action/change_theme', [AdminController::class, 'changeTheme'])->name('change_theme');
Route::post('/admin/action/change_locale', [AdminController::class, 'changeLocale'])->name('admin_change_locale');
Route::post('/locale', [AdminController::class, 'changeLocale'])->name('change_locale');

Route::get('/setup', [SetupController::class, 'displaySetupPage'])->name('setup');
Route::post('/setup', [SetupController::class, 'performSetup'])->name('psetup');
Route::get('/setup/finish', [SetupController::class, 'finishSetup'])->name('setup_finish');

Route::get('/admin/stats/{short_url}', [StatsController::class, 'displayStats']);
Route::post('/shorten', [LinkController::class, 'performShorten'])->name('pshorten');
Route::post('/lost_password', [UserController::class, 'performSendPasswordResetCode'])->middleware('throttle:5,1')->name('plost_password');
Route::post('/reset_password/{username}/{recovery_key}', [UserController::class, 'performPasswordReset'])->name('preset_password');

Route::prefix('api/v2')->group(function () {
    Route::post('link_avail_check', [AjaxController::class, 'checkLinkAvailability'])->name('api_link_check');
    Route::post('admin/toggle_api_active', [AjaxController::class, 'toggleAPIActive'])->name('api_toggle_api_active');
    Route::post('admin/generate_new_api_key', [AjaxController::class, 'generateNewAPIKey'])->name('api_generate_new_api_key');
    Route::post('admin/edit_api_quota', [AjaxController::class, 'editAPIQuota'])->name('api_edit_quota');
    Route::post('admin/toggle_user_active', [AjaxController::class, 'toggleUserActive'])->name('api_toggle_user_active');
    Route::post('admin/change_user_role', [AjaxController::class, 'changeUserRole'])->name('api_change_user_role');
    Route::post('admin/add_new_user', [AjaxController::class, 'addNewUser'])->name('api_add_new_user');
    Route::post('admin/delete_user', [AjaxController::class, 'deleteUser'])->name('api_delete_user');
    Route::post('admin/toggle_link', [AjaxController::class, 'toggleLink'])->name('api_toggle_link');
    Route::post('admin/delete_link', [AjaxController::class, 'deleteLink'])->name('api_delete_link');
    Route::post('admin/edit_link_long_url', [AjaxController::class, 'editLinkLongUrl'])->name('api_edit_link_long_url');

    Route::get('admin/get_admin_users', [AdminPaginationController::class, 'paginateAdminUsers'])->name('api_get_admin_users');
    Route::get('admin/get_admin_links', [AdminPaginationController::class, 'paginateAdminLinks'])->name('api_get_admin_links');
    Route::get('admin/get_user_links', [AdminPaginationController::class, 'paginateUserLinks'])->name('api_get_user_links');
});

Route::prefix('api/v2')->middleware('api.polr')->group(function () {
    Route::match(['get', 'post'], 'action/shorten', [ApiLinkController::class, 'shortenLink'])->name('api_shorten_url');
    Route::post('action/shorten_bulk', [ApiLinkController::class, 'shortenLinksBulk'])->name('api_shorten_url_bulk');
    Route::match(['get', 'post'], 'action/lookup', [ApiLinkController::class, 'lookupLink'])->name('api_lookup_url');
    Route::match(['get', 'post'], 'data/link', [ApiAnalyticsController::class, 'lookupLinkStats'])->name('api_link_analytics');
});

Route::get('/{short_url}', [LinkController::class, 'performRedirect']);
Route::get('/{short_url}/{secret_key}', [LinkController::class, 'performRedirect']);
