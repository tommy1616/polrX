@extends('layouts.minimal')

@section('title')
{{ __('polr.setup.title') }}
@endsection

@section('css')
<style>
.polrx-setup-wrapper {
    max-width: 860px;
    margin: 36px auto 64px;
}
.polrx-setup-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
}
.polrx-setup-brand {
    display: flex;
    align-items: center;
    gap: 14px;
}
.polrx-setup-brand img {
    width: 48px;
    height: 48px;
    border-radius: 12px;
}
.polrx-setup-brand h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: var(--polrx-text) !important;
}
.polrx-setup-brand p {
    margin: 4px 0 0;
    font-size: 13px;
    color: var(--polrx-text-muted) !important;
}
.polrx-setup-lang-pills {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.polrx-setup-lang-pill {
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid var(--polrx-border);
    background: var(--polrx-surface);
    color: var(--polrx-text-muted);
    cursor: pointer;
    transition: all 0.15s ease;
}
.polrx-setup-lang-pill:hover {
    color: var(--polrx-text);
    border-color: var(--polrx-primary);
}
.polrx-setup-lang-pill.is-active {
    background: var(--polrx-primary);
    color: #fff;
    border-color: var(--polrx-primary);
}
.polrx-stepper {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
    margin-bottom: 24px;
}
.polrx-step-tab {
    padding: 11px 10px;
    border-radius: 10px;
    background: var(--polrx-surface);
    border: 1px solid var(--polrx-border);
    color: var(--polrx-text-muted);
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    transition: all 0.18s ease;
    user-select: none;
}
.polrx-step-tab.is-active {
    background: var(--polrx-primary);
    border-color: var(--polrx-primary);
    color: #fff;
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.25);
}
.polrx-step-tab.is-done {
    border-color: var(--polrx-primary);
    color: var(--polrx-text);
}
.polrx-setup-card {
    background: var(--polrx-surface);
    border: 1px solid var(--polrx-border);
    border-radius: 16px;
    padding: 32px;
    box-shadow: var(--polrx-shadow);
}
.polrx-step-pane {
    display: none;
}
.polrx-step-pane.is-active {
    display: block;
}
.polrx-step-pane h2 {
    margin-top: 0;
    margin-bottom: 8px;
    font-size: 21px;
    font-weight: 700;
    color: var(--polrx-text) !important;
}
.polrx-step-desc {
    color: var(--polrx-text-muted) !important;
    font-size: 14px;
    margin-bottom: 24px;
}
.polrx-check-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}
.polrx-check-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border-radius: 10px;
    background: var(--polrx-surface-alt);
    border: 1px solid var(--polrx-border);
}
.polrx-check-item.is-fail {
    border-color: #ef4444;
    background: rgba(239, 68, 68, 0.08);
}
.polrx-check-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}
.polrx-check-badge.pass {
    background: rgba(16, 185, 129, 0.16);
    color: #10b981;
}
.polrx-check-badge.fail {
    background: rgba(239, 68, 68, 0.18);
    color: #ef4444;
}
.polrx-mode-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
    margin-top: 8px;
    margin-bottom: 20px;
}
.polrx-mode-card {
    padding: 14px 16px;
    border-radius: 12px;
    background: var(--polrx-surface-alt);
    border: 2px solid var(--polrx-border);
    cursor: pointer;
    transition: all 0.15s ease;
}
.polrx-mode-card:hover {
    border-color: var(--polrx-primary);
}
.polrx-mode-card.is-active {
    border-color: var(--polrx-primary);
    box-shadow: 0 0 0 3px var(--polrx-ring);
}
.polrx-mode-card h4 {
    margin: 0 0 6px;
    font-size: 14px;
    font-weight: 700;
    color: var(--polrx-text) !important;
}
.polrx-mode-card p {
    margin: 0;
    font-size: 12px;
    color: var(--polrx-text-muted) !important;
}
.polrx-setup-footer-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid var(--polrx-border);
}
.polrx-alert-box {
    padding: 12px 16px;
    border-radius: 10px;
    margin-top: 14px;
    font-size: 13px;
    font-weight: 600;
    display: none;
}
.polrx-alert-box.success {
    display: block;
    background: rgba(16, 185, 129, 0.15);
    border: 1px solid #10b981;
    color: #10b981;
}
.polrx-alert-box.error {
    display: block;
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid #ef4444;
    color: #ef4444;
}
.polrx-summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    padding: 16px;
    border-radius: 12px;
    background: var(--polrx-surface-alt);
    border: 1px solid var(--polrx-border);
    margin-top: 18px;
}
.polrx-summary-item span {
    display: block;
    font-size: 11px;
    color: var(--polrx-text-muted);
}
.polrx-summary-item strong {
    font-size: 14px;
    color: var(--polrx-text);
}
@media (max-width: 767px) {
    .polrx-stepper {
        grid-template-columns: 1fr;
    }
    .polrx-summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<div class="polrx-setup-wrapper">
    <div class="polrx-setup-header">
        <div class="polrx-setup-brand">
            <img src="/favicon.svg" alt="polrX Logo" />
            <div>
                <h1>polrX — {{ __('polr.setup.title') }}</h1>
                <p>{{ __('polr.setup.subtitle') }}</p>
            </div>
        </div>
        <div class="polrx-setup-lang-pills">
            @foreach ($available_locales as $code => $meta)
                <button type="button"
                    class="polrx-setup-lang-pill {{ $user_locale === $code ? 'is-active' : '' }}"
                    onclick="switchSetupLocale('{{ $code }}', false)">
                    {{ $meta['badge'] }} · {{ $meta['name'] }}
                </button>
            @endforeach
        </div>
    </div>

    @if (session('error'))
        <div class="polrx-alert-box error" style="display:block; margin-bottom:18px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="polrx-stepper">
        <div class="polrx-step-tab is-active" data-step-tab="1" onclick="goToStep(1)">{{ __('polr.setup.steps.lang') }}</div>
        <div class="polrx-step-tab" data-step-tab="2" onclick="goToStep(2)">{{ __('polr.setup.steps.check') }}</div>
        <div class="polrx-step-tab" data-step-tab="3" onclick="goToStep(3)">{{ __('polr.setup.steps.db') }}</div>
        <div class="polrx-step-tab" data-step-tab="4" onclick="goToStep(4)">{{ __('polr.setup.steps.site') }}</div>
        <div class="polrx-step-tab" data-step-tab="5" onclick="goToStep(5)">{{ __('polr.setup.steps.admin') }}</div>
    </div>

    <form id="polrx-setup-form" method="POST" action="{{ route('psetup') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" name="app:locale" id="setup-locale-input" value="{{ $user_locale }}" />
        <input type="hidden" name="app:default_theme" id="setup-theme-input" value="{{ $user_theme !== 'default' ? $user_theme : 'midnight' }}" />
        <input type="hidden" name="setting:access_mode" id="setup-access-mode-input" value="public" />

        <div class="polrx-setup-card">
            {{-- STEP 1: LANGUAGE SELECTION (FIRST PRIORITY) --}}
            <div class="polrx-step-pane is-active" data-step-pane="1">
                <h2>{{ __('polr.setup.step1.heading') }}</h2>
                <p class="polrx-step-desc">{{ __('polr.setup.step1.desc') }}</p>

                <div class="polrx-lang-grid">
                    @foreach ($available_locales as $code => $meta)
                        <div class="polrx-lang-card {{ $user_locale === $code ? 'is-active' : '' }}"
                             onclick="switchSetupLocale('{{ $code }}', true)"
                             role="button"
                             tabindex="0">
                            <div class="polrx-lang-card-top">
                                <span class="polrx-lang-badge">{{ $meta['badge'] }}</span>
                                @if ($user_locale === $code)
                                    <span class="polrx-theme-active-pill">{{ __('polr.admin.language.active_badge') }}</span>
                                @endif
                            </div>
                            <div class="polrx-lang-native">{{ $meta['name'] }}</div>
                            <div class="polrx-lang-sub">{{ $meta['subtitle'] }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="polrx-setup-footer-nav">
                    <div></div>
                    <button type="button" class="btn btn-primary" onclick="goToStep(2)">
                        {{ __('polr.setup.nav.next') }} &rarr;
                    </button>
                </div>
            </div>

            {{-- STEP 2: ENVIRONMENT CHECK --}}
            <div class="polrx-step-pane" data-step-pane="2">
                <h2>{{ __('polr.setup.step2.heading') }}</h2>
                <p class="polrx-step-desc">{{ __('polr.setup.step2.desc') }}</p>

                <div class="polrx-check-list" id="setup-check-list">
                    @foreach ($checks as $item)
                        <div class="polrx-check-item {{ $item['passed'] ? '' : 'is-fail' }}">
                            <div>
                                <strong style="color: var(--polrx-text);">{{ $item['label'] }}</strong>
                                <span style="color: var(--polrx-text-muted); font-size: 12px; margin-left: 8px;">({{ $item['detail'] }})</span>
                            </div>
                            <span class="polrx-check-badge {{ $item['passed'] ? 'pass' : 'fail' }}">
                                {{ $item['passed'] ? '✓ '.__('polr.setup.step2.pass') : '✕ '.__('polr.setup.step2.fail') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                @if (! $all_checks_passed)
                    <div class="polrx-alert-box error" style="display:block;" id="setup-check-hint">
                        {{ __('polr.setup.step2.fix_hint') }}
                    </div>
                @endif

                <div class="polrx-setup-footer-nav">
                    <button type="button" class="btn btn-default" onclick="goToStep(1)">
                        &larr; {{ __('polr.setup.nav.prev') }}
                    </button>
                    <div style="display:flex; gap:10px;">
                        <button type="button" class="btn btn-default" onclick="recheckEnvironment()">
                            <i class="fa fa-refresh"></i> {{ __('polr.setup.nav.recheck') }}
                        </button>
                        <button type="button" class="btn btn-primary" id="step2-next-btn" onclick="goToStep(3)">
                            {{ __('polr.setup.nav.next') }} &rarr;
                        </button>
                    </div>
                </div>
            </div>

            {{-- STEP 3: DATABASE CONFIGURATION --}}
            <div class="polrx-step-pane" data-step-pane="3">
                <h2>{{ __('polr.setup.step3.heading') }}</h2>
                <p class="polrx-step-desc">{{ __('polr.setup.step3.desc') }}</p>

                <div class="row">
                    <div class="col-sm-8 form-group">
                        <label>{{ __('polr.setup.step3.host') }}</label>
                        <input type="text" class="form-control" name="db:host" id="db-host" value="{{ env('DB_HOST', 'localhost') }}" required />
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>{{ __('polr.setup.step3.port') }}</label>
                        <input type="number" class="form-control" name="db:port" id="db-port" value="{{ env('DB_PORT', '3306') }}" required />
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label>{{ __('polr.setup.step3.name') }}</label>
                        <input type="text" class="form-control" name="db:name" id="db-name" value="{{ env('DB_DATABASE', 'polr') }}" required />
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>{{ __('polr.setup.step3.username') }}</label>
                        <input type="text" class="form-control" name="db:username" id="db-user" value="{{ env('DB_USERNAME', 'root') }}" required />
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>{{ __('polr.setup.step3.password') }}</label>
                        <input type="password" class="form-control" name="db:password" id="db-pass" value="" />
                    </div>
                </div>

                <div class="checkbox" style="margin-top: 6px;">
                    <label style="color: var(--polrx-text-muted);">
                        <input type="checkbox" name="db:auto_create" id="db-auto-create" value="true" checked />
                        {{ __('polr.setup.step3.auto_create_db') }}
                    </label>
                </div>

                <div style="margin-top: 14px;">
                    <button type="button" class="btn btn-info" id="test-db-btn" onclick="testDatabase()">
                        <i class="fa fa-plug"></i> <span id="test-db-btn-label">{{ __('polr.setup.nav.test_db') }}</span>
                    </button>
                </div>

                <div id="db-test-feedback" class="polrx-alert-box"></div>

                <div class="polrx-setup-footer-nav">
                    <button type="button" class="btn btn-default" onclick="goToStep(2)">
                        &larr; {{ __('polr.setup.nav.prev') }}
                    </button>
                    <button type="button" class="btn btn-primary" onclick="goToStep(4)">
                        {{ __('polr.setup.nav.next') }} &rarr;
                    </button>
                </div>
            </div>

            {{-- STEP 4: SITE BRANDING & THEME --}}
            <div class="polrx-step-pane" data-step-pane="4">
                <h2>{{ __('polr.setup.step4.heading') }}</h2>
                <p class="polrx-step-desc">{{ __('polr.setup.step4.desc') }}</p>

                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label>{{ __('polr.setup.step4.app_name') }}</label>
                        <input type="text" class="form-control" name="app:name" id="app-name" value="polrX" required />
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>{{ __('polr.setup.step4.app_protocol') }}</label>
                        <select class="form-control" name="app:protocol" id="app-protocol">
                            <option value="https://" {{ $detected_protocol === 'https://' ? 'selected' : '' }}>https://</option>
                            <option value="http://" {{ $detected_protocol === 'http://' ? 'selected' : '' }}>http://</option>
                        </select>
                    </div>
                    <div class="col-sm-5 form-group">
                        <label>{{ __('polr.setup.step4.app_address') }}</label>
                        <input type="text" class="form-control" name="app:external_url" id="app-address" value="{{ $detected_host }}" required />
                    </div>
                </div>

                <label style="margin-top: 10px;">{{ __('polr.setup.step4.default_theme') }}</label>
                <div class="polrx-theme-grid" style="margin-top: 8px; margin-bottom: 20px;">
                    @foreach ($available_themes as $key => $theme)
                        <div class="polrx-theme-card {{ ($user_theme !== 'default' ? $user_theme : 'midnight') === $key ? 'is-active' : '' }}"
                             data-setup-theme="{{ $key }}"
                             onclick="selectSetupTheme('{{ $key }}')"
                             role="button"
                             tabindex="0">
                            <div class="polrx-theme-swatches">
                                @foreach ($theme['swatches'] as $color)
                                    <span class="polrx-theme-swatch" style="background: {{ $color }}"></span>
                                @endforeach
                            </div>
                            <div class="polrx-theme-meta">
                                <div class="polrx-theme-title-row">
                                    <strong>{{ $theme['name'] }}</strong>
                                </div>
                                <p>{{ $theme['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <label>{{ __('polr.setup.step4.access_mode') }}</label>
                <div class="polrx-mode-grid">
                    <div class="polrx-mode-card is-active" data-access-mode="public" onclick="selectAccessMode('public')">
                        <h4>{{ __('polr.setup.step4.mode_public_title') }}</h4>
                        <p>{{ __('polr.setup.step4.mode_public_desc') }}</p>
                    </div>
                    <div class="polrx-mode-card" data-access-mode="members" onclick="selectAccessMode('members')">
                        <h4>{{ __('polr.setup.step4.mode_members_title') }}</h4>
                        <p>{{ __('polr.setup.step4.mode_members_desc') }}</p>
                    </div>
                    <div class="polrx-mode-card" data-access-mode="private" onclick="selectAccessMode('private')">
                        <h4>{{ __('polr.setup.step4.mode_private_title') }}</h4>
                        <p>{{ __('polr.setup.step4.mode_private_desc') }}</p>
                    </div>
                </div>

                <div style="margin-top: 10px;">
                    <a href="#setup-advanced-collapse" data-toggle="collapse" style="font-size: 13px; font-weight: 600;">
                        <i class="fa fa-sliders"></i> {{ __('polr.setup.step4.advanced_toggle') }}
                    </a>
                    <div id="setup-advanced-collapse" class="collapse" style="margin-top: 16px; padding: 18px; border-radius: 12px; background: var(--polrx-surface-alt); border: 1px solid var(--polrx-border);">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>{{ __('polr.setup.step4.base_label') }}</label>
                                <select name="setting:base" class="form-control">
                                    <option value="32" selected>{{ __('polr.setup.step4.base_32') }}</option>
                                    <option value="62">{{ __('polr.setup.step4.base_62') }}</option>
                                </select>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>{{ __('polr.setup.step4.ending_type') }}</label>
                                <select name="setting:pseudor_ending" class="form-control">
                                    <option value="false" selected>{{ __('polr.setup.step4.ending_counter') }}</option>
                                    <option value="true">{{ __('polr.setup.step4.ending_pseudo') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>{{ __('polr.setup.step4.registration') }}</label>
                                <select name="setting:registration_permission" class="form-control">
                                    <option value="none" selected>{{ __('polr.setup.step4.reg_none') }}</option>
                                    <option value="no-verification">{{ __('polr.setup.step4.reg_no_verify') }}</option>
                                    <option value="email">{{ __('polr.setup.step4.reg_email') }}</option>
                                </select>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>{{ __('polr.setup.step4.adv_analytics') }}</label>
                                <select name="setting:adv_analytics" class="form-control">
                                    <option value="true" selected>{{ __('polr.setup.step4.adv_analytics_on') }}</option>
                                    <option value="false">{{ __('polr.setup.step4.adv_analytics_off') }}</option>
                                </select>
                            </div>
                        </div>

                        <hr style="border-color: var(--polrx-border);" />
                        <p style="font-weight: 600; font-size: 13px;">{{ __('polr.setup.step4.smtp_section') }}</p>
                        <div class="row">
                            <div class="col-sm-8 form-group">
                                <label>{{ __('polr.setup.step4.smtp_host') }}</label>
                                <input type="text" class="form-control" name="app:smtp_server" placeholder="smtp.example.com" />
                            </div>
                            <div class="col-sm-4 form-group">
                                <label>{{ __('polr.setup.step4.smtp_port') }}</label>
                                <input type="text" class="form-control" name="app:smtp_port" value="587" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>{{ __('polr.setup.step4.smtp_user') }}</label>
                                <input type="text" class="form-control" name="app:smtp_username" />
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>{{ __('polr.setup.step4.smtp_pass') }}</label>
                                <input type="password" class="form-control" name="app:smtp_password" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>{{ __('polr.setup.step4.smtp_from') }}</label>
                                <input type="email" class="form-control" name="app:smtp_from" placeholder="noreply@example.com" />
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>{{ __('polr.setup.step4.smtp_from_name') }}</label>
                                <input type="text" class="form-control" name="app:smtp_from_name" placeholder="polrX" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="polrx-setup-footer-nav">
                    <button type="button" class="btn btn-default" onclick="goToStep(3)">
                        &larr; {{ __('polr.setup.nav.prev') }}
                    </button>
                    <button type="button" class="btn btn-primary" onclick="goToStep(5)">
                        {{ __('polr.setup.nav.next') }} &rarr;
                    </button>
                </div>
            </div>

            {{-- STEP 5: ADMIN ACCOUNT & INSTALL --}}
            <div class="polrx-step-pane" data-step-pane="5">
                <h2>{{ __('polr.setup.step5.heading') }}</h2>
                <p class="polrx-step-desc">{{ __('polr.setup.step5.desc') }}</p>

                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label>{{ __('polr.setup.step5.admin_username') }}</label>
                        <input type="text" class="form-control" name="acct:username" id="admin-user" value="admin" required />
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>{{ __('polr.setup.step5.admin_email') }}</label>
                        <input type="email" class="form-control" name="acct:email" id="admin-email" value="admin@{{ $detected_host }}" required />
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>{{ __('polr.setup.step5.admin_password') }}</label>
                        <input type="password" class="form-control" name="acct:password" id="admin-pass" minlength="6" required />
                    </div>
                </div>

                <h4 style="margin-top: 20px; font-size: 14px; font-weight: 700;">{{ __('polr.setup.step5.summary_title') }}</h4>
                <div class="polrx-summary-grid">
                    <div class="polrx-summary-item">
                        <span>{{ __('polr.setup.steps.lang') }}</span>
                        <strong id="sum-lang">{{ $current_locale_meta['name'] ?? $user_locale }}</strong>
                    </div>
                    <div class="polrx-summary-item">
                        <span>{{ __('polr.setup.steps.db') }}</span>
                        <strong id="sum-db">localhost:3306 / polr</strong>
                    </div>
                    <div class="polrx-summary-item">
                        <span>{{ __('polr.setup.steps.site') }}</span>
                        <strong id="sum-site">{{ $detected_protocol }}{{ $detected_host }}</strong>
                    </div>
                    <div class="polrx-summary-item">
                        <span>{{ __('polr.setup.step4.default_theme') }}</span>
                        <strong id="sum-theme">{{ $user_theme !== 'default' ? $user_theme : 'midnight' }}</strong>
                    </div>
                </div>

                <div id="install-feedback" class="polrx-alert-box"></div>

                <div class="polrx-setup-footer-nav">
                    <button type="button" class="btn btn-default" onclick="goToStep(4)">
                        &larr; {{ __('polr.setup.nav.prev') }}
                    </button>
                    <button type="submit" class="btn btn-success btn-lg" id="install-submit-btn">
                        <i class="fa fa-rocket"></i> <span id="install-btn-label">{{ __('polr.setup.nav.install') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
var currentStep = 1;
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function goToStep(step) {
    currentStep = step;
    document.querySelectorAll('[data-step-pane]').forEach(function (el) {
        el.classList.toggle('is-active', parseInt(el.getAttribute('data-step-pane'), 10) === step);
    });
    document.querySelectorAll('[data-step-tab]').forEach(function (el) {
        var tabStep = parseInt(el.getAttribute('data-step-tab'), 10);
        el.classList.toggle('is-active', tabStep === step);
        el.classList.toggle('is-done', tabStep < step);
    });
    if (step === 5) {
        updateInstallSummary();
    }
    window.location.hash = 'step' + step;
}

function switchSetupLocale(locale, advanceToStep2) {
    document.getElementById('setup-locale-input').value = locale;
    fetch('/locale', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ locale: locale })
    }).then(function () {
        var targetHash = advanceToStep2 ? '#step2' : ('#step' + currentStep);
        window.location.href = '/setup?lang=' + encodeURIComponent(locale) + targetHash;
    }).catch(function () {
        window.location.href = '/setup?lang=' + encodeURIComponent(locale);
    });
}

function recheckEnvironment() {
    fetch('/setup/check', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    }).then(function (r) { return r.json(); }).then(function (data) {
        if (data && data.items) {
            window.location.reload();
        }
    });
}

function testDatabase() {
    var btn = document.getElementById('test-db-btn');
    var label = document.getElementById('test-db-btn-label');
    var feedback = document.getElementById('db-test-feedback');
    btn.disabled = true;
    label.textContent = @json(__('polr.setup.nav.testing_db'));
    feedback.className = 'polrx-alert-box';
    feedback.style.display = 'none';

    var payload = {
        'db:host': document.getElementById('db-host').value,
        'db:port': document.getElementById('db-port').value,
        'db:name': document.getElementById('db-name').value,
        'db:username': document.getElementById('db-user').value,
        'db:password': document.getElementById('db-pass').value,
        'db:auto_create': document.getElementById('db-auto-create').checked ? 'true' : 'false'
    };

    fetch('/setup/test-db', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
    }).then(function (r) {
        return r.json().then(function (body) { return { ok: r.ok, body: body }; });
    }).then(function (res) {
        btn.disabled = false;
        label.textContent = @json(__('polr.setup.nav.test_db'));
        feedback.textContent = res.body.message || '';
        feedback.className = 'polrx-alert-box ' + (res.body.success ? 'success' : 'error');
        feedback.style.display = 'block';
    }).catch(function (err) {
        btn.disabled = false;
        label.textContent = @json(__('polr.setup.nav.test_db'));
        feedback.textContent = err.message || 'Connection test failed';
        feedback.className = 'polrx-alert-box error';
        feedback.style.display = 'block';
    });
}

function selectSetupTheme(themeKey) {
    document.documentElement.setAttribute('data-theme', themeKey);
    document.getElementById('setup-theme-input').value = themeKey;
    document.querySelectorAll('[data-setup-theme]').forEach(function (card) {
        card.classList.toggle('is-active', card.getAttribute('data-setup-theme') === themeKey);
    });
}

function selectAccessMode(mode) {
    document.getElementById('setup-access-mode-input').value = mode;
    document.querySelectorAll('[data-access-mode]').forEach(function (card) {
        card.classList.toggle('is-active', card.getAttribute('data-access-mode') === mode);
    });
}

function updateInstallSummary() {
    document.getElementById('sum-db').textContent =
        document.getElementById('db-host').value + ':' +
        document.getElementById('db-port').value + ' / ' +
        document.getElementById('db-name').value;
    document.getElementById('sum-site').textContent =
        document.getElementById('app-protocol').value +
        document.getElementById('app-address').value +
        ' (' + document.getElementById('app-name').value + ')';
    document.getElementById('sum-theme').textContent =
        document.getElementById('setup-theme-input').value;
}

document.getElementById('polrx-setup-form').addEventListener('submit', function (e) {
    e.preventDefault();
    var form = e.target;
    var btn = document.getElementById('install-submit-btn');
    var label = document.getElementById('install-btn-label');
    var feedback = document.getElementById('install-feedback');

    btn.disabled = true;
    label.textContent = @json(__('polr.setup.nav.installing'));
    feedback.className = 'polrx-alert-box';
    feedback.style.display = 'none';

    var formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    }).then(function (r) {
        return r.json().then(function (body) { return { ok: r.ok, body: body }; });
    }).then(function (res) {
        if (res.ok && res.body.success && res.body.redirect) {
            window.location.href = res.body.redirect;
            return;
        }
        btn.disabled = false;
        label.textContent = @json(__('polr.setup.nav.install'));
        feedback.textContent = (res.body && res.body.message) ? res.body.message : 'Installation failed.';
        feedback.className = 'polrx-alert-box error';
        feedback.style.display = 'block';
    }).catch(function (err) {
        btn.disabled = false;
        label.textContent = @json(__('polr.setup.nav.install'));
        feedback.textContent = err.message || 'Installation failed.';
        feedback.className = 'polrx-alert-box error';
        feedback.style.display = 'block';
    });
});

(function () {
    var m = (window.location.hash || '').match(/^#step([1-5])$/);
    if (m) {
        goToStep(parseInt(m[1], 10));
    }
})();
</script>
@endsection
