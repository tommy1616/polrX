<?php

namespace App\Http\Controllers;

use App\Factories\UserFactory;
use App\Helpers\CryptoHelper;
use App\Helpers\LocaleHelper;
use App\Helpers\ThemeHelper;
use App\Helpers\UserHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PDO;
use Throwable;

class SetupController extends Controller
{
    public static function getEnvironmentChecks(): array
    {
        $basePath = base_path();
        $envPath = base_path('.env');
        $storageWritable = is_writable(storage_path()) && is_writable(base_path('bootstrap/cache'));
        $envWritable = file_exists($envPath) ? is_writable($envPath) : is_writable($basePath);

        $items = [
            [
                'key' => 'php_version',
                'label' => __('polr.setup.step2.php_version'),
                'detail' => 'PHP '.PHP_VERSION,
                'passed' => version_compare(PHP_VERSION, '8.3.0', '>='),
            ],
            [
                'key' => 'ext_pdo',
                'label' => __('polr.setup.step2.ext_pdo'),
                'detail' => 'pdo_mysql',
                'passed' => extension_loaded('pdo_mysql'),
            ],
            [
                'key' => 'ext_mbstring',
                'label' => __('polr.setup.step2.ext_mbstring'),
                'detail' => 'mbstring',
                'passed' => extension_loaded('mbstring'),
            ],
            [
                'key' => 'ext_bcmath',
                'label' => __('polr.setup.step2.ext_bcmath'),
                'detail' => 'bcmath',
                'passed' => extension_loaded('bcmath'),
            ],
            [
                'key' => 'ext_curl',
                'label' => __('polr.setup.step2.ext_curl'),
                'detail' => 'curl',
                'passed' => extension_loaded('curl'),
            ],
            [
                'key' => 'ext_xml',
                'label' => __('polr.setup.step2.ext_xml'),
                'detail' => 'xml',
                'passed' => extension_loaded('xml'),
            ],
            [
                'key' => 'perm_env',
                'label' => __('polr.setup.step2.perm_env'),
                'detail' => '.env',
                'passed' => $envWritable,
            ],
            [
                'key' => 'perm_storage',
                'label' => __('polr.setup.step2.perm_storage'),
                'detail' => 'storage/ & bootstrap/cache/',
                'passed' => $storageWritable,
            ],
        ];

        $allPassed = collect($items)->every(fn ($item) => $item['passed'] === true);

        return [
            'items' => $items,
            'all_passed' => $allPassed,
        ];
    }

    public static function connectDatabase(
        string $host,
        int $port,
        string $database,
        string $username,
        string $password,
        bool $autoCreate = true
    ): array {
        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $database)) {
            return [
                'success' => false,
                'error' => 'Invalid database name (only letters, numbers, underscores, and hyphens are allowed).',
            ];
        }

        try {
            $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port);
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ]);

            if ($autoCreate) {
                $pdo->exec(sprintf(
                    'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
                    str_replace('`', '``', $database)
                ));
            }

            $pdo->exec(sprintf('USE `%s`', str_replace('`', '``', $database)));
            $version = (string) $pdo->query('SELECT VERSION()')->fetchColumn();

            return [
                'success' => true,
                'version' => $version ?: 'MySQL / MariaDB',
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function displaySetupPage(Request $request)
    {
        if (env('POLR_SETUP_RAN')) {
            return view('error', [
                'message' => __('polr.setup.already_completed'),
            ]);
        }

        $checks = self::getEnvironmentChecks();
        $scheme = $request->isSecure() ? 'https://' : 'http://';
        $host = $request->getHttpHost() ?: 'localhost';

        return view('setup', [
            'checks' => $checks['items'],
            'all_checks_passed' => $checks['all_passed'],
            'detected_protocol' => $scheme,
            'detected_host' => $host,
        ]);
    }

    public function checkEnvironment(Request $request)
    {
        if (env('POLR_SETUP_RAN')) {
            return response()->json(['error' => __('polr.setup.already_completed')], 403);
        }

        return response()->json(self::getEnvironmentChecks());
    }

    public function testDatabaseConnection(Request $request)
    {
        if (env('POLR_SETUP_RAN')) {
            return response()->json(['success' => false, 'message' => __('polr.setup.already_completed')], 403);
        }

        $host = trim((string) $request->input('db:host', 'localhost'));
        $port = (int) $request->input('db:port', 3306) ?: 3306;
        $name = trim((string) $request->input('db:name', 'polr'));
        $user = trim((string) $request->input('db:username', 'root'));
        $pass = (string) $request->input('db:password', '');
        $autoCreate = filter_var($request->input('db:auto_create', 'true'), FILTER_VALIDATE_BOOLEAN);

        $result = self::connectDatabase($host, $port, $name, $user, $pass, $autoCreate);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => __('polr.setup.step3.test_success', ['version' => $result['version']]),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('polr.setup.step3.test_failed', ['error' => $result['error']]),
        ], 422);
    }

    public static function runInstallation(array $input, string $clientIp = '127.0.0.1'): array
    {
        $locale = LocaleHelper::normalize($input['app:locale'] ?? App::getLocale());
        App::setLocale($locale);
        session(['locale' => $locale]);

        $theme = ThemeHelper::normalize($input['app:default_theme'] ?? 'midnight');

        $dbHost = trim((string) ($input['db:host'] ?? 'localhost'));
        $dbPort = (int) ($input['db:port'] ?? 3306) ?: 3306;
        $dbName = trim((string) ($input['db:name'] ?? 'polr'));
        $dbUsername = trim((string) ($input['db:username'] ?? 'root'));
        $dbPassword = (string) ($input['db:password'] ?? '');
        $autoCreateDb = filter_var($input['db:auto_create'] ?? 'true', FILTER_VALIDATE_BOOLEAN);

        $acctUsername = trim((string) ($input['acct:username'] ?? ''));
        $acctEmail = trim((string) ($input['acct:email'] ?? ''));
        $acctPassword = (string) ($input['acct:password'] ?? '');

        if ($acctUsername === '' || $acctEmail === '' || strlen($acctPassword) < 6) {
            return [
                'success' => false,
                'error' => __('polr.setup.step5.validation_error'),
            ];
        }

        $dbCheck = self::connectDatabase($dbHost, $dbPort, $dbName, $dbUsername, $dbPassword, $autoCreateDb);
        if (! $dbCheck['success']) {
            return [
                'success' => false,
                'error' => __('polr.setup.step3.test_failed', ['error' => $dbCheck['error']]),
            ];
        }

        $appName = trim((string) ($input['app:name'] ?? 'polrX')) ?: 'polrX';
        $appProtocol = trim((string) ($input['app:protocol'] ?? 'https://')) ?: 'https://';
        if (! str_ends_with($appProtocol, '://')) {
            $appProtocol = rtrim($appProtocol, ':/').'://';
        }
        $appAddress = trim((string) ($input['app:external_url'] ?? 'localhost'));
        $appAddress = preg_replace('#^https?://#i', '', $appAddress);
        $appAddress = rtrim($appAddress, '/');

        $accessMode = $input['setting:access_mode'] ?? null;
        if ($accessMode === 'members') {
            $stPublicInterface = 'true';
            $stShortenPermission = 'true';
        } elseif ($accessMode === 'private') {
            $stPublicInterface = 'false';
            $stShortenPermission = 'true';
        } elseif ($accessMode === 'public') {
            $stPublicInterface = 'true';
            $stShortenPermission = 'false';
        } else {
            $stPublicInterface = ($input['setting:public_interface'] ?? 'true') === 'false' ? 'false' : 'true';
            $stShortenPermission = ($input['setting:shorten_permission'] ?? 'false') === 'true' ? 'true' : 'false';
        }

        $regSetting = $input['setting:registration_permission'] ?? 'none';
        if ($regSetting === 'no-verification') {
            $polrAcctActivation = false;
            $polrAllowAcctCreation = true;
        } elseif ($regSetting === 'email') {
            $polrAcctActivation = true;
            $polrAllowAcctCreation = true;
        } else {
            $polrAcctActivation = false;
            $polrAllowAcctCreation = false;
        }

        $rawBase = (int) ($input['setting:base'] ?? 32);
        $stBase = in_array($rawBase, [32, 62], true) ? $rawBase : 32;
        $stPseudorEnding = ($input['setting:pseudor_ending'] ?? 'true') === 'false' ? 'false' : 'true';
        $stAdvAnalytics = ($input['setting:adv_analytics'] ?? 'true') === 'false' ? 'false' : 'true';
        $stAutoApiKey = ($input['setting:auto_api_key'] ?? 'false') === 'true' ? 'true' : 'false';
        $stAnonApi = ($input['setting:anon_api'] ?? 'false') === 'true' ? 'true' : 'false';
        $stAnonApiQuota = (int) ($input['setting:anon_api_quota'] ?? 10) ?: 10;
        $stPasswordRecov = ($input['setting:password_recovery'] ?? 'false') === 'true' ? 'true' : 'false';
        $stRestrictEmailDomain = ($input['setting:restrict_email_domain'] ?? 'false') === 'true' ? 'true' : 'false';
        $stAllowedEmailDomains = trim((string) ($input['setting:allowed_email_domains'] ?? ''));
        $stIndexRedirect = trim((string) ($input['setting:index_redirect'] ?? ''));
        $stRedirect404 = ($input['setting:redirect_404'] ?? 'false') === 'true' ? 'true' : 'false';

        $mailHost = trim((string) ($input['app:smtp_server'] ?? ''));
        $mailPort = trim((string) ($input['app:smtp_port'] ?? '587'));
        $mailUsername = trim((string) ($input['app:smtp_username'] ?? ''));
        $mailPassword = (string) ($input['app:smtp_password'] ?? '');
        $mailFrom = trim((string) ($input['app:smtp_from'] ?? ''));
        $mailFromName = trim((string) ($input['app:smtp_from_name'] ?? $appName));
        $mailEnabled = $mailHost !== '';

        // Configure runtime database connection to run migrations & create admin user before locking .env
        Config::set('database.default', 'mysql');
        Config::set('database.connections.mysql.host', $dbHost);
        Config::set('database.connections.mysql.port', $dbPort);
        Config::set('database.connections.mysql.database', $dbName);
        Config::set('database.connections.mysql.username', $dbUsername);
        Config::set('database.connections.mysql.password', $dbPassword);
        DB::purge('mysql');
        DB::reconnect('mysql');

        try {
            $exitCode = Artisan::call('migrate', ['--force' => true]);
            if ($exitCode !== 0) {
                return [
                    'success' => false,
                    'error' => 'Database migration failed (exit code '.$exitCode.').',
                ];
            }

            $existingUser = User::where('username', $acctUsername)->first();
            if ($existingUser) {
                $existingUser->email = $acctEmail;
                $existingUser->password = Hash::make($acctPassword);
                $existingUser->role = UserHelper::$USER_ROLES['admin'];
                $existingUser->active = 1;
                if (Schema::hasColumn('users', 'theme')) {
                    $existingUser->theme = $theme;
                }
                if (Schema::hasColumn('users', 'locale')) {
                    $existingUser->locale = $locale;
                }
                $existingUser->save();
                $adminUser = $existingUser;
            } else {
                $adminUser = UserFactory::createUser(
                    $acctUsername,
                    $acctEmail,
                    $acctPassword,
                    1,
                    $clientIp,
                    false,
                    0,
                    UserHelper::$USER_ROLES['admin']
                );
                if (Schema::hasColumn('users', 'theme')) {
                    $adminUser->theme = $theme;
                }
                if (Schema::hasColumn('users', 'locale')) {
                    $adminUser->locale = $locale;
                }
                $adminUser->save();
            }
        } catch (Throwable $e) {
            return [
                'success' => false,
                'error' => 'Database setup failed: '.$e->getMessage(),
            ];
        }

        $currentKey = config('app.key');
        if (! $currentKey || strlen((string) $currentKey) < 16) {
            $currentKey = 'base64:'.base64_encode(random_bytes(32));
        }
        $setupAuthKey = CryptoHelper::generateRandomHex(16);

        date_default_timezone_set('UTC');
        $dateToday = date('F jS, Y');

        $compiledConfiguration = view('env', [
            'APP_KEY' => $currentKey,
            'APP_NAME' => $appName,
            'APP_PROTOCOL' => $appProtocol,
            'APP_ADDRESS' => $appAddress,
            'APP_STYLESHEET' => '',
            'APP_LOCALE' => $locale,
            'POLR_GENERATED_AT' => $dateToday,
            'POLR_SETUP_RAN' => 'true',
            'MAXMIND_LICENSE_KEY' => trim((string) ($input['maxmind:license_key'] ?? '')),

            'DB_HOST' => $dbHost,
            'DB_PORT' => $dbPort,
            'DB_USERNAME' => $dbUsername,
            'DB_PASSWORD' => $dbPassword,
            'DB_DATABASE' => $dbName,

            'ST_PUBLIC_INTERFACE' => $stPublicInterface,
            'POLR_ALLOW_ACCT_CREATION' => $polrAllowAcctCreation,
            'POLR_ACCT_ACTIVATION' => $polrAcctActivation,
            'POLR_ACCT_CREATION_RECAPTCHA' => ($input['setting:acct_registration_recaptcha'] ?? 'false') === 'true' ? 'true' : 'false',
            'ST_SHORTEN_PERMISSION' => $stShortenPermission,
            'ST_INDEX_REDIRECT' => $stIndexRedirect,
            'ST_REDIRECT_404' => $stRedirect404,
            'ST_PASSWORD_RECOV' => $stPasswordRecov,
            'ST_RESTRICT_EMAIL_DOMAIN' => $stRestrictEmailDomain,
            'ST_ALLOWED_EMAIL_DOMAINS' => $stAllowedEmailDomains,
            'POLR_RECAPTCHA_SITE_KEY' => trim((string) ($input['setting:recaptcha_site_key'] ?? '')),
            'POLR_RECAPTCHA_SECRET' => trim((string) ($input['setting:recaptcha_secret_key'] ?? '')),

            'MAIL_ENABLED' => $mailEnabled,
            'MAIL_HOST' => $mailHost,
            'MAIL_PORT' => $mailPort,
            'MAIL_USERNAME' => $mailUsername,
            'MAIL_PASSWORD' => $mailPassword,
            'MAIL_FROM_ADDRESS' => $mailFrom,
            'MAIL_FROM_NAME' => $mailFromName,

            'ST_BASE' => $stBase,
            'ST_AUTO_API' => $stAutoApiKey,
            'ST_ANON_API' => $stAnonApi,
            'ST_ANON_API_QUOTA' => $stAnonApiQuota,
            'ST_PSEUDOR_ENDING' => $stPseudorEnding,
            'ST_ADV_ANALYTICS' => $stAdvAnalytics,

            'TMP_SETUP_AUTH_KEY' => $setupAuthKey,
        ])->render();
        App::setLocale($locale);

        $envPath = base_path('.env');
        if (@file_put_contents($envPath, $compiledConfiguration) === false) {
            return [
                'success' => false,
                'error' => 'Could not write configuration to .env on disk. Check directory write permissions.',
            ];
        }

        try {
            Cache::flush();
        } catch (Throwable) {
        }

        return [
            'success' => true,
            'admin_username' => $acctUsername,
            'site_url' => $appProtocol.$appAddress,
            'theme' => $theme,
            'locale' => $locale,
        ];
    }

    public function performSetup(Request $request)
    {
        if (env('POLR_SETUP_RAN')) {
            return view('error', [
                'message' => __('polr.setup.already_completed'),
            ]);
        }

        $result = self::runInstallation($request->all(), (string) $request->ip());

        if (! $result['success']) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'],
                ], 422);
            }

            return redirect(route('setup'))->with('error', $result['error']);
        }

        $request->session()->put('username', $result['admin_username']);
        $request->session()->put('role', UserHelper::$USER_ROLES['admin']);
        $request->session()->put('theme', $result['theme']);
        $request->session()->put('locale', $result['locale']);
        $request->session()->put('setup_completed_info', $result);

        $cookie = cookie('polrx_locale', $result['locale'], 60 * 24 * 365);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('setup_finish'),
            ])->withCookie($cookie);
        }

        return redirect(route('setup_finish'))->withCookie($cookie);
    }

    public function finishSetup(Request $request)
    {
        $info = $request->session()->get('setup_completed_info');

        if (! $info && ! env('POLR_SETUP_RAN')) {
            return redirect(route('setup'));
        }

        return view('setup_thanks', [
            'admin_username' => $info['admin_username'] ?? session('username', 'admin'),
            'site_url' => $info['site_url'] ?? (env('APP_PROTOCOL', 'https://').env('APP_ADDRESS', 'localhost')),
        ]);
    }
}
