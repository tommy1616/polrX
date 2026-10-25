@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/shorten_result.css' />
@endsection

@section('content')
<div class="polrx-page-card" style="max-width: 640px; margin: 0 auto; text-align: center;">
    <h3 style="margin-top: 0; margin-bottom: 20px;">{{ __('polr.shorten_result.title') }}</h3>
    <div class="input-group shorten-result-group">
        <input type='text' class='result-box form-control' value='{{$short_url}}' id='short_url' readonly />
        <div class='input-group-addon' id='clipboard-copy' data-clipboard-target='#short_url' data-toggle='tooltip' data-placement='bottom' data-title='{{ __('polr.shorten_result.copied') }}'>
            <i class='fa fa-clipboard' aria-hidden='true' title='{{ __('polr.shorten_result.copy_tooltip') }}'></i>
        </div>
    </div>
    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
        <a id="generate-qr-code" class='btn btn-default'>{{ __('polr.shorten_result.generate_qr') }}</a>
        <a href='{{route('index')}}' class='btn btn-primary'>{{ __('polr.shorten_result.shorten_another') }}</a>
    </div>

    <div class="qr-code-panel" id="qr-code-panel" style="display: none;">
        <div class="qr-style-switcher" role="group" aria-label="QR Code Style">
            <button type="button" class="qr-style-pill is-active" data-qr-style="branded">{{ __('polr.shorten_result.qr_style_branded') }}</button>
            <button type="button" class="qr-style-pill" data-qr-style="rounded">{{ __('polr.shorten_result.qr_style_rounded') }}</button>
            <button type="button" class="qr-style-pill" data-qr-style="classic">{{ __('polr.shorten_result.qr_style_classic') }}</button>
        </div>

        <div class="qr-code-container" id="polrx-qr-stage"></div>

        <div style="margin-top: 14px;">
            <button type="button" id="download-qr-code" class="btn btn-default btn-sm">
                <i class="fa fa-download" style="margin-right: 6px;"></i>{{ __('polr.shorten_result.download_qr') }}
            </button>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src='/js/qrcode.min.js'></script>
<script src='/js/clipboard.min.js'></script>
<script src='/js/shorten_result.js'></script>
@endsection
