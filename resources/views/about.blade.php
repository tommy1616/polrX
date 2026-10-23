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

    <p>{{env('APP_NAME')}} 採用 Polr 2 架設，這是一套開源、極簡風格的短網址系統。Polr Project 與本站沒有直接關聯。
        你可以前往 <a href='https://github.com/Cydrobolt/polr'>GitHub 頁面</a> 或 <a href="//project.polr.me">專案網站</a> 了解更多。
        <br />Polr 採用 GNU GPL 授權。
    </p>
</div>

@endsection
