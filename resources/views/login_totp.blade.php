@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/login.css' />
@endsection

@section('content')
<div class="center-text">
    <h1>兩步驟驗證</h1><br/><br/>
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <p style="margin-bottom: 16px; color: #666;">帳號 <strong>{{$pending_username}}</strong> 已啟用 2FA，請輸入驗證器上的 6 碼驗證碼。</p>
        <form action="/login/totp" method="POST">
            <input type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="6 碼驗證碼" name="totp_code" class="form-control login-field" />
            <input type="hidden" name='_token' value='{{csrf_token()}}' />
            <input type="submit" value="驗證並登入" class="login-submit btn btn-success" />
        </form>
    </div>
    <div class="col-md-3"></div>
</div>
@endsection
