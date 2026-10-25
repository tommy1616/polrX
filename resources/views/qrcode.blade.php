@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/shorten_result.css' />
<style>
.qr-studio-card {
    max-width: 940px;
}

.qr-studio-grid {
    display: grid;
    grid-template-columns: 1.25fr 0.95fr;
    gap: 28px;
    align-items: start;
    margin-top: 22px;
    text-align: left;
}

@media (max-width: 820px) {
    .qr-studio-grid {
        grid-template-columns: 1fr;
    }
}

.qr-type-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 20px;
}

.qr-type-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 14px;
    border-radius: 10px;
    border: 1px solid var(--polrx-border);
    background-color: var(--polrx-surface-alt);
    color: var(--polrx-text-muted);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}

.qr-type-btn:hover {
    color: var(--polrx-heading);
    border-color: var(--polrx-primary);
}

.qr-type-btn.is-active {
    background-color: var(--polrx-primary);
    border-color: var(--polrx-primary);
    color: #ffffff;
    box-shadow: 0 4px 12px var(--polrx-primary-soft);
}

.qr-form-pane {
    display: none;
}

.qr-form-pane.is-active {
    display: block;
}

.qr-form-group {
    margin-bottom: 14px;
}

.qr-preview-box {
    background-color: var(--polrx-surface-alt);
    border: 1px solid var(--polrx-border);
    border-radius: 14px;
    padding: 22px 20px;
    text-align: center;
}

.qr-export-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 16px;
}
</style>
@endsection

@section('content')
<div class="polrx-page-card qr-studio-card">
    <h2 style="margin-top: 0; margin-bottom: 8px;">{{ __('polr.qrcode_page.title') }}</h2>
    <p class="polrx-muted" style="margin-bottom: 0;">{{ __('polr.qrcode_page.subtitle') }}</p>

    <div class="qr-studio-grid">
        <div>
            <div class="qr-type-tabs" role="tablist">
                <button type="button" class="qr-type-btn is-active" data-qr-type="url">
                    <i class="fa fa-link"></i> {{ __('polr.qrcode_page.type_url') }}
                </button>
                <button type="button" class="qr-type-btn" data-qr-type="text">
                    <i class="fa fa-align-left"></i> {{ __('polr.qrcode_page.type_text') }}
                </button>
                <button type="button" class="qr-type-btn" data-qr-type="wifi">
                    <i class="fa fa-wifi"></i> {{ __('polr.qrcode_page.type_wifi') }}
                </button>
                <button type="button" class="qr-type-btn" data-qr-type="email">
                    <i class="fa fa-envelope-o"></i> {{ __('polr.qrcode_page.type_email') }}
                </button>
                <button type="button" class="qr-type-btn" data-qr-type="sms">
                    <i class="fa fa-phone"></i> {{ __('polr.qrcode_page.type_sms') }}
                </button>
            </div>

            <!-- 1. URL -->
            <div class="qr-form-pane is-active" data-pane="url">
                <div class="qr-form-group">
                    <label class="polrx-field-label">{{ __('polr.qrcode_page.url_label') }}</label>
                    <input type="text" id="qr-input-url" class="form-control" value="{{ env('APP_PROTOCOL') . env('APP_ADDRESS') }}" placeholder="{{ __('polr.qrcode_page.url_placeholder') }}">
                </div>
            </div>

            <!-- 2. Plain Text -->
            <div class="qr-form-pane" data-pane="text">
                <div class="qr-form-group">
                    <label class="polrx-field-label">{{ __('polr.qrcode_page.text_label') }}</label>
                    <textarea id="qr-input-text" class="form-control" rows="4" placeholder="{{ __('polr.qrcode_page.text_placeholder') }}"></textarea>
                </div>
            </div>

            <!-- 3. Wi-Fi -->
            <div class="qr-form-pane" data-pane="wifi">
                <div class="qr-form-group">
                    <label class="polrx-field-label">{{ __('polr.qrcode_page.wifi_ssid') }}</label>
                    <input type="text" id="qr-wifi-ssid" class="form-control" placeholder="{{ __('polr.qrcode_page.wifi_ssid_placeholder') }}" autocomplete="off">
                </div>
                <div class="qr-form-group">
                    <label class="polrx-field-label">{{ __('polr.qrcode_page.wifi_encryption') }}</label>
                    <select id="qr-wifi-enc" class="form-control">
                        <option value="WPA">{{ __('polr.qrcode_page.wifi_enc_wpa') }}</option>
                        <option value="WEP">{{ __('polr.qrcode_page.wifi_enc_wep') }}</option>
                        <option value="nopass">{{ __('polr.qrcode_page.wifi_enc_nopass') }}</option>
                    </select>
                </div>
                <div class="qr-form-group" id="qr-wifi-pass-group">
                    <label class="polrx-field-label">{{ __('polr.qrcode_page.wifi_password') }}</label>
                    <input type="text" id="qr-wifi-pass" class="form-control" placeholder="{{ __('polr.qrcode_page.wifi_password_placeholder') }}" autocomplete="off">
                </div>
                <div class="checkbox" style="margin-top: 8px;">
                    <label>
                        <input type="checkbox" id="qr-wifi-hidden"> {{ __('polr.qrcode_page.wifi_hidden') }}
                    </label>
                </div>
            </div>

            <!-- 4. Email -->
            <div class="qr-form-pane" data-pane="email">
                <div class="qr-form-group">
                    <label class="polrx-field-label">{{ __('polr.qrcode_page.email_to') }}</label>
                    <input type="text" id="qr-email-to" class="form-control" placeholder="{{ __('polr.qrcode_page.email_to_placeholder') }}">
                </div>
                <div class="qr-form-group">
                    <label class="polrx-field-label">{{ __('polr.qrcode_page.email_subject') }}</label>
                    <input type="text" id="qr-email-subject" class="form-control" placeholder="{{ __('polr.qrcode_page.email_subject_placeholder') }}">
                </div>
                <div class="qr-form-group">
                    <label class="polrx-field-label">{{ __('polr.qrcode_page.email_body') }}</label>
                    <textarea id="qr-email-body" class="form-control" rows="3" placeholder="{{ __('polr.qrcode_page.email_body_placeholder') }}"></textarea>
                </div>
            </div>

            <!-- 5. Phone / SMS -->
            <div class="qr-form-pane" data-pane="sms">
                <div class="qr-form-group">
                    <label class="polrx-field-label">{{ __('polr.qrcode_page.sms_mode') }}</label>
                    <select id="qr-sms-mode" class="form-control">
                        <option value="tel">{{ __('polr.qrcode_page.sms_mode_call') }}</option>
                        <option value="sms">{{ __('polr.qrcode_page.sms_mode_sms') }}</option>
                    </select>
                </div>
                <div class="qr-form-group">
                    <label class="polrx-field-label">{{ __('polr.qrcode_page.sms_phone') }}</label>
                    <input type="text" id="qr-sms-phone" class="form-control" placeholder="{{ __('polr.qrcode_page.sms_phone_placeholder') }}">
                </div>
                <div class="qr-form-group" id="qr-sms-body-group" style="display: none;">
                    <label class="polrx-field-label">{{ __('polr.qrcode_page.sms_body') }}</label>
                    <textarea id="qr-sms-body" class="form-control" rows="3" placeholder="{{ __('polr.qrcode_page.sms_body_placeholder') }}"></textarea>
                </div>
            </div>

            <div style="margin-top: 18px;">
                <button type="button" id="qr-studio-generate" class="btn btn-primary">
                    <i class="fa fa-qrcode" style="margin-right: 6px;"></i>{{ __('polr.qrcode_page.generate_btn') }}
                </button>
            </div>
        </div>

        <!-- Right column: Live Custom SVG QR Preview -->
        <div class="qr-preview-box">
            <h4 style="margin-top: 0; margin-bottom: 14px;">{{ __('polr.qrcode_page.preview_title') }}</h4>

            <div class="qr-style-switcher" role="group" aria-label="QR Code Style">
                <button type="button" class="qr-style-pill is-active" data-qr-style="branded">{{ __('polr.shorten_result.qr_style_branded') }}</button>
                <button type="button" class="qr-style-pill" data-qr-style="rounded">{{ __('polr.shorten_result.qr_style_rounded') }}</button>
                <button type="button" class="qr-style-pill" data-qr-style="classic">{{ __('polr.shorten_result.qr_style_classic') }}</button>
            </div>

            <div class="qr-code-container" id="polrx-qr-studio-stage"></div>

            <div class="qr-export-actions">
                <button type="button" id="qr-studio-download-png" class="btn btn-primary btn-sm">
                    <i class="fa fa-download" style="margin-right: 5px;"></i>{{ __('polr.qrcode_page.download_png') }}
                </button>
                <button type="button" id="qr-studio-download-svg" class="btn btn-default btn-sm">
                    <i class="fa fa-code" style="margin-right: 5px;"></i>{{ __('polr.qrcode_page.download_svg') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src='/js/qrcode.min.js'></script>
<script src='/js/qrcode_studio.js'></script>
@endsection
