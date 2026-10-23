@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/themes.css'>
<link rel='stylesheet' href='/css/admin.css'>
<link rel='stylesheet' href='/css/datatables.min.css'>
@endsection

@section('content')
<div ng-controller="AdminCtrl" class="ng-root">
    <div class='col-md-2'>
        <ul class='nav nav-pills nav-stacked admin-nav' role='tablist'>
            <li role='presentation' aria-controls="home" class='admin-nav-item active'><a href='#home'>首頁</a></li>
            <li role='presentation' aria-controls="links" class='admin-nav-item'><a href='#links'>連結</a></li>
            <li role='presentation' aria-controls="settings" class='admin-nav-item'><a href='#settings'>設定</a></li>

            @if ($role == $admin_role)
            <li role='presentation' class='admin-nav-item'><a href='#admin'>管理</a></li>
            @endif

            @if ($api_active == 1)
            <li role='presentation' class='admin-nav-item'><a href='#developer'>開發者</a></li>
            @endif
        </ul>
    </div>
    <div class='col-md-10'>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <h2>歡迎使用 {{env('APP_NAME')}} 後台</h2>
                <p>請使用左側選單管理你的短網址、帳號與設定。</p>
            </div>

            <div role="tabpanel" class="tab-pane" id="links">
                @include('snippets.link_table', [
                    'table_id' => 'user_links_table'
                ])
            </div>

            <div role="tabpanel" class="tab-pane" id="settings">
                <h3>修改密碼</h3>
                <form action='/admin/action/change_password' method='POST'>
                    目前密碼： <input class="form-control password-box" type='password' name='current_password' />
                    新密碼： <input class="form-control password-box" type='password' name='new_password' />
                    <input type="hidden" name='_token' value='{{csrf_token()}}' />
                    <input type='submit' class='btn btn-success change-password-btn' value='更新密碼'/>
                </form>

                <hr style="margin: 36px 0 28px;">

                <div class="theme-selector">
                    <h3>介面主題</h3>
                    <p style="color:var(--polrx-text-muted, #64748b); margin-bottom: 12px;">選擇偏好的視覺主題，點擊後會立即預覽並綁定至你的帳號。</p>
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
                                <span class="polrx-theme-badge">使用中</span>
                            </div>
                            <div class="polrx-theme-desc">{{ $themeMeta['description'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <hr style="margin: 36px 0 28px;">

                <h3>登入兩步驟驗證（TOTP）</h3>
                @if ($totp_enabled)
                    <div class="well" style="background:#f7fbf3; border-color:#d8e7c2;">
                        <p style="margin-bottom: 8px;"><strong>目前狀態：</strong> 已啟用</p>
                        <p style="color:#666; margin-bottom: 18px;">之後登入時，輸入帳號密碼後還需要再輸入一次驗證器的 6 碼驗證碼。</p>
                        <form action='/admin/action/totp/disable' method='POST' style="max-width: 420px;">
                            <label style="display:block; margin-bottom: 8px;">目前密碼：</label>
                            <input class="form-control password-box" type='password' name='disable_totp_current_password' />
                            <input type="hidden" name='_token' value='{{csrf_token()}}' />
                            <input type='submit' class='btn btn-danger' style="margin-top: 14px;" value='停用 2FA'/>
                        </form>
                    </div>
                @elseif ($pending_totp_secret)
                    <div class="well">
                        <p style="margin-bottom: 12px;">使用 Google Authenticator、1Password、Authy 或其他支援 TOTP 的驗證器掃描下方 QR code。</p>
                        <div style="display:flex; flex-wrap:wrap; gap: 24px; align-items:flex-start; margin-bottom: 16px;">
                            <div>
                                <div id="totp-qrcode" style="width: 190px; min-height: 190px; padding: 12px; background: #fff; border: 1px solid #e4e4e4; border-radius: 10px;"></div>
                            </div>
                            <div style="flex:1; min-width: 260px;">
                                <p style="margin-bottom: 10px;"><strong>帳號標籤：</strong> {{$totp_label}}</p>
                                <p style="margin-bottom: 10px;"><strong>手動輸入金鑰：</strong></p>
                                <code style="display:block; padding: 12px 14px; background:#f6f8fa; border-radius: 8px; word-break: break-all;">{{$pending_totp_secret}}</code>
                            </div>
                        </div>

                        <form action='/admin/action/totp/confirm' method='POST' style="max-width: 460px;">
                            <label style="display:block; margin-bottom: 8px;">目前密碼：</label>
                            <input class="form-control password-box" type='password' name='totp_current_password' />
                            <label style="display:block; margin: 14px 0 8px;">驗證器上的 6 碼驗證碼：</label>
                            <input class="form-control password-box" type='text' inputmode='numeric' maxlength='6' name='totp_code' placeholder='123456' />
                            <input type="hidden" name='_token' value='{{csrf_token()}}' />
                            <div style="margin-top: 14px; display:flex; gap:10px; flex-wrap:wrap;">
                                <input type='submit' class='btn btn-success' value='確認啟用 2FA'/>
                            </div>
                        </form>

                        <form action='/admin/action/totp/cancel' method='POST' style="margin-top: 10px;">
                            <input type="hidden" name='_token' value='{{csrf_token()}}' />
                            <input type='submit' class='btn btn-default' value='取消綁定'/>
                        </form>
                    </div>
                @else
                    <div class="well">
                        <p style="margin-bottom: 10px;">啟用後，登入時除了帳號密碼，還需要再輸入驗證器 App 的 6 碼 TOTP 驗證碼。</p>
                        <form action='/admin/action/totp/start' method='POST'>
                            <input type="hidden" name='_token' value='{{csrf_token()}}' />
                            <input type='submit' class='btn btn-primary' value='開始綁定 2FA'/>
                        </form>
                    </div>
                @endif
            </div>

            @if ($role == $admin_role)
            <div role="tabpanel" class="tab-pane" id="admin">
                <h3>所有連結</h3>
                @include('snippets.link_table', [
                    'table_id' => 'admin_links_table'
                ])

                <h3 class="users-heading">使用者</h3>
                <a ng-click="state.showNewUserWell = !state.showNewUserWell" class="btn btn-primary btn-sm status-display">新增</a>

                <div ng-if="state.showNewUserWell" class="new-user-fields well">
                    <table class="table">
                        <tr>
                            <th>帳號</th>
                            <th>密碼</th>
                            <th>Email</th>
                            <th>角色</th>
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
                                <a ng-click="addNewUser($event)" class="btn btn-primary btn-sm status-display new-user-add">新增</a>
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
                <h3>開發者</h3>

                <p>這裡提供 API 金鑰與開發文件。</p>
                <p>
                    專案與文件：
                    <a href='https://github.com/tommy1616/polrX' target='_blank' rel='noopener noreferrer'>https://github.com/tommy1616/polrX</a>
                </p>

                <h4>API 金鑰：</h4>
                <div class='row'>
                    <div class='col-md-8'>
                        <input class='form-control status-display' disabled type='text' value='{{$api_key}}'>
                    </div>
                    <div class='col-md-4'>
                        <a href='#' ng-click="generateNewAPIKey($event, '{{$user_id}}', true)" id='api-reset-key' class='btn btn-danger'>重設</a>
                    </div>
                </div>

                <h4>API 額度：</h4>
                <h2 class='api-quota'>
                    @if ($api_quota == -1)
                        無限制
                    @else
                        <code>{{$api_quota}}</code>
                    @endif
                </h2>
                <span>每分鐘請求次數</span>
            </div>
            @endif
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
