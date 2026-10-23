@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/about.css' />
@endsection

@section('content')
<div class="polrx-page-card about-contents">
    <div class="polrx-about-header">
        <img class='logo-img' src='/favicon.svg' alt='polrX Logo' style='width:96px;height:96px;border-radius:22px;box-shadow:0 8px 24px rgba(0,0,0,0.18);' />
        <div>
            <h2 style="margin: 0 0 6px;">{{env('APP_NAME')}}</h2>
            <p class="polrx-muted" style="margin: 0;">基於現代 Laravel 13 與 PHP 8.4 重構之輕量短網址服務</p>
        </div>
    </div>

    <hr class="polrx-divider">

    @if ($role == "admin")
    <div class="well polrx-subcard" style="margin-bottom: 24px;">
        <h4 style="margin-top: 0; margin-bottom: 10px;">系統資訊（管理員檢視）</h4>
        <dl style="margin-bottom: 10px;">
            <dt>版本： {{env('POLR_VERSION')}}</dt>
            <dt>發佈日期： {{env('POLR_RELDATE')}}</dt>
            <dt>安裝資訊： {{env('APP_NAME')}} / {{env('APP_ADDRESS')}} / {{env('POLR_GENERATED_AT')}}</dt>
        </dl>
        <p class="polrx-muted" style="margin-bottom: 0; font-size: 13px;">你目前以管理員身份登入，因此可以看到系統資訊。若要修改本頁內容，可編輯 <code>resources/views/about.blade.php</code></p>
    </div>
    @endif

    <p style="line-height: 1.7; margin-bottom: 0;">{{env('APP_NAME')}} 採用 <strong>polrX</strong>（基於 Polr 2 重構之 Laravel 13 短網址系統）架設。
        你可以前往 <a href='https://github.com/tommy1616/polrX' target='_blank' rel='noopener noreferrer'>GitHub 專案頁面（tommy1616/polrX）</a> 了解更多。
        <br />polrX 採用 GNU GPL v2+ 授權釋出。
    </p>
</div>
@endsection
