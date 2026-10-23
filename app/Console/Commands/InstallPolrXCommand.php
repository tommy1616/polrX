<?php

namespace App\Console\Commands;

use App\Helpers\LocaleHelper;
use App\Helpers\ThemeHelper;
use App\Http\Controllers\SetupController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class InstallPolrXCommand extends Command
{
    protected $signature = 'polrx:install
        {--lang= : Interface & default locale (zh_TW, en, ja, ko)}
        {--db-host=localhost : Database host}
        {--db-port=3306 : Database port}
        {--db-name=polr : Database name}
        {--db-user=root : Database username}
        {--db-pass= : Database password}
        {--app-name=polrX : Application name}
        {--app-protocol=https:// : Application protocol (https:// or http://)}
        {--app-address=localhost : Application domain (without protocol)}
        {--theme=midnight : Default theme (default, midnight, ocean, forest, rose, nord)}
        {--access-mode=public : Access mode (public, members, private)}
        {--admin-user=admin : Admin username}
        {--admin-email=admin@localhost : Admin email}
        {--admin-pass= : Admin password (min 6 chars)}
        {--force : Overwrite existing setup even if POLR_SETUP_RAN is true}';

    protected $description = 'Interactive setup wizard for polrX (supports zh_TW, en, ja, ko)';

    public function handle(): int
    {
        $this->newLine();
        $this->line('<fg=cyan;options=bold>========================================================</>');
        $this->line('<fg=cyan;options=bold>   polrX — Interactive Setup Wizard / 安裝導引精靈</>');
        $this->line('<fg=cyan;options=bold>========================================================</>');
        $this->newLine();

        // STEP 1: Language Selection FIRST
        $langOption = $this->option('lang');
        if ($langOption && LocaleHelper::isValid($langOption)) {
            $locale = $langOption;
        } elseif (! $this->input->isInteractive()) {
            $locale = LocaleHelper::DEFAULT_LOCALE;
        } else {
            $choice = $this->choice(
                '1. Please choose your language / 請選擇語言 / 言語を選択 / 언어를 선택하세요',
                [
                    'zh_TW' => '繁體中文（台灣） (Traditional Chinese)',
                    'en' => 'English (International)',
                    'ja' => '日本語 (Japanese)',
                    'ko' => '한국어 (Korean)',
                ],
                'zh_TW'
            );
            $locale = LocaleHelper::normalize($choice);
        }

        App::setLocale($locale);

        if (env('POLR_SETUP_RAN') && ! $this->option('force')) {
            $this->warn(__('polr.setup.already_completed'));
            if (! $this->input->isInteractive() || ! $this->confirm('Re-run setup anyway?', false)) {
                return self::FAILURE;
            }
        }

        // STEP 2: System Environment Checks
        $this->info('▶ '.__('polr.setup.step2.heading'));
        $checks = SetupController::getEnvironmentChecks();
        foreach ($checks['items'] as $item) {
            $status = $item['passed']
                ? '<fg=green>✓ '.__('polr.setup.step2.pass').'</>'
                : '<fg=red>✕ '.__('polr.setup.step2.fail').'</>';
            $this->line(sprintf('  - %s (%s): %s', $item['label'], $item['detail'], $status));
        }
        $this->newLine();

        if (! $checks['all_passed']) {
            $this->error(__('polr.setup.step2.fix_hint'));
            return self::FAILURE;
        }

        // STEP 3: Database Configuration
        $this->info('▶ '.__('polr.setup.step3.heading'));
        $interactive = $this->input->isInteractive() && ! $this->option('admin-pass');

        while (true) {
            $dbHost = $interactive
                ? $this->ask(__('polr.setup.step3.host'), (string) $this->option('db-host'))
                : (string) $this->option('db-host');
            $dbPort = (int) ($interactive
                ? $this->ask(__('polr.setup.step3.port'), (string) $this->option('db-port'))
                : $this->option('db-port'));
            $dbName = $interactive
                ? $this->ask(__('polr.setup.step3.name'), (string) $this->option('db-name'))
                : (string) $this->option('db-name');
            $dbUser = $interactive
                ? $this->ask(__('polr.setup.step3.username'), (string) $this->option('db-user'))
                : (string) $this->option('db-user');
            $dbPass = $interactive
                ? (string) ($this->secret(__('polr.setup.step3.password')) ?? '')
                : (string) $this->option('db-pass');

            $dbTest = SetupController::connectDatabase($dbHost, $dbPort, $dbName, $dbUser, $dbPass, true);
            if ($dbTest['success']) {
                $this->info('  ✓ '.__('polr.setup.step3.test_success', ['version' => $dbTest['version']]));
                break;
            }

            $this->error('  ✕ '.__('polr.setup.step3.test_failed', ['error' => $dbTest['error']]));
            if (! $interactive) {
                return self::FAILURE;
            }
        }
        $this->newLine();

        // STEP 4: Site & Appearance
        $this->info('▶ '.__('polr.setup.step4.heading'));
        $appName = $interactive
            ? $this->ask(__('polr.setup.step4.app_name'), (string) $this->option('app-name'))
            : (string) $this->option('app-name');
        $appProtocol = $interactive
            ? $this->choice(__('polr.setup.step4.app_protocol'), ['https://', 'http://'], (string) $this->option('app-protocol'))
            : (string) $this->option('app-protocol');
        $appAddress = $interactive
            ? $this->ask(__('polr.setup.step4.app_address'), (string) $this->option('app-address'))
            : (string) $this->option('app-address');

        $themeKeys = array_keys(ThemeHelper::all());
        $defaultTheme = $interactive
            ? $this->choice(__('polr.setup.step4.default_theme'), $themeKeys, (string) $this->option('theme'))
            : ThemeHelper::normalize((string) $this->option('theme'));

        $accessMode = $interactive
            ? $this->choice(__('polr.setup.step4.access_mode'), [
                'public' => __('polr.setup.step4.mode_public_title'),
                'members' => __('polr.setup.step4.mode_members_title'),
                'private' => __('polr.setup.step4.mode_private_title'),
            ], 'public')
            : (string) $this->option('access-mode');
        $this->newLine();

        // STEP 5: Admin Account
        $this->info('▶ '.__('polr.setup.step5.heading'));
        $adminUser = $interactive
            ? $this->ask(__('polr.setup.step5.admin_username'), (string) $this->option('admin-user'))
            : (string) $this->option('admin-user');
        $adminEmail = $interactive
            ? $this->ask(__('polr.setup.step5.admin_email'), (string) $this->option('admin-email'))
            : (string) $this->option('admin-email');

        $adminPass = (string) $this->option('admin-pass');
        while (strlen($adminPass) < 6) {
            if (! $this->input->isInteractive()) {
                $this->error(__('polr.setup.step5.validation_error'));
                return self::FAILURE;
            }
            $adminPass = (string) ($this->secret(__('polr.setup.step5.admin_password')) ?? '');
            if (strlen($adminPass) < 6) {
                $this->warn(__('polr.setup.step5.validation_error'));
            }
        }

        $this->line(__('polr.setup.nav.installing'));
        $result = SetupController::runInstallation([
            'app:locale' => $locale,
            'app:default_theme' => $defaultTheme,
            'db:host' => $dbHost,
            'db:port' => $dbPort,
            'db:name' => $dbName,
            'db:username' => $dbUser,
            'db:password' => $dbPass,
            'db:auto_create' => 'true',
            'app:name' => $appName,
            'app:protocol' => $appProtocol,
            'app:external_url' => $appAddress,
            'setting:access_mode' => $accessMode,
            'acct:username' => $adminUser,
            'acct:email' => $adminEmail,
            'acct:password' => $adminPass,
        ]);

        if (! $result['success']) {
            $this->error($result['error']);
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('🎉 '.__('polr.setup.thanks.title'));
        $this->line('  - '.__('polr.setup.thanks.site_url').': <fg=cyan>'.$result['site_url'].'</>');
        $this->line('  - '.__('polr.setup.thanks.admin_user').': <fg=cyan>'.$result['admin_username'].'</>');
        $this->newLine();

        return self::SUCCESS;
    }
}
