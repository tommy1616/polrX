@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/about.css' />
<link rel='stylesheet' href='/css/effects.css' />
@endsection

@section('content')
<div class='well logo-well'>
    <img class='logo-img' src='/img/logo.png' />
</div>

<div class='about-contents'>
    @if ($role == "admin")
    <dl>
        <p>系統資訊</p>
        <dt>版本： {{env('POLR_VERSION')}}</dt>
        <dt>發佈日期： {{env('POLR_RELDATE')}}</dt>
        <dt>安裝資訊： {{env('APP_NAME')}} / {{env('APP_ADDRESS')}} / {{env('POLR_GENERATED_AT')}}<dt>
    </dl>
    <p>你目前以管理員身份登入，因此可以看到系統資訊。若要修改本頁內容，可編輯 <code>resources/views/about.blade.php</code></p>
    @endif

    <p>{{env('APP_NAME')}} 採用 polrX（基於 Polr 2 重構之 Laravel 13 短網址系統）架設。
        你可以前往 <a href='https://github.com/tommy1616/polrX' target='_blank' rel='noopener noreferrer'>GitHub 專案頁面（tommy1616/polrX）</a> 了解更多。
        <br />polrX 採用 GNU GPL v2+ 授權釋出。
    </p>
</div>

@endsection
