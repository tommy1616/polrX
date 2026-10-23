@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/admin.css'>
<link rel='stylesheet' href='/css/datatables.min.css'>
@endsection

@section('content')
<div ng-controller="AdminCtrl" class="ng-root">
    <div class="row admin-layout-row">
    <div class='col-md-2'>
        <ul class='nav nav-pills nav-stacked admin-nav' role='tablist'>
            <li role='presentation' aria-controls="home" class='admin-nav-item active'><a href='#home'>{{ __('polr.admin.nav.home') }}</a></li>
            <li role='presentation' aria-controls="links" class='admin-nav-item'><a href='#links'>{{ __('polr.admin.nav.links') }}</a></li>
            <li role='presentation' aria-controls="settings" class='admin-nav-item'><a href='#settings'>{{ __('polr.admin.nav.settings') }}</a></li>
            <li role='presentation' aria-controls="totp" class='admin-nav-item'><a href='#totp'>{{ __('polr.admin.nav.totp') }}</a></li>
            <li role='presentation' aria-controls="language" class='admin-nav-item'><a href='#language'>{{ __('polr.admin.nav.language') }}</a></li>

            @if ($role == $admin_role)
            <li role='presentation' class='admin-nav-item'><a href='#admin'>{{ __('polr.admin.nav.admin') }}</a></li>
            @endif

            @if ($api_active == 1)
            <li role='presentation' class='admin-nav-item'><a href='#developer'>{{ __('polr.admin.nav.developer') }}</a></li>
            @endif
        </ul>
    </div>
    <div class='col-md-10'>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <h2>{{ __('polr.admin.home.welcome', ['app' => env('APP_NAME')]) }}</h2>
                <p class="polrx-muted">{{ __('polr.admin.home.desc') }}</p>
            </div>

            <div role="tabpanel" class="tab-pane" id="links">
                <h3>{{ __('polr.admin.links.my_links') }}</h3>
                @include('snippets.link_table', [
                    'table_id' => 'user_links_table'
                ])
            </div>

            <div role="tabpanel" class="tab-pane" id="settings">
                <h3>{{ __('polr.admin.settings.change_password') }}</h3>
                <form action='/admin/action/change_password' method='POST' class="polrx-form-narrow">
                    <label class="polrx-field-label">{{ __('polr.admin.settings.current_password') }}</label>
                    <input class="form-control password-box" type='password' name='current_password' />
                    <label class="polrx-field-label">{{ __('polr.admin.settings.new_password') }}</label>
                    <input class="form-control password-box" type='password' name='new_password' />
                    <input type="hidden" name='_token' value='{{csrf_token()}}' />
                    <input type='submit' class='btn btn-primary change-password-btn' value='{{ __('polr.admin.settings.update_password_btn') }}'/>
                </form>

                <hr class="polrx-divider">

                <div class="theme-selector">
                    <h3>{{ __('polr.admin.settings.theme_title') }}</h3>
                    <p class="polrx-muted" style="margin-bottom: 14px;">{{ __('polr.admin.settings.theme_desc') }}</p>
                    <div class="polrx-theme-grid">
                        @foreach ($available_themes as $themeKey => $themeMeta)
                        <div class="polrx-theme-card {{ ($user_theme === $themeKey) ? 'is-active' : '' }}"
                             data-theme="{{ $themeKey }}"
                             role="button"
                             tabindex="0"
                             aria-pressed="{{ ($user_theme === $themeKey) ? 'true' : 'false' }}">
                            <div class="polrx-theme-swatches">
                                @foreach ($themeMeta['swatches'] as $hex)
                                <span class="polrx-theme-swatch" style="background-color: {{ $hex }};"></span>
                                @endforeach
                            </div>
                            <div class="polrx-theme-title-row">
                                <span class="polrx-theme-title">{{ $themeMeta['name'] }}</span>
                                <span class="polrx-theme-badge">{{ __('polr.admin.settings.active_badge') }}</span>
                            </div>
                            <div class="polrx-theme-desc">{{ $themeMeta['description'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="totp">
                <h3>{{ __('polr.admin.totp.title') }}</h3>
                <p class="polrx-muted" style="margin-bottom: 18px;">{{ __('polr.admin.totp.subtitle') }}</p>

                @if ($totp_enabled)
                    <div class="well polrx-subcard">
                        <p style="margin-bottom: 8px;"><strong>{{ __('polr.admin.totp.status_label') }}</strong> <span class="polrx-status-pill">{{ __('polr.admin.totp.enabled_badge') }}</span></p>
                        <p class="polrx-muted" style="margin-bottom: 18px;">{{ __('polr.admin.totp.enabled_desc') }}</p>
                        <form action='/admin/action/totp/disable' method='POST' class="polrx-form-narrow">
                            <label class="polrx-field-label">{{ __('polr.admin.settings.current_password') }}</label>
                            <input class="form-control password-box" type='password' name='disable_totp_current_password' />
                            <input type="hidden" name='_token' value='{{csrf_token()}}' />
                            <input type='submit' class='btn btn-danger' style="margin-top: 14px;" value='{{ __('polr.admin.totp.disable_btn') }}'/>
                        </form>
                    </div>
                @elseif ($pending_totp_secret)
                    <div class="well polrx-subcard">
                        <p style="margin-bottom: 14px;">{{ __('polr.admin.totp.scan_prompt') }}</p>
                        <div style="display:flex; flex-wrap:wrap; gap: 24px; align-items:flex-start; margin-bottom: 16px;">
                            <div>
                                <div id="totp-qrcode" class="polrx-qr-box"></div>
                            </div>
                            <div style="flex:1; min-width: 260px;">
                                <p style="margin-bottom: 10px;"><strong>{{ __('polr.admin.totp.account_label') }}</strong> {{$totp_label}}</p>
                                <p style="margin-bottom: 10px;"><strong>{{ __('polr.admin.totp.manual_secret') }}</strong></p>
                                <code class="polrx-code-block">{{$pending_totp_secret}}</code>
                            </div>
                        </div>

                        <form action='/admin/action/totp/confirm' method='POST' class="polrx-form-narrow">
                            <label class="polrx-field-label">{{ __('polr.admin.settings.current_password') }}</label>
                            <input class="form-control password-box" type='password' name='totp_current_password' />
                            <label class="polrx-field-label" style="margin-top: 12px;">{{ __('polr.admin.totp.code_label') }}</label>
                            <input class="form-control password-box" type='text' inputmode='numeric' maxlength='6' name='totp_code' placeholder='123456' />
                            <input type="hidden" name='_token' value='{{csrf_token()}}' />
                            <div style="margin-top: 14px; display:flex; gap:10px; flex-wrap:wrap;">
                                <input type='submit' class='btn btn-primary' value='{{ __('polr.admin.totp.confirm_btn') }}'/>
                            </div>
                        </form>

                        <form action='/admin/action/totp/cancel' method='POST' style="margin-top: 10px;">
                            <input type="hidden" name='_token' value='{{csrf_token()}}' />
                            <input type='submit' class='btn btn-default' value='{{ __('polr.admin.totp.cancel_btn') }}'/>
                        </form>
                    </div>
                @else
                    <div class="well polrx-subcard">
                        <p style="margin-bottom: 8px;"><strong>{{ __('polr.admin.totp.status_label') }}</strong> <span class="polrx-status-pill is-muted">{{ __('polr.admin.totp.disabled_badge') }}</span></p>
                        <p class="polrx-muted" style="margin-bottom: 16px;">{{ __('polr.admin.totp.disabled_desc') }}</p>
                        <form action='/admin/action/totp/start' method='POST'>
                            <input type="hidden" name='_token' value='{{csrf_token()}}' />
                            <input type='submit' class='btn btn-primary' value='{{ __('polr.admin.totp.start_btn') }}'/>
                        </form>
                    </div>
                @endif
            </div>

            <div role="tabpanel" class="tab-pane" id="language">
                <h3>{{ __('polr.admin.language.title') }}</h3>
                <p class="polrx-muted" style="margin-bottom: 16px;">{{ __('polr.admin.language.desc') }}</p>
                <div class="polrx-theme-grid">
                    @foreach ($available_locales as $locCode => $locMeta)
                    <div class="polrx-theme-card polrx-locale-card {{ ($user_locale === $locCode) ? 'is-active' : '' }}"
                         data-locale="{{ $locCode }}"
                         role="button"
                         tabindex="0"
                         aria-pressed="{{ ($user_locale === $locCode) ? 'true' : 'false' }}">
                        <div class="polrx-locale-header">
                            <span class="polrx-locale-code-pill">{{ $locMeta['badge'] }}</span>
                            <span class="polrx-theme-badge">{{ __('polr.admin.language.active_badge') }}</span>
                        </div>
                        <div class="polrx-theme-title-row" style="margin-top: 10px;">
                            <span class="polrx-theme-title">{{ $locMeta['name'] }}</span>
                        </div>
                        <div class="polrx-theme-desc">{{ $locMeta['subtitle'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if ($role == $admin_role)
            <div role="tabpanel" class="tab-pane" id="admin">
                <h3>{{ __('polr.admin.links.all_links') }}</h3>
                @include('snippets.link_table', [
                    'table_id' => 'admin_links_table'
                ])

                <h3 class="users-heading">{{ __('polr.admin.users.heading') }}</h3>
                <a ng-click="state.showNewUserWell = !state.showNewUserWell" class="btn btn-primary btn-sm status-display">{{ __('polr.admin.users.add_btn') }}</a>

                <div ng-if="state.showNewUserWell" class="new-user-fields well">
                    <table class="table">
                        <tr>
                            <th>{{ __('polr.admin.users.username') }}</th>
                            <th>{{ __('polr.admin.users.password') }}</th>
                            <th>{{ __('polr.admin.users.email') }}</th>
                            <th>{{ __('polr.admin.users.role') }}</th>
                            <th></th>
                        </tr>
                        <tr id="new-user-form">
                            <td><input type="text" class="form-control" ng-model="newUserParams.username"></td>
                            <td><input type="password" class="form-control" ng-model="newUserParams.userPassword"></td>
                            <td><input type="email" class="form-control" ng-model="newUserParams.userEmail"></td>
                            <td>
                                <select class="form-control new-user-role" ng-model="newUserParams.userRole">
                                    @foreach  ($user_roles as $role_text => $role_val)
                                        <option value="{{$role_val}}">{{$role_text}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <a ng-click="addNewUser($event)" class="btn btn-primary btn-sm status-display new-user-add">{{ __('polr.admin.users.add_btn') }}</a>
                            </td>
                        </tr>
                    </table>
                </div>

                @include('snippets.user_table', [
                    'table_id' => 'admin_users_table'
                ])

            </div>
            @endif

            @if ($api_active == 1)
            <div role="tabpanel" class="tab-pane" id="developer">
                <h3>{{ __('polr.admin.developer.title') }}</h3>

                <p>{{ __('polr.admin.developer.desc') }}</p>
                <p>
                    {{ __('polr.admin.developer.docs_label') }}
                    <a href='https://github.com/tommy1616/polrX' target='_blank' rel='noopener noreferrer'>https://github.com/tommy1616/polrX</a>
                </p>

                <h4>{{ __('polr.admin.developer.api_key_label') }}</h4>
                <div class='row'>
                    <div class='col-md-8'>
                        <input class='form-control status-display' disabled type='text' value='{{$api_key}}'>
                    </div>
                    <div class='col-md-4'>
                        <a href='#' ng-click="generateNewAPIKey($event, '{{$user_id}}', true)" id='api-reset-key' class='btn btn-danger'>{{ __('polr.admin.developer.reset_btn') }}</a>
                    </div>
                </div>

                <h4>{{ __('polr.admin.developer.api_quota_label') }}</h4>
                <h2 class='api-quota'>
                    @if ($api_quota == -1)
                        {{ __('polr.admin.developer.unlimited') }}
                    @else
                        <code>{{$api_quota}}</code>
                    @endif
                </h2>
                <span>{{ __('polr.admin.developer.requests_per_min') }}</span>
            </div>
            @endif
        </div>
    </div>
    </div>

    <div class="angular-modals">
        <edit-long-link-modal ng-repeat="modal in modals.editLongLink" link-ending="modal.linkEnding"
            old-long-link="modal.oldLongLink" clean-modals="cleanModals"></edit-long-link-modal>
        <edit-user-api-info-modal ng-repeat="modal in modals.editUserApiInfo" user-id="modal.userId"
            api-quota="modal.apiQuota" api-active="modal.apiActive" api-key="modal.apiKey"
            generate-new-api-key="generateNewAPIKey" clean-modals="cleanModals"></edit-user-api-info>
    </div>
</div>
@endsection

@section('js')
<script src='/js/theme-switcher.js'></script>
@include('snippets.modals')
<script src='/js/datatables.min.js'></script>
<script src='/js/api.js'></script>
<script src='/js/AdminCtrl.js'></script>
<script src='/js/qrcode.min.js'></script>
@if ($pending_totp_otpauth)
<script>
(function () {
    var target = document.getElementById('totp-qrcode');
    if (!target) return;
    target.innerHTML = '';
    new QRCode(target, {
        text: {!! json_encode($pending_totp_otpauth) !!},
        width: 164,
        height: 164,
        correctLevel: QRCode.CorrectLevel.M
    });
})();
</script>
@endif
@endsection
