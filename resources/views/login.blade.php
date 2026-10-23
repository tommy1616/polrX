@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='css/login.css' />
@endsection

@section('content')
<div class="center-text">
    <h1>登入</h1><br/><br/>
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <form action="login" method="POST">
            <input type="text" placeholder="帳號" name="username" class="form-control login-field" />
            <input type="password" placeholder="密碼" name="password" class="form-control login-field" />
            <input type="hidden" name='_token' value='{{csrf_token()}}' />
            <input type="submit" value="登入" class="login-submit btn btn-success" />

            <p class='login-prompts'>
            @if (env('POLR_ALLOW_ACCT_CREATION') == true)
                <small>還沒有帳號？ <a href='{{route('signup')}}'>註冊</a></small>
            @endif

            @if (env('SETTING_PASSWORD_RECOV') == true)
                <small>忘記密碼？ <a href='{{route('lost_password')}}'>重設</a></small>
            @endif
            </p>
        </form>
    </div>
    <div class="col-md-3"></div>
</div
@endsection
